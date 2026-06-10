<?php

namespace App\Services;

use App\Repositories\CountryRepository;

class CountryService extends BaseService
{
    private CountryRepository $repo;

    public function __construct(?CountryRepository $repo = null)
    {
        $this->repo = $repo ?? new CountryRepository();
    }

    public function list(array $filters, int $page = 1, int $perPage = 30): array
    {
        return $this->repo->search($filters, $page, $perPage);
    }

    public function allActive(): array
    {
        return $this->repo->allActive();
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
            'iso_code'   => strtoupper(trim($input['iso_code'] ?? '')),
            'sort_order' => (int) ($input['sort_order'] ?? 0),
            'is_active'  => isset($input['is_active']) ? 1 : 0,
        ];
        if ($data['iso_code'] === '') {
            $data['iso_code'] = null;
        }
        $unId = $this->transaction(fn () => $this->repo->create($data));
        $this->audit('country.created', 'country', $unId, ['name' => $data['name']]);
        return $this->repo->findByUnId($unId);
    }

    public function update(string $unId, array $input): array
    {
        if (! $this->repo->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Country not found.');
        }
        $data = [
            'name'       => trim($input['name']),
            'iso_code'   => strtoupper(trim($input['iso_code'] ?? '')),
            'sort_order' => (int) ($input['sort_order'] ?? 0),
            'is_active'  => isset($input['is_active']) ? 1 : 0,
        ];
        if ($data['iso_code'] === '') {
            $data['iso_code'] = null;
        }
        $this->transaction(fn () => $this->repo->updateByUnId($unId, $data));
        $this->audit('country.updated', 'country', $unId);
        return $this->repo->findByUnId($unId);
    }

    public function delete(string $unId): void
    {
        if (! $this->repo->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Country not found.');
        }
        $this->transaction(fn () => $this->repo->deleteByUnId($unId));
        $this->audit('country.deleted', 'country', $unId);
    }
}
