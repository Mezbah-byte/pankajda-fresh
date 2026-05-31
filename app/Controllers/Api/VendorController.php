<?php

namespace App\Controllers\Api;

use App\Services\VendorService;

class VendorController extends BaseApiController
{
    private VendorService $service;

    public function __construct()
    {
        $this->service = new VendorService();
    }

    public function index()
    {
        $pg      = $this->parsePagination();
        $filters = [
            'q'             => $this->request->getGet('q'),
            'company_un_id' => $this->request->getGet('company_un_id'),
            'status'        => $this->request->getGet('status'),
            'city'          => $this->request->getGet('city'),
        ];
        $r = $this->service->list($filters, $pg['page'], $pg['per_page']);
        return $this->paginated($r['items'], $r['page'], $r['per_page'], $r['total']);
    }

    public function show($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $row = $this->service->get($unId);
        if (! $row) return $this->failNotFound('Vendor not found.');
        return $this->ok($row);
    }

    public function create()
    {
        $body  = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'vendor_name'   => 'required|min_length[2]|max_length[200]',
            'vendor_code'   => 'permit_empty|max_length[80]',
            'phone'         => 'permit_empty|max_length[30]',
            'email'         => 'permit_empty|valid_email',
            'payment_terms' => 'permit_empty|max_length[100]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $row = $this->service->create($body);
        } catch (\InvalidArgumentException $e) {
            return $this->failValidation(['_error' => $e->getMessage()]);
        }
        return $this->created($row, 'Vendor created');
    }

    public function update($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $body  = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $rules = [
            'vendor_name'   => 'permit_empty|min_length[2]|max_length[200]',
            'vendor_code'   => 'permit_empty|max_length[80]',
            'phone'         => 'permit_empty|max_length[30]',
            'email'         => 'permit_empty|valid_email',
            'status'        => 'permit_empty|in_list[active,inactive]',
            'payment_terms' => 'permit_empty|max_length[100]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $row = $this->service->update($unId, $body);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok($row, 'Vendor updated');
    }

    public function delete($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok(null, 'Vendor deleted');
    }

    public function addPayment($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $body  = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'amount'             => 'required|numeric|greater_than[0]',
            'payment_date'       => 'required|valid_date',
            'payment_method'     => 'permit_empty|max_length[50]',
            'bank_account_un_id' => 'permit_empty|max_length[60]',
            'reference_no'       => 'permit_empty|max_length[100]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $payUnId = $this->service->addPayment($unId, $body);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->created(['payment_un_id' => $payUnId], 'Payment recorded');
    }

    public function payments($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $pg = $this->parsePagination();
        $r  = $this->service->payments($unId, $pg['page'], $pg['per_page']);
        return $this->paginated($r['items'], $r['page'], $r['per_page'], $r['total']);
    }

    public function totals()
    {
        return $this->ok($this->service->totals(), 'Vendor totals');
    }
}
