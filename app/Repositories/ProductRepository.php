<?php

namespace App\Repositories;

use App\Models\ProductModel;

class ProductRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new ProductModel();
    }

    /**
     * Paginated search with optional filters.
     */
    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $builder->groupStart()
                    ->like('product_name', $filters['q'])
                    ->orLike('product_code', $filters['q'])
                    ->groupEnd();
            }
            if (! empty($filters['category'])) {
                $builder->where('category', $filters['category']);
            }
            if (! empty($filters['company_un_id'])) {
                $builder->where('company_un_id', $filters['company_un_id']);
            }
            if (! empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
        });
    }

    /**
     * Returns lightweight list of active products for sale form dropdowns.
     *
     * @return array<int, array{un_id: string, product_name: string, unit: string, default_price: float}>
     */
    public function findActiveForSelect(): array
    {
        $rows = $this->model
            ->select('un_id, product_name, unit, default_price')
            ->where('status', 'active')
            ->where('deleted_at', null)
            ->orderBy('product_name', 'ASC')
            ->findAll();

        return array_map(static function (array $row): array {
            return [
                'un_id'         => $row['un_id'],
                'product_name'  => $row['product_name'],
                'unit'          => $row['unit'],
                'default_price' => (float) $row['default_price'],
            ];
        }, $rows);
    }

    /**
     * Returns distinct non-null category values.
     */
    public function categories(): array
    {
        $rows = $this->model
            ->select('category')
            ->distinct()
            ->where('deleted_at', null)
            ->where('category IS NOT NULL', null, false)
            ->orderBy('category', 'ASC')
            ->findAll();

        return array_column($rows, 'category');
    }
}
