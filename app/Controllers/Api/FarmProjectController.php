<?php

namespace App\Controllers\Api;

use App\Services\FarmProjectService;

class FarmProjectController extends BaseApiController
{
    private FarmProjectService $service;

    public function __construct()
    {
        $this->service = new FarmProjectService();
    }

    public function index()
    {
        $pg = $this->parsePagination();
        $filters = [
            'q'              => $this->request->getGet('q'),
            'company_un_id'  => $this->request->getGet('company_un_id'),
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
            return $this->failNotFound('Farm project not found.');
        }
        return $this->ok($row);
    }

    public function create()
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'project_name' => 'required|min_length[2]|max_length[200]',
            'crop_name'    => 'permit_empty|max_length[100]',
            'land_size'    => 'permit_empty|numeric',
            'land_unit'    => 'permit_empty|max_length[20]',
            'start_date'   => 'permit_empty|valid_date',
            'end_date'     => 'permit_empty|valid_date',
            'total_cost'   => 'permit_empty|numeric',
            'sale_amount'  => 'permit_empty|numeric',
            'status'       => 'permit_empty|in_list[planning,active,harvesting,completed,cancelled]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        $row = $this->service->create($body);
        return $this->created($row, 'Farm project created');
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
        return $this->ok($row, 'Farm project updated');
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
        return $this->ok(null, 'Farm project deleted');
    }

    /**
     * POST /api/v1/farm-projects/{un_id}/activities
     * Add a farm activity (workers, seeds, cost) to a project.
     */
    public function addActivity($unId = null)
    {
        if (! $unId) {
            return $this->failNotFound();
        }
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'activity_type'  => 'required|max_length[80]',
            'activity_date'  => 'permit_empty|valid_date',
            'description'    => 'permit_empty|max_length[500]',
            'workers'        => 'permit_empty|integer',
            'cost'           => 'permit_empty|numeric',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $result = $this->service->addActivity($unId, $body);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->created($result, 'Activity added');
    }
}
