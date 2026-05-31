<?php

namespace App\Controllers\Api;

use App\Services\CustomerLedgerService;

class CustomerLedgerController extends BaseApiController
{
    private CustomerLedgerService $service;

    public function __construct()
    {
        $this->service = new CustomerLedgerService();
    }

    public function show($customerUnId = null)
    {
        if (! $customerUnId) return $this->failNotFound();

        $from = $this->request->getGet('date_from') ?: date('Y-01-01');
        $to   = $this->request->getGet('date_to')   ?: date('Y-m-d');

        $data = $this->service->ledger($customerUnId, $from, $to);
        if (! $data) return $this->failNotFound('Customer not found.');

        return $this->ok($data, 'Customer ledger');
    }
}
