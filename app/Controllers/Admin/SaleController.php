<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\CompanyRepository;
use App\Repositories\ContainerRepository;
use App\Repositories\CustomerRepository;
use App\Services\SaleService;

class SaleController extends BaseController
{
    private SaleService $service;
    private CustomerRepository $customers;
    private CompanyRepository $companies;

    public function __construct()
    {
        $this->service   = new SaleService();
        $this->customers = new CustomerRepository();
        $this->companies = new CompanyRepository();
    }

    public function index()
    {
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = [
            'q'              => $this->request->getGet('q'),
            'customer_un_id' => $this->request->getGet('customer_un_id'),
            'sale_type'      => $this->request->getGet('sale_type'),
            'payment_status' => $this->request->getGet('payment_status'),
            'date_from'      => $this->request->getGet('date_from'),
            'date_to'        => $this->request->getGet('date_to'),
        ];
        $result = $this->service->list($filters, $page, 15);
        return view('admin/sales/index', [
            'title'      => 'Sales',
            'sales'      => $result['items'],
            'customers'  => $this->customers->search([], 1, 200)['items'],
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
        return view('admin/sales/form', [
            'title'         => 'New Sale',
            'next_invoice'  => $this->service->nextInvoiceNo(),
            'customers'     => $this->customers->search([], 1, 500)['items'],
            'companies'     => $this->companies->search([], 1, 100)['items'],
            'action'        => site_url('admin/sales'),
        ]);
    }

    public function store()
    {
        $post = $this->request->getPost();

        // Reshape posted items[] arrays into the structure SaleService expects
        $items = [];
        if (! empty($post['items']) && is_array($post['items'])) {
            foreach ($post['items'] as $row) {
                if (empty($row['product_name']) || (float) ($row['quantity'] ?? 0) <= 0) continue;
                $items[] = $row;
            }
        }
        $post['items'] = $items;

        try {
            $sale = $this->service->create($post);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/sales/' . $sale['un_id'])->with('success', 'Sale recorded.');
    }

    public function show(string $unId)
    {
        $sale = $this->service->get($unId);
        if (! $sale) return redirect()->to('admin/sales')->with('error', 'Sale not found.');
        $customer = $this->customers->findByUnId($sale['customer_un_id']);
        $company  = $sale['company_un_id'] ? $this->companies->findByUnId($sale['company_un_id']) : null;
        return view('admin/sales/show', [
            'title'    => 'Invoice ' . $sale['invoice_no'],
            'sale'     => $sale,
            'customer' => $customer,
            'company'  => $company,
        ]);
    }

    public function invoice(string $unId)
    {
        $sale = $this->service->get($unId);
        if (! $sale) return redirect()->to('admin/sales')->with('error', 'Sale not found.');
        $customer = $this->customers->findByUnId($sale['customer_un_id']);
        $company  = $sale['company_un_id'] ? $this->companies->findByUnId($sale['company_un_id']) : null;
        return view('admin/sales/invoice', [
            'sale'     => $sale,
            'customer' => $customer,
            'company'  => $company,
        ]);
    }

    /**
     * Stream a PDF version of the invoice directly to the browser.
     * Requires dompdf/dompdf — run: composer require dompdf/dompdf
     */
    public function invoicePdf(string $unId)
    {
        $sale = $this->service->get($unId);
        if (! $sale) return redirect()->to('admin/sales')->with('error', 'Sale not found.');
        $customer = $this->customers->findByUnId($sale['customer_un_id']);
        $company  = $sale['company_un_id'] ? $this->companies->findByUnId($sale['company_un_id']) : null;

        try {
            $pdf = new \App\Libraries\PdfExporter();
            $pdf->loadView('admin/sales/invoice_pdf', [
                    'sale'     => $sale,
                    'customer' => $customer,
                    'company'  => $company,
                ])
                ->setPaper('A4', 'portrait')
                ->stream('invoice-' . ($sale['invoice_no'] ?? $unId) . '.pdf');
        } catch (\RuntimeException $e) {
            return redirect()->to('admin/sales/' . $unId . '/invoice')
                             ->with('error', $e->getMessage());
        }
    }

    public function addPayment(string $unId)
    {
        if (! $this->validate(['amount' => 'required|numeric|greater_than[0]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->addPayment($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/sales/' . $unId)->with('success', 'Payment recorded.');
    }

    public function delete(string $unId)
    {
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/sales')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/sales')->with('success', 'Sale deleted.');
    }
}
