<?php

namespace App\Controllers\Api;

use App\Services\EmployeeService;

class EmployeeController extends BaseApiController
{
    private EmployeeService $service;

    public function __construct()
    {
        $this->service = new EmployeeService();
    }

    public function index()
    {
        $pg = $this->parsePagination();
        $filters = [
            'q'              => $this->request->getGet('q'),
            'company_un_id'  => $this->request->getGet('company_un_id'),
            'department'     => $this->request->getGet('department'),
            'status'         => $this->request->getGet('status'),
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
            return $this->failNotFound('Employee not found.');
        }
        return $this->ok($row);
    }

    public function create()
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'name'        => 'required|min_length[2]|max_length[150]',
            'email'       => 'permit_empty|valid_email',
            'phone'       => 'permit_empty|max_length[30]',
            'designation' => 'permit_empty|max_length[100]',
            'department'  => 'permit_empty|max_length[100]',
            'salary'      => 'permit_empty|numeric',
            'status'      => 'permit_empty|in_list[active,inactive,on_leave,terminated]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        $row = $this->service->create($body);
        return $this->created($row, 'Employee created');
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
        return $this->ok($row, 'Employee updated');
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
        return $this->ok(null, 'Employee deleted');
    }
}
