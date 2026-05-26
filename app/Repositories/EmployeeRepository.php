<?php

namespace App\Repositories;

use App\Models\EmployeeModel;
use Config\Database;

class EmployeeRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new EmployeeModel();
    }

    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $q = $filters['q'];
                $builder->groupStart()
                    ->like('name', $q)
                    ->orLike('employee_code', $q)
                    ->orLike('phone', $q)
                    ->orLike('email', $q)
                    ->groupEnd();
            }
            if (! empty($filters['company_un_id'])) {
                $builder->where('company_un_id', $filters['company_un_id']);
            }
            if (! empty($filters['department'])) {
                $builder->where('department', $filters['department']);
            }
            if (! empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
        });
    }

    public function totals(): array
    {
        $db = Database::connect();
        $count   = $this->model->where('deleted_at', null)->countAllResults(false);
        $active  = $this->model->where('status', 'active')->where('deleted_at', null)->countAllResults();
        $row     = $db->table('employees')->selectSum('salary', 'total')
            ->where('deleted_at', null)->where('status', 'active')->get()->getRowArray();
        return [
            'count'           => $count,
            'active'          => $active,
            'monthly_payroll' => (float) ($row['total'] ?? 0),
        ];
    }
}
