<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService extends BaseService
{
    private ProductRepository $products;

    public function __construct(?ProductRepository $products = null)
    {
        $this->products = $products ?? new ProductRepository();
    }

    /**
     * Paginated list of products with optional filters.
     */
    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->products->search($filters, $page, $perPage);
    }

    /**
     * Get a single product by un_id.
     */
    public function get(string $unId): ?array
    {
        return $this->products->findByUnId($unId);
    }

    /**
     * Products supplied by a specific vendor.
     */
    public function forVendor(string $vendorUnId): array
    {
        return $this->products->getModel()
            ->where('vendor_un_id', $vendorUnId)
            ->where('deleted_at', null)
            ->orderBy('product_name', 'ASC')
            ->findAll();
    }

    /**
     * Create a new product.
     */
    public function create(array $input): array
    {
        $data = $this->normalize($input);
        $data['default_price'] = (float) ($data['default_price'] ?? 0);
        $data['cost_price']    = (float) ($data['cost_price'] ?? 0);
        $data['status']        = $data['status'] ?? 'active';

        $unId = $this->transaction(fn () => $this->products->create($data));

        $this->audit('product.created', 'product', $unId, [
            'product_name' => $data['product_name'] ?? '',
            'category'     => $data['category'] ?? '',
        ]);

        return $this->products->findByUnId($unId);
    }

    /**
     * Update an existing product.
     */
    public function update(string $unId, array $input): array
    {
        if (! $this->products->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Product not found.');
        }

        $data = $this->normalize($input);
        if (isset($data['default_price'])) {
            $data['default_price'] = (float) $data['default_price'];
        }
        if (isset($data['cost_price'])) {
            $data['cost_price'] = (float) $data['cost_price'];
        }

        $this->transaction(fn () => $this->products->updateByUnId($unId, $data));

        $this->audit('product.updated', 'product', $unId, [
            'product_name' => $data['product_name'] ?? '',
        ]);

        return $this->products->findByUnId($unId);
    }

    /**
     * Soft-delete a product.
     */
    public function delete(string $unId): void
    {
        if (! $this->products->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Product not found.');
        }

        $this->transaction(fn () => $this->products->deleteByUnId($unId));
        $this->audit('product.deleted', 'product', $unId);
    }

    /**
     * Returns distinct category values for filter dropdowns.
     */
    public function categories(): array
    {
        return $this->products->categories();
    }

    /**
     * Returns lightweight product list for sale form dropdowns.
     */
    public function forSelect(): array
    {
        return $this->products->findActiveForSelect();
    }

    /**
     * Whitelist allowed fields. Maps sale_price → default_price for form compatibility.
     */
    private function normalize(array $input): array
    {
        // Form sends 'sale_price'; map to DB column 'default_price'
        if (isset($input['sale_price']) && !isset($input['default_price'])) {
            $input['default_price'] = $input['sale_price'];
        }
        $whitelisted = [
            'company_un_id', 'vendor_un_id', 'product_name', 'product_code', 'category',
            'unit', 'default_price', 'cost_price', 'description', 'status',
        ];
        return array_intersect_key($input, array_flip($whitelisted));
    }
}
