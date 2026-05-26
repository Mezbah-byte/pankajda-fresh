<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\CompanyRepository;
use App\Services\CustomerService;

class CustomerController extends BaseController
{
    private CustomerService $service;
    private CompanyRepository $companies;

    public function __construct()
    {
        $this->service   = new CustomerService();
        $this->companies = new CompanyRepository();
    }

    public function index()
    {
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = [
            'q'       => $this->request->getGet('q'),
            'has_due' => $this->request->getGet('has_due'),
        ];
        $result = $this->service->list($filters, $page, 15);
        return view('admin/customers/index', [
            'title'      => 'Customers',
            'customers'  => $result['items'],
            'pagination' => [
                'page'      => $result['page'],
                'per_page'  => $result['per_page'],
                'total'     => $result['total'],
                'last_page' => max(1, (int) ceil($result['total'] / max(1, $result['per_page']))),
            ],
            'filters'    => $filters,
            'totals'     => $this->service->totals(),
        ]);
    }

    public function create()
    {
        return view('admin/customers/form', [
            'title'     => 'Add Customer',
            'customer'  => null,
            'companies' => $this->companies->search([], 1, 100)['items'],
            'action'    => site_url('admin/customers'),
        ]);
    }

    public function store()
    {
        if (! $this->validate(['customer_name' => 'required|min_length[2]|max_length[200]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $row = $this->service->create($this->request->getPost());
        return redirect()->to('admin/customers/' . $row['un_id'])->with('success', 'Customer added.');
    }

    public function show(string $unId)
    {
        $customer = $this->service->get($unId);
        if (! $customer) return redirect()->to('admin/customers')->with('error', 'Customer not found.');
        return view('admin/customers/show', [
            'title'    => $customer['customer_name'],
            'customer' => $customer,
        ]);
    }

    public function edit(string $unId)
    {
        $customer = $this->service->get($unId);
        if (! $customer) return redirect()->to('admin/customers')->with('error', 'Customer not found.');
        return view('admin/customers/form', [
            'title'     => 'Edit ' . $customer['customer_name'],
            'customer'  => $customer,
            'companies' => $this->companies->search([], 1, 100)['items'],
            'action'    => site_url('admin/customers/' . $unId),
        ]);
    }

    public function update(string $unId)
    {
        if (! $this->validate(['customer_name' => 'required|min_length[2]|max_length[200]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->update($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/customers')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/customers/' . $unId)->with('success', 'Customer updated.');
    }

    public function delete(string $unId)
    {
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/customers')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/customers')->with('success', 'Customer deleted.');
    }
}
