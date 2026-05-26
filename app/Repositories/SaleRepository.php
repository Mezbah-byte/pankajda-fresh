<?php

namespace App\Repositories;

use App\Models\SaleItemModel;
use App\Models\SaleModel;
use App\Models\SalePaymentModel;
use Config\Database;

class SaleRepository extends BaseRepository
{
    private SaleItemModel $items;
    private SalePaymentModel $payments;

    protected function bootModel(): void
    {
        $this->model    = new SaleModel();
        $this->items    = new SaleItemModel();
        $this->payments = new SalePaymentModel();
    }

    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $builder->like('invoice_no', $filters['q']);
            }
            if (! empty($filters['customer_un_id'])) {
                $builder->where('customer_un_id', $filters['customer_un_id']);
            }
            if (! empty($filters['sale_type'])) {
                $builder->where('sale_type', $filters['sale_type']);
            }
            if (! empty($filters['payment_status'])) {
                $builder->where('payment_status', $filters['payment_status']);
            }
            if (! empty($filters['date_from'])) {
                $builder->where('sale_date >=', $filters['date_from']);
            }
            if (! empty($filters['date_to'])) {
                $builder->where('sale_date <=', $filters['date_to']);
            }
        });
    }

    public function nextInvoiceNo(): string
    {
        $db = Database::connect();
        $row = $db->table('sales')->select('invoice_no')
            ->orderBy('id', 'DESC')->limit(1)->get()->getRowArray();

        $year = date('Y');
        if ($row && preg_match('/INV-' . $year . '-(\d+)/', $row['invoice_no'], $m)) {
            $next = (int) $m[1] + 1;
        } else {
            $next = 1001;
        }
        return sprintf('INV-%s-%05d', $year, $next);
    }

    public function insertItems(string $saleUnId, array $items): void
    {
        foreach ($items as $it) {
            $it['sale_un_id'] = $saleUnId;
            $this->items->insert($it);
        }
    }

    public function itemsFor(string $saleUnId): array
    {
        return $this->items->where('sale_un_id', $saleUnId)
            ->where('deleted_at', null)
            ->findAll();
    }

    public function paymentsFor(string $saleUnId): array
    {
        return $this->payments->where('sale_un_id', $saleUnId)
            ->where('deleted_at', null)
            ->orderBy('payment_date', 'DESC')
            ->findAll();
    }

    public function recordPayment(array $payment): string
    {
        $id = $this->payments->insert($payment, true);
        if ($id === false) {
            throw new \RuntimeException('Failed to record sale payment.');
        }
        return $this->payments->find($id)['un_id'];
    }

    public function totals(): array
    {
        $db = Database::connect();
        $totalRow = $db->table('sales')->selectSum('total_amount', 'total')
            ->selectSum('paid_amount', 'paid')->selectSum('due_amount', 'due')
            ->where('deleted_at', null)->get()->getRowArray();
        $count = $this->model->where('deleted_at', null)->countAllResults();
        return [
            'count' => $count,
            'total' => (float) ($totalRow['total'] ?? 0),
            'paid'  => (float) ($totalRow['paid'] ?? 0),
            'due'   => (float) ($totalRow['due'] ?? 0),
        ];
    }
}
