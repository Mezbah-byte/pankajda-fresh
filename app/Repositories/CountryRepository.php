<?php

namespace App\Repositories;

use App\Models\CountryModel;

class CountryRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new CountryModel();
    }

    public function allActive(): array
    {
        return (new CountryModel())->allActive();
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

    public function search(array $filters, int $page = 1, int $perPage = 30): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $builder->like('name', $filters['q']);
            }
            if (isset($filters['is_active']) && $filters['is_active'] !== '') {
                $builder->where('is_active', (int) $filters['is_active']);
            }
        });
    }
}
