<?php

namespace App\Repositories;

use App\Models\StockItemModel;
use App\Models\StockTransactionModel;

class StockRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new StockItemModel();
    }

    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $builder->like('item_name', $filters['q']);
            }
            if (! empty($filters['category'])) {
                $builder->where('category', $filters['category']);
            }
            if (! empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
            if (! empty($filters['company_un_id'])) {
                $builder->where('company_un_id', $filters['company_un_id']);
            }
        });
    }

    public function lowStock(): array
    {
        return $this->model
            ->where('status', 'active')
            ->where('deleted_at', null)
            ->where('min_qty >', 0)
            ->where('current_qty <=', 'min_qty', false)
            ->findAll();
    }

    public function updateQty(string $unId, float $delta): void
    {
        $this->model->db->table('stock_items')
            ->where('un_id', $unId)
            ->set('current_qty', "current_qty + ({$delta})", false)
            ->update();
    }

    public function setQty(string $unId, float $qty): void
    {
        $this->model->db->table('stock_items')
            ->where('un_id', $unId)
            ->update(['current_qty' => $qty]);
    }

    public function transactionsFor(string $stockItemUnId, int $page = 1, int $perPage = 20): array
    {
        $txnModel = new StockTransactionModel();
        $total    = $txnModel->where('stock_item_un_id', $stockItemUnId)->countAllResults(false);
        $offset   = ($page - 1) * $perPage;
        $items    = $txnModel->where('stock_item_un_id', $stockItemUnId)
                             ->orderBy('txn_date', 'DESC')->orderBy('id', 'DESC')
                             ->limit($perPage, $offset)->findAll();
        return ['items' => $items, 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    public function createTransaction(array $data): string
    {
        $txnModel = new StockTransactionModel();
        $id  = $txnModel->insert($data, true);
        $row = $txnModel->find($id);
        return $row['un_id'] ?? '';
    }
}
