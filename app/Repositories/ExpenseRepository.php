<?php

namespace App\Repositories;

use App\Models\ExpenseModel;
use Config\Database;

class ExpenseRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new ExpenseModel();
    }

    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $builder->like('expense_title', $filters['q']);
            }
            if (! empty($filters['category'])) {
                $builder->where('category', $filters['category']);
            }
            if (! empty($filters['company_un_id'])) {
                $builder->where('company_un_id', $filters['company_un_id']);
            }
            if (! empty($filters['date_from'])) {
                $builder->where('expense_date >=', $filters['date_from']);
            }
            if (! empty($filters['date_to'])) {
                $builder->where('expense_date <=', $filters['date_to']);
            }
        });
    }

    public function categories(): array
    {
        $rows = $this->model->select('category')
            ->distinct()
            ->where('deleted_at', null)
            ->orderBy('category', 'ASC')
            ->findAll();
        return array_column($rows, 'category');
    }

    public function totals(): array
    {
        $db = Database::connect();
        $count = $this->model->where('deleted_at', null)->countAllResults(false);
        $row    = $db->table('expenses')->selectSum('amount', 'total')
            ->where('deleted_at', null)->get()->getRowArray();
        $month  = $db->table('expenses')->selectSum('amount', 'total')
            ->where('deleted_at', null)
            ->where('expense_date >=', date('Y-m-01'))
            ->get()->getRowArray();
        return [
            'count' => $count,
            'total' => (float) ($row['total'] ?? 0),
            'month' => (float) ($month['total'] ?? 0),
        ];
    }

    public function byCategory(): array
    {
        $db = Database::connect();
        return $db->table('expenses')
            ->select('category, SUM(amount) AS total, COUNT(*) AS count')
            ->where('deleted_at', null)
            ->groupBy('category')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();
    }
}
