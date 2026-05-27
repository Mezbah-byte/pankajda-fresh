<?php

namespace App\Repositories;

use App\Models\EmployeeAdvanceModel;
use Config\Database;

class EmployeeAdvanceRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new EmployeeAdvanceModel();
    }

    /**
     * Paginated list of advances for a specific employee.
     */
    public function forEmployee(string $empUnId, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($empUnId) {
            $builder->where('employee_un_id', $empUnId);
        });
    }

    /**
     * Sum of (amount - repaid_amount) for all outstanding advances of an employee.
     */
    public function outstandingTotal(string $empUnId): float
    {
        $db = Database::connect();
        $row = $db->table('employee_advances')
            ->selectSum('amount - repaid_amount', 'outstanding')
            ->where('employee_un_id', $empUnId)
            ->where('status', 'outstanding')
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();
        return (float) ($row['outstanding'] ?? 0);
    }
}
