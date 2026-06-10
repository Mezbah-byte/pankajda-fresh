<?php

namespace App\Repositories;

use App\Models\CompanyTypeModel;

class CompanyTypeRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new CompanyTypeModel();
    }

    public function allActive(): array
    {
        return (new CompanyTypeModel())->allActive();
    }

    public function all(): array
    {
        return $this->model->where('deleted_at', null)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function names(): array
    {
        return array_column($this->allActive(), 'name');
    }
}
