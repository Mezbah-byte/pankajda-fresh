<?php

namespace App\Controllers\Api;

use App\Services\CustomerService;

class CustomerController extends BaseApiController
{
    private CustomerService $service;

    public function __construct()
    {
        $this->service = new CustomerService();
    }

    public function index()
    {
        $pg = $this->parsePagination();
        $filters = [
            'q'             => $this->request->getGet('q'),
            'company_un_id' => $this->request->getGet('company_un_id'),
            'status'        => $this->request->getGet('status'),
            'has_due'       => $this->request->getGet('has_due'),
        ];
        $r = $this->service->list($filters, $pg['page'], $pg['per_page']);
        return $this->paginated($r['items'], $r['page'], $r['per_page'], $r['total']);
    }

    public function show($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $row = $this->service->get($unId);
        if (! $row) return $this->failNotFound('Customer not found.');
        return $this->ok($row);
    }

    public function create()
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        if (! $this->validateData($body, [
            'customer_name' => 'required|min_length[2]|max_length[200]',
            'email'         => 'permit_empty|valid_email',
        ])) {
            return $this->failValidation($this->validator->getErrors());
        }
        return $this->created($this->service->create($body), 'Customer created');
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
        return $this->ok($row, 'Customer updated');
    }

    public function delete($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok(null, 'Customer deleted');
    }
}
