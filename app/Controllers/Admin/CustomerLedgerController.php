<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\CustomerRepository;
use App\Services\CustomerLedgerService;

class CustomerLedgerController extends BaseController
{
    private CustomerLedgerService $ledgerService;
    private CustomerRepository    $customers;

    public function __construct()
    {
        $this->ledgerService = new CustomerLedgerService();
        $this->customers     = new CustomerRepository();
    }

    /**
     * GET /admin/customers/{un_id}/ledger
     */
    public function show(string $customerUnId)
    {
        $from = $this->request->getGet('date_from') ?: date('Y-01-01');
        $to   = $this->request->getGet('date_to')   ?: date('Y-m-d');

        $data = $this->ledgerService->ledger($customerUnId, $from, $to);

        if (! $data) {
            return redirect()->to('admin/customers')->with('error', 'Customer not found.');
        }

        return view('admin/customers/ledger', [
            'title'  => 'Account Ledger — ' . $data['customer']['customer_name'],
            'ledger' => $data,
            'from'   => $from,
            'to'     => $to,
        ]);
    }

    /**
     * GET /admin/customers/{un_id}/ledger/print — printable version
     */
    public function print(string $customerUnId)
    {
        $from = $this->request->getGet('date_from') ?: date('Y-01-01');
        $to   = $this->request->getGet('date_to')   ?: date('Y-m-d');

        $data = $this->ledgerService->ledger($customerUnId, $from, $to);

        if (! $data) {
            return redirect()->to('admin/customers')->with('error', 'Customer not found.');
        }

        return view('admin/customers/ledger_print', [
            'title'  => 'Account Statement',
            'ledger' => $data,
        ]);
    }
}
