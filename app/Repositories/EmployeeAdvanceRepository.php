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
    public function forEmployee(?string $empUnId, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($empUnId) {
            if ($empUnId !== null) {
                $builder->where('employee_un_id', $empUnId);
            }
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

    /**
     * Apply a repayment amount against oldest outstanding advances first.
     * Updates repaid_amount and flips status to 'repaid' when fully settled.
     */
    public function applyRepayment(string $empUnId, float $repayAmount): void
    {
        if ($repayAmount <= 0) return;

        $db       = Database::connect();
        $advances = $db->table('employee_advances')
            ->where('employee_un_id', $empUnId)
            ->where('status', 'outstanding')
            ->where('deleted_at', null)
            ->orderBy('advance_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $remaining = $repayAmount;
        foreach ($advances as $adv) {
            if ($remaining <= 0.001) break;
            $outstanding = (float) $adv['amount'] - (float) $adv['repaid_amount'];
            if ($outstanding <= 0) continue;
            $apply      = min($remaining, $outstanding);
            $newRepaid  = (float) $adv['repaid_amount'] + $apply;
            $newStatus  = ($newRepaid >= (float) $adv['amount'] - 0.001) ? 'repaid' : 'outstanding';
            $db->table('employee_advances')
               ->where('un_id', $adv['un_id'])
               ->update(['repaid_amount' => $newRepaid, 'status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')]);
            $remaining -= $apply;
        }
    }
}
