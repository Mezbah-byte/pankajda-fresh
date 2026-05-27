<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\BankAccountService;

class BankAccountController extends BaseController
{
    private BankAccountService $service;

    public function __construct()
    {
        $this->service = new BankAccountService();
    }

    public function index()
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = ['q' => $this->request->getGet('q'), 'status' => $this->request->getGet('status')];
        $result  = $this->service->list($filters, $page, 20);
        return view('admin/bank_accounts/index', [
            'title'      => 'Bank Accounts',
            'accounts'   => $result['items'],
            'pagination' => ['page' => $result['page'], 'per_page' => $result['per_page'], 'total' => $result['total'], 'last_page' => max(1, (int) ceil($result['total'] / $result['per_page']))],
            'filters'    => $filters,
        ]);
    }

    public function create()
    {
        return view('admin/bank_accounts/form', ['title' => 'New Bank Account', 'account' => null, 'action' => site_url('admin/bank-accounts')]);
    }

    public function store()
    {
        if (! $this->validate(['account_name' => 'required', 'bank_name' => 'required', 'account_number' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $this->service->create($this->request->getPost());
        return redirect()->to('admin/bank-accounts')->with('success', 'Bank account created.');
    }

    public function show(string $unId)
    {
        $account = $this->service->get($unId);
        if (! $account) return redirect()->to('admin/bank-accounts')->with('error', 'Account not found.');
        return view('admin/bank_accounts/show', ['title' => $account['account_name'], 'account' => $account]);
    }

    public function edit(string $unId)
    {
        $account = $this->service->get($unId);
        if (! $account) return redirect()->to('admin/bank-accounts')->with('error', 'Account not found.');
        return view('admin/bank_accounts/form', ['title' => 'Edit Account', 'account' => $account, 'action' => site_url('admin/bank-accounts/' . $unId)]);
    }

    public function update(string $unId)
    {
        if (! $this->validate(['account_name' => 'required', 'bank_name' => 'required', 'account_number' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try { $this->service->update($unId, $this->request->getPost()); }
        catch (\InvalidArgumentException $e) { return redirect()->to('admin/bank-accounts')->with('error', $e->getMessage()); }
        return redirect()->to('admin/bank-accounts')->with('success', 'Account updated.');
    }

    public function delete(string $unId)
    {
        try { $this->service->delete($unId); }
        catch (\InvalidArgumentException $e) { return redirect()->to('admin/bank-accounts')->with('error', $e->getMessage()); }
        return redirect()->to('admin/bank-accounts')->with('success', 'Account deleted.');
    }

    public function adjust(string $unId)
    {
        if (! $this->validate(['new_balance' => 'required|numeric|greater_than_equal_to[0]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $account = $this->service->get($unId);
        if (! $account) return redirect()->to('admin/bank-accounts')->with('error', 'Account not found.');

        $newBalance = (float) $this->request->getPost('new_balance');
        $current    = (float) ($account['balance'] ?? 0);
        $diff       = $newBalance - $current;

        try {
            if ($diff > 0) {
                $this->service->adjustBalance($unId, abs($diff), 'credit');
            } elseif ($diff < 0) {
                $this->service->adjustBalance($unId, abs($diff), 'debit');
            }
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/bank-accounts/' . $unId)->with('success', 'Balance adjusted.');
    }
}
