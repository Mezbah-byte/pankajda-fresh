<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\CompanyRepository;
use App\Services\CustomerService;
use Config\Database;

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

        $db = Database::connect();

        // Company
        $company = $customer['company_un_id']
            ? $this->companies->findByUnId($customer['company_un_id'])
            : null;

        // Total payments received for this customer
        $payRow = $db->table('sale_payments sp')
            ->join('sales s', 's.un_id = sp.sale_un_id')
            ->selectSum('sp.amount', 'total')
            ->where('s.customer_un_id', $unId)
            ->where('s.deleted_at', null)
            ->where('sp.deleted_at', null)
            ->get()->getRowArray();
        $totalPayments = (float) ($payRow['total'] ?? 0);

        // Total discount from all sales for this customer
        $discRow = $db->table('sales')
            ->selectSum('discount', 'total')
            ->where('customer_un_id', $unId)
            ->where('deleted_at', null)
            ->get()->getRowArray();
        $totalDiscount = (float) ($discRow['total'] ?? 0);

        // Recent invoices (last 10)
        $recentSales = $db->table('sales')
            ->where('customer_un_id', $unId)
            ->where('deleted_at', null)
            ->orderBy('sale_date', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        // GRVs for this customer
        $grvs = $db->table('goods_return_vouchers')
            ->where('customer_un_id', $unId)
            ->where('deleted_at', null)
            ->orderBy('grv_date', 'DESC')
            ->get()->getResultArray();

        return view('admin/customers/show', [
            'title'          => $customer['customer_name'],
            'customer'       => $customer,
            'company'        => $company,
            'total_payments' => $totalPayments,
            'total_discount' => $totalDiscount,
            'recent_sales'   => $recentSales,
            'grvs'           => $grvs,
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
