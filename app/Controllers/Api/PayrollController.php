<?php

namespace App\Controllers\Api;

use App\Services\PayrollService;

class PayrollController extends BaseApiController
{
    private PayrollService $service;

    public function __construct()
    {
        $this->service = new PayrollService();
    }

    public function index()
    {
        $pg      = $this->parsePagination();
        $filters = [
            'pay_period'     => $this->request->getGet('pay_period') ?: date('Y-m'),
            'employee_un_id' => $this->request->getGet('employee_un_id'),
            'status'         => $this->request->getGet('status'),
        ];
        $r = $this->service->list($filters, $pg['page'], $pg['per_page']);
        return $this->paginated($r['items'], $r['page'], $r['per_page'], $r['total']);
    }

    public function show($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $row = $this->service->get($unId);
        if (! $row) return $this->failNotFound('Payroll record not found.');
        return $this->ok($row);
    }

    public function create()
    {
        $body  = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'employee_un_id' => 'required',
            'pay_period'     => 'required|regex_match[/^\d{4}-\d{2}$/]',
            'basic_salary'   => 'required|numeric|greater_than_equal_to[0]',
            'allowances'     => 'permit_empty|numeric',
            'deductions'     => 'permit_empty|numeric',
            'advance_deduction' => 'permit_empty|numeric',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $row = $this->service->generate($body);
        } catch (\InvalidArgumentException $e) {
            return $this->failValidation(['_error' => $e->getMessage()]);
        }
        return $this->created($row, 'Payroll generated');
    }

    public function markPaid($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        try {
            $row = $this->service->markPaid($unId, $body);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok($row, 'Marked as paid');
    }

    public function delete($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok(null, 'Payroll record deleted');
    }

    public function summary()
    {
        $period = $this->request->getGet('pay_period') ?: date('Y-m');
        return $this->ok($this->service->summary($period), 'Payroll summary');
    }

    public function advances()
    {
        $pg      = $this->parsePagination();
        $empUnId = $this->request->getGet('employee_un_id');
        $r       = $this->service->advances($empUnId, $pg['page'], $pg['per_page']);
        return $this->paginated($r['items'], $r['page'], $r['per_page'], $r['total']);
    }

    public function addAdvance()
    {
        $body  = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'employee_un_id' => 'required',
            'amount'         => 'required|numeric|greater_than[0]',
            'advance_date'   => 'required|valid_date',
            'notes'          => 'permit_empty|max_length[500]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $unId = $this->service->addAdvance($body);
        } catch (\InvalidArgumentException $e) {
            return $this->failValidation(['_error' => $e->getMessage()]);
        }
        return $this->created(['advance_un_id' => $unId], 'Advance recorded');
    }
}
