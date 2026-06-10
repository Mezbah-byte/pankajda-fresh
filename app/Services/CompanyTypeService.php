<?php

namespace App\Services;

use App\Repositories\CompanyTypeRepository;

class CompanyTypeService extends BaseService
{
    private CompanyTypeRepository $repo;

    public function __construct(?CompanyTypeRepository $repo = null)
    {
        $this->repo = $repo ?? new CompanyTypeRepository();
    }

    public function all(): array
    {
        return $this->repo->all();
    }

    public function names(): array
    {
        return $this->repo->names();
    }

    public function get(string $unId): ?array
    {
        return $this->repo->findByUnId($unId);
    }

    public function create(array $input): array
    {
        $data = [
            'name'       => trim($input['name']),
            'sort_order' => (int) ($input['sort_order'] ?? 0),
            'is_active'  => !empty($input['is_active']) ? 1 : 0,
        ];
        $unId = $this->transaction(fn () => $this->repo->create($data));
        $this->audit('company_type.created', 'company_type', $unId, ['name' => $data['name']]);
        return $this->repo->findByUnId($unId);
    }

    public function update(string $unId, array $input): array
    {
        if (! $this->repo->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Company type not found.');
        }
        $data = [
            'name'       => trim($input['name']),
            'sort_order' => (int) ($input['sort_order'] ?? 0),
            'is_active'  => !empty($input['is_active']) ? 1 : 0,
        ];
        $this->transaction(fn () => $this->repo->updateByUnId($unId, $data));
        $this->audit('company_type.updated', 'company_type', $unId);
        return $this->repo->findByUnId($unId);
    }

    public function delete(string $unId): void
    {
        if (! $this->repo->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Company type not found.');
        }
        $this->transaction(fn () => $this->repo->deleteByUnId($unId));
        $this->audit('company_type.deleted', 'company_type', $unId);
    }
}
