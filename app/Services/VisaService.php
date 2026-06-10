<?php

namespace App\Services;

use App\Repositories\VisaRepository;

/**
 * VisaService - business logic for visa management.
 *
 * The service owns the relationship between cost / paid_amount /
 * due_amount / payment_status. Whenever a payment is recorded, we
 * recompute paid + due + status atomically inside a transaction.
 */
class VisaService extends BaseService
{
    private VisaRepository $visas;

    public function __construct(?VisaRepository $visas = null)
    {
        $this->visas = $visas ?? new VisaRepository();
    }

    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->visas->search($filters, $page, $perPage);
    }

    public function get(string $unId): ?array
    {
        return $this->visas->findByUnId($unId);
    }

    public function extraCostsFor(string $visaUnId): array
    {
        return $this->visas->extraCostsFor($visaUnId);
    }

    public function addExtraCost(string $visaUnId, array $payload): void
    {
        $visa = $this->visas->findByUnId($visaUnId);
        if (! $visa) throw new \InvalidArgumentException('Visa not found.');

        $amount = (float) ($payload['amount'] ?? 0);
        if ($amount <= 0) throw new \InvalidArgumentException('Amount must be greater than zero.');

        $this->transaction(function () use ($visaUnId, $payload, $visa, $amount) {
            $this->visas->addExtraCost($visaUnId, [
                'description' => $payload['description'] ?? 'Extra Cost',
                'amount'      => $amount,
            ]);
            $extraTotal = $this->visas->sumExtraCosts($visaUnId);
            $profit = (float) $visa['selling_price'] - (float) $visa['purchase_price'] - $extraTotal;
            $this->visas->updateByUnId($visaUnId, [
                'extra_costs' => $extraTotal,
                'profit'      => $profit,
            ]);
        });
        $this->audit('visa.extra_cost_added', 'visa', $visaUnId, ['amount' => $amount]);
    }

    public function deleteExtraCost(string $visaUnId, string $costUnId): void
    {
        $visa = $this->visas->findByUnId($visaUnId);
        if (! $visa) throw new \InvalidArgumentException('Visa not found.');

        $this->transaction(function () use ($visaUnId, $costUnId, $visa) {
            $this->visas->deleteExtraCost($costUnId);
            $extraTotal = $this->visas->sumExtraCosts($visaUnId);
            $profit = (float) $visa['selling_price'] - (float) $visa['purchase_price'] - $extraTotal;
            $this->visas->updateByUnId($visaUnId, [
                'extra_costs' => $extraTotal,
                'profit'      => $profit,
            ]);
        });
    }

    public function create(array $input): array
    {
        $data = $this->normalize($input);
        $data['paid_amount']    = (float) ($data['paid_amount'] ?? 0);
        $data['visa_cost']      = (float) ($data['visa_cost'] ?? 0);
        $data['selling_price']  = (float) ($data['selling_price'] ?? $data['visa_cost']);
        $data['purchase_price'] = (float) ($data['purchase_price'] ?? 0);
        $data['extra_costs']    = (float) ($data['extra_costs'] ?? 0);
        $data['profit']         = $data['selling_price'] - $data['purchase_price'] - $data['extra_costs'];
        $data['due_amount']     = max(0, $data['visa_cost'] - $data['paid_amount']);
        $data['payment_status'] = $this->statusFor($data['paid_amount'], $data['due_amount']);

        $unId = $this->transaction(fn () => $this->visas->create($data));
        $this->audit('visa.created', 'visa', $unId, ['name' => $data['visa_name']]);
        return $this->visas->findByUnId($unId);
    }

    public function update(string $unId, array $input): array
    {
        $existing = $this->visas->findByUnId($unId);
        if (! $existing) {
            throw new \InvalidArgumentException('Visa not found.');
        }
        $data = $this->normalize($input);

        // Recompute due/status if cost or paid changed
        $cost    = (float) ($data['visa_cost']      ?? $existing['visa_cost']);
        $paid    = (float) ($data['paid_amount']    ?? $existing['paid_amount']);
        $sell    = (float) ($data['selling_price']  ?? $existing['selling_price'] ?? $cost);
        $buy     = (float) ($data['purchase_price'] ?? $existing['purchase_price'] ?? 0);
        $extra   = (float) ($data['extra_costs']    ?? $existing['extra_costs'] ?? 0);
        $data['visa_cost']      = $cost;
        $data['selling_price']  = $sell;
        $data['purchase_price'] = $buy;
        $data['extra_costs']    = $extra;
        $data['profit']         = $sell - $buy - $extra;
        $data['paid_amount']    = $paid;
        $data['due_amount']     = max(0, $cost - $paid);
        $data['payment_status'] = $this->statusFor($paid, $data['due_amount']);

        $this->transaction(fn () => $this->visas->updateByUnId($unId, $data));
        $this->audit('visa.updated', 'visa', $unId);
        return $this->visas->findByUnId($unId);
    }

    public function delete(string $unId): void
    {
        if (! $this->visas->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Visa not found.');
        }
        $this->transaction(fn () => $this->visas->deleteByUnId($unId));
        $this->audit('visa.deleted', 'visa', $unId);
    }

    /**
     * Record a payment against a visa. Atomically:
     *   - inserts visa_payments row
     *   - recomputes paid_amount, due_amount, payment_status
     */
    public function addPayment(string $visaUnId, array $payload): array
    {
        $visa = $this->visas->findByUnId($visaUnId);
        if (! $visa) {
            throw new \InvalidArgumentException('Visa not found.');
        }
        $amount = (float) ($payload['amount'] ?? 0);
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Payment amount must be greater than zero.');
        }
        $cost = (float) $visa['visa_cost'];
        $alreadyPaid = (float) $visa['paid_amount'];
        if (round($alreadyPaid + $amount, 2) > round($cost, 2)) {
            throw new \InvalidArgumentException('Payment exceeds outstanding due.');
        }

        return $this->transaction(function () use ($visaUnId, $amount, $payload, $visa, $cost, $alreadyPaid) {
            $paymentUnId = $this->visas->recordPayment($visaUnId, [
                'amount'          => $amount,
                'payment_method'  => $payload['payment_method'] ?? 'cash',
                'reference_no'    => $payload['reference_no'] ?? null,
                'payment_date'    => $payload['payment_date'] ?? date('Y-m-d'),
                'notes'           => $payload['notes'] ?? null,
                'created_by_un_id'=> session('user_un_id'),
            ]);

            $newPaid = $alreadyPaid + $amount;
            $newDue  = max(0, $cost - $newPaid);
            $this->visas->updateByUnId($visaUnId, [
                'paid_amount'    => $newPaid,
                'due_amount'     => $newDue,
                'payment_status' => $this->statusFor($newPaid, $newDue),
            ]);
            $this->audit('visa.payment_added', 'visa', $visaUnId, [
                'amount' => $amount, 'payment_un_id' => $paymentUnId,
            ]);
            return [
                'visa'    => $this->visas->findByUnId($visaUnId),
                'payment' => ['un_id' => $paymentUnId, 'amount' => $amount],
            ];
        });
    }

    public function paymentsFor(string $visaUnId): array
    {
        return $this->visas->paymentsFor($visaUnId);
    }

    public function totals(): array
    {
        return $this->visas->totals();
    }

    private function statusFor(float $paid, float $due): string
    {
        if ($due <= 0.001 && $paid > 0)  return PAYMENT_PAID;
        if ($paid > 0)                    return PAYMENT_PARTIAL;
        return PAYMENT_DUE;
    }

    private function normalize(array $input): array
    {
        $whitelisted = [
            'company_un_id', 'visa_name', 'visa_number', 'country', 'from_country', 'category',
            'beneficiary_name', 'passport_no', 'visa_cost', 'paid_amount',
            'visa_issue_date', 'visa_expiry_date',
            'work_permit_number', 'work_permit_issue_date', 'work_permit_expiry_date',
            'purchase_price', 'selling_price', 'extra_costs',
            'status', 'notes',
        ];
        return array_intersect_key($input, array_flip($whitelisted));
    }
}
