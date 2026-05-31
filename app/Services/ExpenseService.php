<?php

namespace App\Services;

use App\Repositories\ExpenseRepository;
use App\Services\BankAccountService;

class ExpenseService extends BaseService
{
    private ExpenseRepository  $expenses;
    private BankAccountService $bankAccounts;

    public function __construct(?ExpenseRepository $expenses = null, ?BankAccountService $bankAccounts = null)
    {
        $this->expenses     = $expenses     ?? new ExpenseRepository();
        $this->bankAccounts = $bankAccounts ?? new BankAccountService();
    }

    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->expenses->search($filters, $page, $perPage);
    }

    public function get(string $unId): ?array
    {
        return $this->expenses->findByUnId($unId);
    }

    public function create(array $input): array
    {
        $data   = $this->normalize($input);
        $data['created_by_un_id'] = session('user_un_id')
            ?? (service('request')->auth_user['un_id'] ?? null);
        $amount = (float) ($data['amount'] ?? 0);

        $unId = $this->transaction(function () use ($data, $amount) {
            $id = $this->expenses->create($data);
            if (! empty($data['bank_account_un_id']) && $amount > 0) {
                $this->bankAccounts->adjustBalance($data['bank_account_un_id'], $amount, 'debit');
            }
            return $id;
        });

        $this->audit('expense.created', 'expense', $unId, [
            'title'  => $data['expense_title'] ?? '',
            'amount' => $amount,
        ]);
        return $this->expenses->findByUnId($unId);
    }

    public function update(string $unId, array $input): array
    {
        if (! $this->expenses->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Expense not found.');
        }
        $data = $this->normalize($input);
        $this->transaction(fn () => $this->expenses->updateByUnId($unId, $data));
        $this->audit('expense.updated', 'expense', $unId);
        return $this->expenses->findByUnId($unId);
    }

    public function delete(string $unId): void
    {
        $expense = $this->expenses->findByUnId($unId);
        if (! $expense) {
            throw new \InvalidArgumentException('Expense not found.');
        }

        $bankUnId = $expense['bank_account_un_id'] ?? null;
        $amount   = (float) ($expense['amount'] ?? 0);

        $this->transaction(function () use ($unId, $bankUnId, $amount) {
            $this->expenses->deleteByUnId($unId);
            if ($bankUnId && $amount > 0) {
                $this->bankAccounts->adjustBalance($bankUnId, $amount, 'credit');
            }
        });

        $this->audit('expense.deleted', 'expense', $unId);
    }

    public function categories(): array
    {
        return $this->expenses->categories();
    }

    public function totals(): array
    {
        return $this->expenses->totals();
    }

    public function byCategory(): array
    {
        return $this->expenses->byCategory();
    }

    private function normalize(array $input): array
    {
        $whitelisted = [
            'company_un_id', 'container_un_id', 'expense_title', 'category', 'amount',
            'expense_date', 'payment_method', 'bank_account_un_id', 'reference_no',
            'notes', 'attachment_path',
        ];
        return array_intersect_key($input, array_flip($whitelisted));
    }
}
