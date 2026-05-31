<?php

namespace App\Controllers\Api;

use App\Services\StockService;

class StockController extends BaseApiController
{
    private StockService $service;

    public function __construct()
    {
        $this->service = new StockService();
    }

    public function index()
    {
        $pg      = $this->parsePagination();
        $filters = [
            'q'             => $this->request->getGet('q'),
            'category'      => $this->request->getGet('category'),
            'company_un_id' => $this->request->getGet('company_un_id'),
            'low_stock'     => $this->request->getGet('low_stock'),
            'status'        => $this->request->getGet('status'),
        ];
        $r = $this->service->list($filters, $pg['page'], $pg['per_page']);
        return $this->paginated($r['items'], $r['page'], $r['per_page'], $r['total']);
    }

    public function show($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $row = $this->service->get($unId);
        if (! $row) return $this->failNotFound('Stock item not found.');
        return $this->ok($row);
    }

    public function create()
    {
        $body  = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'item_name'  => 'required|min_length[2]|max_length[200]',
            'item_code'  => 'permit_empty|max_length[80]',
            'category'   => 'permit_empty|max_length[100]',
            'unit'       => 'permit_empty|max_length[30]',
            'current_qty'=> 'permit_empty|numeric',
            'min_qty'    => 'permit_empty|numeric',
            'unit_cost'  => 'permit_empty|numeric',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $row = $this->service->create($body);
        } catch (\InvalidArgumentException $e) {
            return $this->failValidation(['_error' => $e->getMessage()]);
        }
        return $this->created($row, 'Stock item created');
    }

    public function update($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $body  = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $rules = [
            'item_name' => 'permit_empty|min_length[2]|max_length[200]',
            'item_code' => 'permit_empty|max_length[80]',
            'category'  => 'permit_empty|max_length[100]',
            'unit'      => 'permit_empty|max_length[30]',
            'min_qty'   => 'permit_empty|numeric',
            'unit_cost' => 'permit_empty|numeric',
            'status'    => 'permit_empty|in_list[active,inactive]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $row = $this->service->update($unId, $body);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok($row, 'Stock item updated');
    }

    public function delete($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok(null, 'Stock item deleted');
    }

    public function stockIn($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $body  = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'quantity'  => 'required|numeric|greater_than[0]',
            'txn_date'  => 'permit_empty|valid_date',
            'unit_cost' => 'permit_empty|numeric',
            'reference' => 'permit_empty|max_length[100]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $txnUnId = $this->service->stockIn($unId, $body);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->created(['transaction_un_id' => $txnUnId], 'Stock in recorded');
    }

    public function stockOut($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $body  = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'quantity' => 'required|numeric|greater_than[0]',
            'txn_date' => 'permit_empty|valid_date',
            'reference'=> 'permit_empty|max_length[100]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $txnUnId = $this->service->stockOut($unId, $body);
        } catch (\InvalidArgumentException $e) {
            return $this->failValidation(['_error' => $e->getMessage()]);
        }
        return $this->created(['transaction_un_id' => $txnUnId], 'Stock out recorded');
    }

    public function adjust($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $body  = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'new_qty' => 'required|numeric|greater_than_equal_to[0]',
            'notes'   => 'permit_empty|max_length[500]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $this->service->adjust($unId, (float) $body['new_qty'], $body['notes'] ?? '');
            $row = $this->service->get($unId);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }
        return $this->ok($row, 'Stock adjusted');
    }

    public function transactions($unId = null)
    {
        if (! $unId) return $this->failNotFound();
        $pg = $this->parsePagination();
        $r  = $this->service->transactions($unId, $pg['page'], $pg['per_page']);
        return $this->paginated($r['items'], $r['page'], $r['per_page'], $r['total']);
    }

    public function lowStock()
    {
        return $this->ok($this->service->lowStock(), 'Low stock items');
    }

    public function categories()
    {
        return $this->ok($this->service->categories(), 'Stock categories');
    }

    public function summary()
    {
        return $this->ok($this->service->summary(), 'Stock summary');
    }
}
