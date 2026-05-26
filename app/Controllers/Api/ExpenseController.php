<?php

namespace App\Controllers\Api;

use App\Services\ExpenseService;

class ExpenseController extends BaseApiController
{
    private ExpenseService $service;

    public function __construct()
    {
        $this->service = new ExpenseService();
    }

    public function index()
    {
        $pg = $this->parsePagination();
        $filters = [
            'q'              => $this->request->getGet('q'),
            'category'       => $this->request->getGet('category'),
            'company_un_id'  => $this->request->getGet('company_un_id'),
            'date_from'      => $this->request->getGet('date_from'),
            'date_to'        => $this->request->getGet('date_to'),
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
            return $this->failNotFound('Expense not found.');
        }
        return $this->ok($row);
    }

    public function create()
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'expense_title'  => 'required|min_length[2]|max_length[200]',
            'category'       => 'required|max_length[80]',
            'amount'         => 'required|numeric',
            'expense_date'   => 'permit_empty|valid_date',
            'payment_method' => 'permit_empty|max_length[40]',
            'reference_no'   => 'permit_empty|max_length[60]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        // For API calls, use the JWT auth user instead of session
        if (! isset($body['created_by_un_id'])) {
            $body['created_by_un_id'] = $this->authUserUnId();
        }
        $row = $this->service->create($body);
        return $this->created($row, 'Expense created');
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
        return $this->ok($row, 'Expense updated');
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
        return $this->ok(null, 'Expense deleted');
    }

    /**
     * GET /api/v1/expenses/categories
     * List distinct expense categories.
     */
    public function categories()
    {
        return $this->ok($this->service->categories(), 'Expense categories');
    }
}
