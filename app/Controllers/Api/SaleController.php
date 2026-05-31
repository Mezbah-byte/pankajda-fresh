<?php

namespace App\Controllers\Api;

use App\Services\SaleService;

class SaleController extends BaseApiController
{
    private SaleService $service;

    public function __construct()
    {
        $this->service = new SaleService();
    }

    public function index()
    {
        $pg = $this->parsePagination();
        $filters = [
            'q'              => $this->request->getGet('q'),
            'customer_un_id' => $this->request->getGet('customer_un_id'),
            'sale_type'      => $this->request->getGet('sale_type'),
            'payment_status' => $this->request->getGet('payment_status'),
            'date_from'      => $this->request->getGet('date_from'),
            'date_to'        => $this->request->getGet('date_to'),
        ];
        $r = $this->service->list($filters, $pg['page'], $pg['per_page']);
        return $this->paginated($r['items'], $r['page'], $r['per_page'], $r['total']);
    }

    public function show($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $row = $this->service->get($unId);
        if (! $row) return $this->failNotFound('Sale not found.');
        return $this->ok($row);
    }

    public function create()
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        try {
            $sale = $this->service->create($body);
        } catch (\InvalidArgumentException $e) {
            return $this->failValidation(['_error' => $e->getMessage()]);
        }
        return $this->created($sale, 'Sale created');
    }

    public function update($unId = null)
    {
        return api_error('Sale updates are not supported. Delete and recreate.', 405);
    }

    public function delete($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok(null, 'Sale deleted');
    }

    public function addPayment($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        if (! $this->validateData($body, [
            'amount'              => 'required|numeric|greater_than[0]',
            'payment_method'      => 'permit_empty|max_length[50]',
            'bank_account_un_id'  => 'permit_empty|max_length[60]',
            'reference_no'        => 'permit_empty|max_length[100]',
            'payment_date'        => 'permit_empty|valid_date',
        ])) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $sale = $this->service->addPayment($unId, $body);
        } catch (\InvalidArgumentException $e) {
            return $this->failValidation(['amount' => $e->getMessage()]);
        }
        return $this->created($sale, 'Payment recorded');
    }
}
