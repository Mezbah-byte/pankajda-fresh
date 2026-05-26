<?php

namespace App\Services;

use App\Repositories\CustomerRepository;
use App\Repositories\SaleRepository;

/**
 * SaleService - invoice creation, payment recording, due tracking.
 *
 * Maintains the integrity of: sale.total_amount, sale.paid_amount,
 * sale.due_amount, sale.payment_status, AND customer.current_due.
 * Every change goes through a transaction so partial failures can't
 * leave totals out of sync.
 */
class SaleService extends BaseService
{
    private SaleRepository $sales;
    private CustomerRepository $customers;

    public function __construct(?SaleRepository $sales = null, ?CustomerRepository $customers = null)
    {
        $this->sales     = $sales     ?? new SaleRepository();
        $this->customers = $customers ?? new CustomerRepository();
    }

    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->sales->search($filters, $page, $perPage);
    }

    public function get(string $unId): ?array
    {
        $sale = $this->sales->findByUnId($unId);
        if (! $sale) return null;
        $sale['items']    = $this->sales->itemsFor($unId);
        $sale['payments'] = $this->sales->paymentsFor($unId);
        return $sale;
    }

    public function nextInvoiceNo(): string
    {
        return $this->sales->nextInvoiceNo();
    }

    public function totals(): array
    {
        return $this->sales->totals();
    }

    /**
     * Create a sale + line items + (optional) initial payment.
     *
     * Required input:
     *  customer_un_id, sale_type, items[ {product_name, quantity, unit, unit_price} ]
     * Optional:
     *  company_un_id, container_un_id, sale_date, discount, tax,
     *  paid_amount (initial payment), notes
     */
    public function create(array $input): array
    {
        if (empty($input['customer_un_id'])) {
            throw new \InvalidArgumentException('customer_un_id is required.');
        }
        if (empty($input['items']) || ! is_array($input['items'])) {
            throw new \InvalidArgumentException('At least one line item is required.');
        }

        $type = $input['sale_type'] ?? SALE_CASH;
        if (! in_array($type, [SALE_CASH, SALE_CREDIT], true)) {
            throw new \InvalidArgumentException('sale_type must be cash or credit.');
        }

        // Compute line items + subtotal
        $subtotal = 0.0;
        $items = [];
        foreach ($input['items'] as $row) {
            $qty   = (float) ($row['quantity'] ?? 0);
            $price = (float) ($row['unit_price'] ?? 0);
            $total = round($qty * $price, 2);
            if ($qty <= 0 || $price < 0) {
                throw new \InvalidArgumentException('Invalid line item quantity/price.');
            }
            $items[] = [
                'product_name' => $row['product_name'] ?? 'Item',
                'quantity'     => $qty,
                'unit'         => $row['unit'] ?? 'kg',
                'unit_price'   => $price,
                'total'        => $total,
            ];
            $subtotal += $total;
        }

        $discount = (float) ($input['discount'] ?? 0);
        $tax      = (float) ($input['tax'] ?? 0);
        $total    = round(max(0, $subtotal - $discount + $tax), 2);

        // For cash sales, paid defaults to total. For credit, paid defaults to whatever was provided.
        $paid = (float) ($input['paid_amount'] ?? ($type === SALE_CASH ? $total : 0));
        $paid = max(0, min($paid, $total));
        $due  = max(0, $total - $paid);
        $status = $this->statusFor($paid, $due);

        return $this->transaction(function () use ($input, $items, $subtotal, $discount, $tax, $total, $paid, $due, $status, $type) {
            $invoiceNo = $this->sales->nextInvoiceNo();

            $saleUnId = $this->sales->create([
                'invoice_no'      => $invoiceNo,
                'company_un_id'   => $input['company_un_id'] ?? null,
                'customer_un_id'  => $input['customer_un_id'],
                'container_un_id' => $input['container_un_id'] ?? null,
                'sale_type'       => $type,
                'sale_date'       => $input['sale_date'] ?? date('Y-m-d'),
                'subtotal'        => $subtotal,
                'discount'        => $discount,
                'tax'             => $tax,
                'total_amount'    => $total,
                'paid_amount'     => $paid,
                'due_amount'      => $due,
                'payment_status'  => $status,
                'notes'           => $input['notes'] ?? null,
                'created_by_un_id'=> session('user_un_id'),
            ]);

            $this->sales->insertItems($saleUnId, $items);

            // Record initial payment if any
            if ($paid > 0) {
                $this->sales->recordPayment([
                    'sale_un_id'      => $saleUnId,
                    'customer_un_id'  => $input['customer_un_id'],
                    'amount'          => $paid,
                    'payment_method'  => $input['payment_method'] ?? 'cash',
                    'reference_no'    => $input['reference_no'] ?? null,
                    'payment_date'    => $input['sale_date'] ?? date('Y-m-d'),
                    'created_by_un_id'=> session('user_un_id'),
                ]);
            }

            // Adjust customer due: increases by total, decreases by paid (net = due)
            $this->customers->adjustDue($input['customer_un_id'], $due);

            $this->audit('sale.created', 'sale', $saleUnId, [
                'invoice_no' => $invoiceNo, 'total' => $total, 'due' => $due,
            ]);

            return $this->get($saleUnId);
        });
    }

    /**
     * Record a payment against an existing credit sale.
     * Updates the sale's paid/due/status AND the customer's running due.
     */
    public function addPayment(string $saleUnId, array $payload): array
    {
        $sale = $this->sales->findByUnId($saleUnId);
        if (! $sale) throw new \InvalidArgumentException('Sale not found.');

        $amount = (float) ($payload['amount'] ?? 0);
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Payment amount must be greater than zero.');
        }
        $newPaid = (float) $sale['paid_amount'] + $amount;
        if (round($newPaid, 2) > round((float) $sale['total_amount'], 2)) {
            throw new \InvalidArgumentException('Payment exceeds outstanding due.');
        }
        $newDue = max(0, (float) $sale['total_amount'] - $newPaid);

        return $this->transaction(function () use ($saleUnId, $sale, $amount, $payload, $newPaid, $newDue) {
            $paymentUnId = $this->sales->recordPayment([
                'sale_un_id'      => $saleUnId,
                'customer_un_id'  => $sale['customer_un_id'],
                'amount'          => $amount,
                'payment_method'  => $payload['payment_method'] ?? 'cash',
                'reference_no'    => $payload['reference_no'] ?? null,
                'payment_date'    => $payload['payment_date'] ?? date('Y-m-d'),
                'notes'           => $payload['notes'] ?? null,
                'created_by_un_id'=> session('user_un_id'),
            ]);

            $this->sales->updateByUnId($saleUnId, [
                'paid_amount'    => $newPaid,
                'due_amount'     => $newDue,
                'payment_status' => $this->statusFor($newPaid, $newDue),
            ]);

            // Reduce customer's running due by the payment amount
            if ($sale['customer_un_id']) {
                $this->customers->adjustDue($sale['customer_un_id'], -$amount);
            }

            $this->audit('sale.payment_added', 'sale', $saleUnId, [
                'amount' => $amount, 'payment_un_id' => $paymentUnId,
            ]);
            return $this->get($saleUnId);
        });
    }

    public function delete(string $unId): void
    {
        $sale = $this->sales->findByUnId($unId);
        if (! $sale) throw new \InvalidArgumentException('Sale not found.');
        $this->transaction(function () use ($unId, $sale) {
            $this->sales->deleteByUnId($unId);
            // Roll back the customer's due by the outstanding amount
            if ($sale['customer_un_id'] && (float) $sale['due_amount'] > 0) {
                $this->customers->adjustDue($sale['customer_un_id'], -(float) $sale['due_amount']);
            }
            $this->audit('sale.deleted', 'sale', $unId);
        });
    }

    private function statusFor(float $paid, float $due): string
    {
        if ($due <= 0.001 && $paid > 0)  return PAYMENT_PAID;
        if ($paid > 0)                    return PAYMENT_PARTIAL;
        return PAYMENT_DUE;
    }
}
