<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\StockService;

class StockController extends BaseController
{
    private StockService $service;

    public function __construct()
    {
        $this->service = new StockService();
    }

    public function index()
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = [
            'q'        => $this->request->getGet('q'),
            'category' => $this->request->getGet('category'),
            'status'   => $this->request->getGet('status'),
        ];
        $result  = $this->service->list($filters, $page, 20);
        $summary = $this->service->summary();

        return view('admin/stock/index', [
            'title'      => 'Stock / Inventory',
            'items'      => $result['items'],
            'summary'    => $summary,
            'categories' => $this->service->categories(),
            'low_stock'  => $this->service->lowStock(),
            'pagination' => ['page' => $result['page'], 'per_page' => $result['per_page'], 'total' => $result['total'], 'last_page' => max(1, (int) ceil($result['total'] / $result['per_page']))],
            'filters'    => $filters,
        ]);
    }

    public function create()
    {
        return view('admin/stock/form', [
            'title'      => 'New Stock Item',
            'item'       => null,
            'categories' => $this->service->categories(),
            'action'     => site_url('admin/stock'),
        ]);
    }

    public function store()
    {
        if (! $this->validate([
            'item_name'   => 'required|min_length[2]|max_length[200]',
            'unit'        => 'required',
            'current_qty' => 'required|numeric',
            'unit_cost'   => 'permit_empty|numeric',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $item = $this->service->create($this->request->getPost());
        return redirect()->to('admin/stock/' . $item['un_id'])->with('success', 'Stock item created.');
    }

    public function show(string $unId)
    {
        $item = $this->service->get($unId);
        if (! $item) return redirect()->to('admin/stock')->with('error', 'Item not found.');
        $txns = $this->service->transactions($unId, 1, 30);
        return view('admin/stock/show', [
            'title'        => $item['item_name'],
            'item'         => $item,
            'transactions' => $txns['items'],
        ]);
    }

    public function edit(string $unId)
    {
        $item = $this->service->get($unId);
        if (! $item) return redirect()->to('admin/stock')->with('error', 'Item not found.');
        return view('admin/stock/form', [
            'title'      => 'Edit Stock Item',
            'item'       => $item,
            'categories' => $this->service->categories(),
            'action'     => site_url('admin/stock/' . $unId),
        ]);
    }

    public function update(string $unId)
    {
        if (! $this->validate([
            'item_name' => 'required|min_length[2]|max_length[200]',
            'unit'      => 'required',
            'unit_cost' => 'permit_empty|numeric',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->update($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/stock')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/stock/' . $unId)->with('success', 'Item updated.');
    }

    public function delete(string $unId)
    {
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/stock')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/stock')->with('success', 'Item deleted.');
    }

    public function stockIn(string $unId)
    {
        if (! $this->validate([
            'quantity'  => 'required|numeric|greater_than[0]',
            'txn_date'  => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->stockIn($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/stock/' . $unId)->with('success', 'Stock received.');
    }

    public function stockOut(string $unId)
    {
        if (! $this->validate([
            'quantity' => 'required|numeric|greater_than[0]',
            'txn_date' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->stockOut($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/stock/' . $unId)->with('success', 'Stock issued.');
    }

    public function adjust(string $unId)
    {
        if (! $this->validate(['new_qty' => 'required|numeric|greater_than_equal_to[0]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->adjust($unId, (float) $this->request->getPost('new_qty'), $this->request->getPost('notes') ?? '');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/stock/' . $unId)->with('success', 'Stock adjusted.');
    }
}
