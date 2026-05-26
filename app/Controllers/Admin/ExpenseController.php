<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\CompanyRepository;
use App\Services\ExpenseService;

class ExpenseController extends BaseController
{
    private ExpenseService $service;
    private CompanyRepository $companies;

    public function __construct()
    {
        $this->service   = new ExpenseService();
        $this->companies = new CompanyRepository();
    }

    public function index()
    {
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = [
            'q'             => $this->request->getGet('q'),
            'category'      => $this->request->getGet('category'),
            'company_un_id' => $this->request->getGet('company_un_id'),
            'date_from'     => $this->request->getGet('date_from'),
            'date_to'       => $this->request->getGet('date_to'),
        ];
        $result = $this->service->list($filters, $page, 15);
        return view('admin/expenses/index', [
            'title'      => 'Expenses',
            'expenses'   => $result['items'],
            'companies'  => $this->companies->search([], 1, 100)['items'],
            'categories' => $this->service->categories(),
            'pagination' => [
                'page'      => $result['page'],
                'per_page'  => $result['per_page'],
                'total'     => $result['total'],
                'last_page' => max(1, (int) ceil($result['total'] / max(1, $result['per_page']))),
            ],
            'filters'    => $filters,
            'totals'     => $this->service->totals(),
            'byCategory' => $this->service->byCategory(),
        ]);
    }

    public function create()
    {
        return view('admin/expenses/form', [
            'title'      => 'New Expense',
            'expense'    => null,
            'companies'  => $this->companies->search([], 1, 100)['items'],
            'categories' => $this->service->categories(),
            'action'     => site_url('admin/expenses'),
        ]);
    }

    public function store()
    {
        if (! $this->validate([
            'expense_title' => 'required|min_length[2]|max_length[200]',
            'amount'        => 'required|numeric',
            'expense_date'  => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $this->service->create($this->request->getPost());
        return redirect()->to('admin/expenses')->with('success', 'Expense recorded.');
    }

    public function show(string $unId)
    {
        $expense = $this->service->get($unId);
        if (! $expense) {
            return redirect()->to('admin/expenses')->with('error', 'Expense not found.');
        }
        // Resolve company name if linked
        $company = null;
        if (! empty($expense['company_un_id'])) {
            $company = $this->companies->findByUnId($expense['company_un_id']);
        }
        return view('admin/expenses/show', [
            'title'   => 'Expense Detail',
            'expense' => $expense,
            'company' => $company,
        ]);
    }

    public function edit(string $unId)
    {
        $expense = $this->service->get($unId);
        if (! $expense) return redirect()->to('admin/expenses')->with('error', 'Expense not found.');
        return view('admin/expenses/form', [
            'title'      => 'Edit Expense',
            'expense'    => $expense,
            'companies'  => $this->companies->search([], 1, 100)['items'],
            'categories' => $this->service->categories(),
            'action'     => site_url('admin/expenses/' . $unId),
        ]);
    }

    public function update(string $unId)
    {
        if (! $this->validate([
            'expense_title' => 'required|min_length[2]|max_length[200]',
            'amount'        => 'required|numeric',
            'expense_date'  => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->update($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/expenses')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/expenses')->with('success', 'Expense updated.');
    }

    public function delete(string $unId)
    {
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/expenses')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/expenses')->with('success', 'Expense deleted.');
    }
}
