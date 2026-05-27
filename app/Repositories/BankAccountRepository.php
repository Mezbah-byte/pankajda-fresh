<?php

namespace App\Repositories;

use App\Models\BankAccountModel;

class BankAccountRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new BankAccountModel();
    }

    /**
     * Paginated search with optional filters.
     *
     * @param array $filters  Supports: q (account_name/bank_name/account_number), status, company_un_id
     */
    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $q = $filters['q'];
                $builder->groupStart()
                    ->like('account_name', $q)
                    ->orLike('bank_name', $q)
                    ->orLike('account_number', $q)
                    ->groupEnd();
            }
            if (! empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
            if (! empty($filters['company_un_id'])) {
                $builder->where('company_un_id', $filters['company_un_id']);
            }
        });
    }

    /**
     * Return all active bank accounts (for dropdown selects).
     */
    public function findActive(): array
    {
        return $this->model
            ->where('status', 'active')
            ->where('deleted_at', null)
            ->orderBy('account_name', 'ASC')
            ->findAll();
    }
}
