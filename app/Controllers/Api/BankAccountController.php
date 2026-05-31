<?php

namespace App\Controllers\Api;

use App\Services\BankAccountService;

class BankAccountController extends BaseApiController
{
    private BankAccountService $service;

    public function __construct()
    {
        $this->service = new BankAccountService();
    }

    public function index()
    {
        $pg      = $this->parsePagination();
        $filters = [
            'q'             => $this->request->getGet('q'),
            'account_type'  => $this->request->getGet('account_type'),
            'status'        => $this->request->getGet('status'),
            'company_un_id' => $this->request->getGet('company_un_id'),
        ];
        $r = $this->service->list($filters, $pg['page'], $pg['per_page']);
        return $this->paginated($r['items'], $r['page'], $r['per_page'], $r['total']);
    }

    public function show($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $row = $this->service->get($unId);
        if (! $row) return $this->failNotFound('Bank account not found.');
        return $this->ok($row);
    }

    public function create()
    {
        $body  = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'account_name'   => 'required|min_length[2]|max_length[200]',
            'account_number' => 'permit_empty|max_length[100]',
            'bank_name'      => 'permit_empty|max_length[200]',
            'account_type'   => 'permit_empty|max_length[50]',
            'opening_balance'=> 'permit_empty|numeric',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $row = $this->service->create($body);
        } catch (\InvalidArgumentException $e) {
            return $this->failValidation(['_error' => $e->getMessage()]);
        }
        return $this->created($row, 'Bank account created');
    }

    public function update($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $body  = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $rules = [
            'account_name'   => 'permit_empty|min_length[2]|max_length[200]',
            'account_number' => 'permit_empty|max_length[100]',
            'bank_name'      => 'permit_empty|max_length[200]',
            'account_type'   => 'permit_empty|max_length[50]',
            'status'         => 'permit_empty|in_list[active,inactive]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $row = $this->service->update($unId, $body);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok($row, 'Bank account updated');
    }

    public function delete($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok(null, 'Bank account deleted');
    }

    public function adjust($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $body  = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'amount'    => 'required|numeric|greater_than[0]',
            'direction' => 'required|in_list[credit,debit]',
            'notes'     => 'permit_empty|max_length[500]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $this->service->adjustBalance($unId, (float) $body['amount'], $body['direction']);
            $row = $this->service->get($unId);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok($row, 'Balance adjusted');
    }

    public function active()
    {
        return $this->ok($this->service->active(), 'Active bank accounts');
    }
}
