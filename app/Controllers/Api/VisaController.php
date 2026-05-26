<?php

namespace App\Controllers\Api;

use App\Services\VisaService;

class VisaController extends BaseApiController
{
    private VisaService $service;

    public function __construct()
    {
        $this->service = new VisaService();
    }

    public function index()
    {
        $pg = $this->parsePagination();
        $filters = [
            'q'              => $this->request->getGet('q'),
            'company_un_id'  => $this->request->getGet('company_un_id'),
            'payment_status' => $this->request->getGet('payment_status'),
            'country'        => $this->request->getGet('country'),
        ];
        $r = $this->service->list($filters, $pg['page'], $pg['per_page']);
        return $this->paginated($r['items'], $r['page'], $r['per_page'], $r['total']);
    }

    public function show($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $row = $this->service->get($unId);
        if (! $row) return $this->failNotFound('Visa not found.');
        $row['payments'] = $this->service->paymentsFor($unId);
        return $this->ok($row);
    }

    public function create()
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'visa_name'     => 'required|min_length[2]|max_length[200]',
            'company_un_id' => 'required',
            'visa_cost'     => 'permit_empty|numeric',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        $row = $this->service->create($body);
        return $this->created($row, 'Visa created');
    }

    public function update($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $body = $this->request->getJSON(true) ?? $this->request->getRawInput();
        try {
            $row = $this->service->update($unId, $body);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok($row, 'Visa updated');
    }

    public function delete($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok(null, 'Visa deleted');
    }

    public function addPayment($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        if (! $this->validateData($body, ['amount' => 'required|numeric|greater_than[0]'])) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $result = $this->service->addPayment($unId, $body);
        } catch (\InvalidArgumentException $e) {
            return $this->failValidation(['amount' => $e->getMessage()]);
        }
        return $this->created($result, 'Payment recorded');
    }
}
