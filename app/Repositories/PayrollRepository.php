<?php

namespace App\Repositories;

use App\Models\PayrollModel;

class PayrollRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new PayrollModel();
    }

    /**
     * Paginated search with optional filters.
     *
     * @param array $filters  Supports: employee_un_id, pay_period, status, company_un_id
     */
    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['employee_un_id'])) {
                $builder->where('employee_un_id', $filters['employee_un_id']);
            }
            if (! empty($filters['pay_period'])) {
                $builder->where('pay_period', $filters['pay_period']);
            }
            if (! empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
            if (! empty($filters['company_un_id'])) {
                $builder->where('company_un_id', $filters['company_un_id']);
            }
        });
    }

    /**
     * Find existing payroll record for a specific employee and period.
     */
    public function findByEmployeePeriod(string $empUnId, string $period): ?array
    {
        $row = $this->model
            ->where('employee_un_id', $empUnId)
            ->where('pay_period', $period)
            ->where('deleted_at', null)
            ->first();
        return $row ?: null;
    }
}
