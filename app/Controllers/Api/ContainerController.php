<?php

namespace App\Controllers\Api;

use App\Services\ContainerService;

class ContainerController extends BaseApiController
{
    private ContainerService $service;

    public function __construct()
    {
        $this->service = new ContainerService();
    }

    public function index()
    {
        $pg = $this->parsePagination();
        $filters = [
            'q'              => $this->request->getGet('q'),
            'company_un_id'  => $this->request->getGet('company_un_id'),
            'status'         => $this->request->getGet('status'),
            'customs_status' => $this->request->getGet('customs_status'),
        ];
        $r = $this->service->list($filters, $pg['page'], $pg['per_page']);
        return $this->paginated($r['items'], $r['page'], $r['per_page'], $r['total']);
    }

    public function show($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $row = $this->service->get($unId);
        if (! $row) return $this->failNotFound('Container not found.');
        return $this->ok($row);
    }

    public function create()
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        if (! $this->validateData($body, ['container_number' => 'required|max_length[80]'])) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $row = $this->service->create($body);
        } catch (\InvalidArgumentException $e) {
            return $this->failValidation(['_error' => $e->getMessage()]);
        }
        return $this->created($row, 'Container created');
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
        return $this->ok($row, 'Container updated');
    }

    public function delete($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok(null, 'Container deleted');
    }
}
