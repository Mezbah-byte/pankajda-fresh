<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\VendorService;
use App\Services\ProductService;

class VendorController extends BaseController
{
    private VendorService $service;

    public function __construct()
    {
        $this->service = new VendorService();
    }

    public function index()
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = [
            'q'      => $this->request->getGet('q'),
            'status' => $this->request->getGet('status'),
        ];
        $result = $this->service->list($filters, $page, 20);
        return view('admin/vendors/index', [
            'title'      => 'Vendors / Suppliers',
            'vendors'    => $result['items'],
            'totals'     => $this->service->totals(),
            'pagination' => [
                'page'      => $result['page'],
                'per_page'  => $result['per_page'],
                'total'     => $result['total'],
                'last_page' => max(1, (int) ceil($result['total'] / $result['per_page'])),
            ],
            'filters'    => $filters,
        ]);
    }

    public function create()
    {
        return view('admin/vendors/form', [
            'title'  => 'New Vendor',
            'vendor' => null,
            'action' => site_url('admin/vendors'),
        ]);
    }

    public function store()
    {
        if (! $this->validate(['vendor_name' => 'required|min_length[2]|max_length[200]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $this->service->create($this->request->getPost());
        return redirect()->to('admin/vendors')->with('success', 'Vendor created.');
    }

    public function show(string $unId)
    {
        $vendor   = $this->service->get($unId);
        if (! $vendor) return redirect()->to('admin/vendors')->with('error', 'Vendor not found.');
        $payments = $this->service->payments($unId, 1, 20);
        $products = (new ProductService())->forVendor($unId);
        return view('admin/vendors/show', [
            'title'    => $vendor['vendor_name'],
            'vendor'   => $vendor,
            'payments' => $payments,
            'products' => $products,
        ]);
    }

    public function edit(string $unId)
    {
        $vendor = $this->service->get($unId);
        if (! $vendor) return redirect()->to('admin/vendors')->with('error', 'Vendor not found.');
        return view('admin/vendors/form', [
            'title'  => 'Edit Vendor',
            'vendor' => $vendor,
            'action' => site_url('admin/vendors/' . $unId),
        ]);
    }

    public function update(string $unId)
    {
        if (! $this->validate(['vendor_name' => 'required|min_length[2]|max_length[200]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->update($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/vendors')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/vendors')->with('success', 'Vendor updated.');
    }

    public function delete(string $unId)
    {
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/vendors')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/vendors')->with('success', 'Vendor deleted.');
    }

    public function addPayment(string $unId)
    {
        if (! $this->validate(['amount' => 'required|numeric|greater_than[0]', 'payment_date' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->addPayment($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/vendors/' . $unId)->with('success', 'Payment recorded.');
    }
}
