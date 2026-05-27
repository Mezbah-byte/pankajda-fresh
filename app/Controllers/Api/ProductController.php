<?php

namespace App\Controllers\Api;

use App\Services\ProductService;

class ProductController extends BaseApiController
{
    private ProductService $service;

    public function __construct()
    {
        $this->service = new ProductService();
    }

    /**
     * GET /api/v1/products
     */
    public function index()
    {
        $pg      = $this->parsePagination();
        $filters = [
            'q'             => $this->request->getGet('q'),
            'category'      => $this->request->getGet('category'),
            'company_un_id' => $this->request->getGet('company_un_id'),
            'status'        => $this->request->getGet('status'),
        ];
        $result = $this->service->list($filters, $pg['page'], $pg['per_page']);
        return $this->paginated($result['items'], $result['page'], $result['per_page'], $result['total']);
    }

    /**
     * GET /api/v1/products/{un_id}
     */
    public function show($unId = null)
    {
        if (! $unId) {
            return $this->failNotFound();
        }
        $row = $this->service->get($unId);
        if (! $row) {
            return $this->failNotFound('Product not found.');
        }
        return $this->ok($row);
    }

    /**
     * POST /api/v1/products
     */
    public function create()
    {
        $body  = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'product_name'  => 'required|min_length[2]|max_length[200]',
            'product_code'  => 'permit_empty|max_length[80]',
            'category'      => 'permit_empty|max_length[80]',
            'unit'          => 'permit_empty|max_length[40]',
            'default_price' => 'permit_empty|numeric',
        ];

        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }

        $row = $this->service->create($body);
        return $this->created($row, 'Product created');
    }

    /**
     * PUT /api/v1/products/{un_id}
     */
    public function update($unId = null)
    {
        if (! $unId) {
            return $this->failNotFound();
        }
        $body  = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $rules = [
            'product_name'  => 'permit_empty|min_length[2]|max_length[200]',
            'product_code'  => 'permit_empty|max_length[80]',
            'category'      => 'permit_empty|max_length[80]',
            'unit'          => 'permit_empty|max_length[40]',
            'default_price' => 'permit_empty|numeric',
            'status'        => 'permit_empty|in_list[active,inactive]',
        ];

        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }

        try {
            $row = $this->service->update($unId, $body);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }

        return $this->ok($row, 'Product updated');
    }

    /**
     * DELETE /api/v1/products/{un_id}
     */
    public function delete($unId = null)
    {
        if (! $unId) {
            return $this->failNotFound();
        }

        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }

        return $this->ok(null, 'Product deleted');
    }

    /**
     * GET /api/v1/products/select
     * Returns lightweight product list for sale form dropdowns.
     */
    public function select()
    {
        return $this->ok($this->service->forSelect(), 'Products for select');
    }

    /**
     * GET /api/v1/products/categories
     */
    public function categories()
    {
        return $this->ok($this->service->categories(), 'Product categories');
    }
}
