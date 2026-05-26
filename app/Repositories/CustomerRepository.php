<?php

namespace App\Repositories;

use App\Models\CustomerModel;
use Config\Database;

class CustomerRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new CustomerModel();
    }

    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $q = $filters['q'];
                $builder->groupStart()
                    ->like('customer_name', $q)
                    ->orLike('phone', $q)
                    ->orLike('email', $q)
                    ->orLike('customer_code', $q)
                    ->groupEnd();
            }
            if (! empty($filters['company_un_id'])) {
                $builder->where('company_un_id', $filters['company_un_id']);
            }
            if (! empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
            if (isset($filters['has_due']) && $filters['has_due']) {
                $builder->where('current_due >', 0);
            }
        });
    }

    /**
     * Adjust a customer's running due balance.
     *
     * @param float $delta positive = increase due (new credit sale),
     *                     negative = decrease due (payment received)
     */
    public function adjustDue(string $customerUnId, float $delta): void
    {
        $db = Database::connect();
        $db->table('customers')
            ->where('un_id', $customerUnId)
            ->set('current_due', 'current_due + ' . (float) $delta, false)
            ->update();
    }

    public function totals(): array
    {
        $db = Database::connect();
        $total = $this->model->where('deleted_at', null)->countAllResults(false);
        $row = $db->table('customers')->selectSum('current_due', 'total')
            ->where('deleted_at', null)->get()->getRowArray();
        return [
            'total'       => $total,
            'total_due'   => (float) ($row['total'] ?? 0),
        ];
    }
}
