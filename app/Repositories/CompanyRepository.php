<?php

namespace App\Repositories;

use App\Models\CompanyModel;

class CompanyRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new CompanyModel();
    }

    /**
     * Search/filter list with pagination.
     */
    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $q = $filters['q'];
                $builder->groupStart()
                    ->like('company_name', $q)
                    ->orLike('email', $q)
                    ->orLike('phone', $q)
                    ->orLike('trade_license', $q)
                    ->groupEnd();
            }
            if (! empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
            if (! empty($filters['company_type'])) {
                $builder->where('company_type', $filters['company_type']);
            }
        });
    }

    public function totals(): array
    {
        $total    = $this->model->where('deleted_at', null)->countAllResults(false);
        $active   = $this->model->where('status', STATUS_ACTIVE)->where('deleted_at', null)->countAllResults(false);
        $inactive = $this->model->where('status', STATUS_INACTIVE)->where('deleted_at', null)->countAllResults();
        return compact('total', 'active', 'inactive');
    }
}
