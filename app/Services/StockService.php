<?php

namespace App\Services;

use App\Repositories\StockRepository;
use Config\Database;

class StockService extends BaseService
{
    private StockRepository $repo;

    public function __construct(?StockRepository $repo = null)
    {
        $this->repo = $repo ?? new StockRepository();
    }

    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->repo->search($filters, $page, $perPage);
    }

    public function get(string $unId): ?array
    {
        return $this->repo->findByUnId($unId);
    }

    public function create(array $input): array
    {
        $data = $this->normalize($input);
        $unId = $this->transaction(fn () => $this->repo->create($data));
        $this->audit('stock.created', 'stock_item', $unId, ['name' => $data['item_name'] ?? '']);
        return $this->repo->findByUnId($unId);
    }

    public function update(string $unId, array $input): array
    {
        if (! $this->repo->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Stock item not found.');
        }
        $data = $this->normalize($input);
        $this->transaction(fn () => $this->repo->updateByUnId($unId, $data));
        $this->audit('stock.updated', 'stock_item', $unId);
        return $this->repo->findByUnId($unId);
    }

    public function delete(string $unId): bool
    {
        if (! $this->repo->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Stock item not found.');
        }
        $this->repo->deleteByUnId($unId);
        $this->audit('stock.deleted', 'stock_item', $unId);
        return true;
    }

    public function stockIn(string $unId, array $input): string
    {
        $item = $this->repo->findByUnId($unId);
        if (! $item) throw new \InvalidArgumentException('Stock item not found.');
        $qty = (float) ($input['quantity'] ?? 0);
        if ($qty <= 0) throw new \InvalidArgumentException('Quantity must be positive.');

        $txnUnId = $this->transaction(function () use ($unId, $input, $qty) {
            $this->repo->updateQty($unId, $qty);
            return $this->repo->createTransaction([
                'stock_item_un_id' => $unId,
                'type'             => 'in',
                'quantity'         => $qty,
                'unit_cost'        => $input['unit_cost'] ?? null,
                'reference'        => $input['reference'] ?? null,
                'notes'            => $input['notes'] ?? null,
                'txn_date'         => $input['txn_date'] ?? date('Y-m-d'),
                'created_by_un_id' => session('user_un_id'),
            ]);
        });
        $this->audit('stock.in', 'stock_item', $unId, ['qty' => $qty]);
        return $txnUnId;
    }

    public function stockOut(string $unId, array $input): string
    {
        $item = $this->repo->findByUnId($unId);
        if (! $item) throw new \InvalidArgumentException('Stock item not found.');
        $qty = (float) ($input['quantity'] ?? 0);
        if ($qty <= 0) throw new \InvalidArgumentException('Quantity must be positive.');
        if ((float) $item['current_qty'] < $qty) {
            throw new \InvalidArgumentException('Insufficient stock. Available: ' . $item['current_qty']);
        }

        $txnUnId = $this->transaction(function () use ($unId, $input, $qty) {
            $this->repo->updateQty($unId, -$qty);
            return $this->repo->createTransaction([
                'stock_item_un_id' => $unId,
                'type'             => 'out',
                'quantity'         => $qty,
                'unit_cost'        => null,
                'reference'        => $input['reference'] ?? null,
                'notes'            => $input['notes'] ?? null,
                'txn_date'         => $input['txn_date'] ?? date('Y-m-d'),
                'created_by_un_id' => session('user_un_id'),
            ]);
        });
        $this->audit('stock.out', 'stock_item', $unId, ['qty' => $qty]);
        return $txnUnId;
    }

    public function adjust(string $unId, float $newQty, string $notes = ''): void
    {
        $item = $this->repo->findByUnId($unId);
        if (! $item) throw new \InvalidArgumentException('Stock item not found.');
        $diff = $newQty - (float) $item['current_qty'];

        $this->transaction(function () use ($unId, $newQty, $diff, $notes) {
            $this->repo->setQty($unId, $newQty);
            $this->repo->createTransaction([
                'stock_item_un_id' => $unId,
                'type'             => 'adjustment',
                'quantity'         => abs($diff),
                'notes'            => 'Adjustment: ' . $notes,
                'txn_date'         => date('Y-m-d'),
                'created_by_un_id' => session('user_un_id'),
            ]);
        });
        $this->audit('stock.adjusted', 'stock_item', $unId, ['new_qty' => $newQty]);
    }

    public function lowStock(): array { return $this->repo->lowStock(); }

    public function categories(): array
    {
        $db   = Database::connect();
        $rows = $db->table('stock_items')->select('category')->distinct()
                   ->where('deleted_at', null)->whereNotIn('category', [''])->get()->getResultArray();
        return array_filter(array_column($rows, 'category'));
    }

    public function summary(): array
    {
        $db = Database::connect();
        $row = $db->table('stock_items')
            ->selectCount('id', 'total_items')
            ->select('SUM(current_qty * unit_cost) AS total_value', false)
            ->where('deleted_at', null)
            ->get()->getRowArray();
        return [
            'total_items'      => (int)   ($row['total_items']  ?? 0),
            'total_value'      => (float) ($row['total_value']  ?? 0),
            'low_stock_count'  => count($this->repo->lowStock()),
        ];
    }

    public function transactions(string $unId, int $page = 1, int $perPage = 20): array
    {
        return $this->repo->transactionsFor($unId, $page, $perPage);
    }

    private function normalize(array $input): array
    {
        return array_intersect_key($input, array_flip([
            'company_un_id', 'product_un_id', 'item_name', 'category',
            'unit', 'current_qty', 'min_qty', 'unit_cost', 'status',
        ]));
    }
}
