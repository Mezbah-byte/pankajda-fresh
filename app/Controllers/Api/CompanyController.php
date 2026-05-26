<?php

namespace App\Controllers\Api;

class CompanyController extends BaseApiController
{
    private \App\Services\CompanyService $service;

    public function __construct()
    {
        $this->service = new \App\Services\CompanyService();
    }

    public function index()
    {
        $pg = $this->parsePagination();
        $filters = [
            'q'            => $this->request->getGet('q'),
            'status'       => $this->request->getGet('status'),
            'company_type' => $this->request->getGet('company_type'),
        ];
        $result = $this->service->list($filters, $pg['page'], $pg['per_page']);
        return $this->paginated($result['items'], $result['page'], $result['per_page'], $result['total']);
    }

    public function show($unId = null)
    {
        if (! $unId) {
            return $this->failNotFound();
        }
        $row = $this->service->get($unId);
        if (! $row) {
            return $this->failNotFound('Company not found.');
        }
        return $this->ok($row);
    }

    public function create()
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'company_name' => 'required|min_length[2]|max_length[200]',
            'email'        => 'permit_empty|valid_email',
            'phone'        => 'permit_empty|max_length[30]',
            'status'       => 'permit_empty|in_list[active,inactive,pending]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        $row = $this->service->create($body);
        return $this->created($row, 'Company created');
    }

    public function update($unId = null)
    {
        if (! $unId) {
            return $this->failNotFound();
        }
        $body = $this->request->getJSON(true) ?? $this->request->getRawInput();
        try {
            $row = $this->service->update($unId, $body);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok($row, 'Company updated');
    }

    public function delete($unId = null)
    {
        if (! $unId) {
            return $this->failNotFound();
        }
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok(null, 'Company deleted');
    }
}
