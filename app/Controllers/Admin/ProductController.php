<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\CompanyRepository;
use App\Services\ProductService;

class ProductController extends BaseController
{
    private ProductService    $service;
    private CompanyRepository $companies;

    public function __construct()
    {
        $this->service   = new ProductService();
        $this->companies = new CompanyRepository();
    }

    public function index()
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = [
            'q'        => $this->request->getGet('q'),
            'category' => $this->request->getGet('category'),
            'status'   => $this->request->getGet('status'),
        ];
        $result = $this->service->list($filters, $page, 20);

        return view('admin/products/index', [
            'title'      => 'Products',
            'products'   => $result['items'],
            'categories' => $this->service->categories(),
            'pagination' => [
                'page'      => $result['page'],
                'per_page'  => $result['per_page'],
                'total'     => $result['total'],
                'last_page' => max(1, (int) ceil($result['total'] / max(1, $result['per_page']))),
            ],
            'filters'    => $filters,
        ]);
    }

    public function create()
    {
        return view('admin/products/form', [
            'title'      => 'New Product',
            'product'    => null,
            'companies'  => $this->companies->search([], 1, 100)['items'],
            'categories' => $this->service->categories(),
            'action'     => site_url('admin/products'),
        ]);
    }

    public function store()
    {
        if (! $this->validate([
            'product_name' => 'required|min_length[2]|max_length[200]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->service->create($this->request->getPost());

        return redirect()->to('admin/products')->with('success', 'Product created successfully.');
    }

    public function edit(string $unId)
    {
        $product = $this->service->get($unId);
        if (! $product) {
            return redirect()->to('admin/products')->with('error', 'Product not found.');
        }

        return view('admin/products/form', [
            'title'      => 'Edit Product',
            'product'    => $product,
            'companies'  => $this->companies->search([], 1, 100)['items'],
            'categories' => $this->service->categories(),
            'action'     => site_url('admin/products/' . $unId),
        ]);
    }

    public function update(string $unId)
    {
        if (! $this->validate([
            'product_name' => 'required|min_length[2]|max_length[200]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $this->service->update($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/products')->with('error', $e->getMessage());
        }

        return redirect()->to('admin/products')->with('success', 'Product updated successfully.');
    }

    public function delete(string $unId)
    {
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/products')->with('error', $e->getMessage());
        }

        return redirect()->to('admin/products')->with('success', 'Product deleted.');
    }
}
