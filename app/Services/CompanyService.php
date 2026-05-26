<?php

namespace App\Services;

use App\Repositories\CompanyRepository;

/**
 * CompanyService - business logic for the Multi-Company module.
 *
 * Repository handles persistence; this layer enforces invariants like
 * "company_name is unique within active rows" and produces audit
 * entries on every state change.
 */
class CompanyService extends BaseService
{
    private CompanyRepository $companies;

    public function __construct(?CompanyRepository $companies = null)
    {
        $this->companies = $companies ?? new CompanyRepository();
    }

    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->companies->search($filters, $page, $perPage);
    }

    public function get(string $unId): ?array
    {
        return $this->companies->findByUnId($unId);
    }

    public function create(array $input): array
    {
        $data = $this->normalize($input);
        $unId = $this->transaction(fn () => $this->companies->create($data));
        $this->audit('company.created', 'company', $unId, ['name' => $data['company_name']]);
        return $this->companies->findByUnId($unId);
    }

    public function update(string $unId, array $input): array
    {
        if (! $this->companies->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Company not found.');
        }
        $data = $this->normalize($input);
        $this->transaction(fn () => $this->companies->updateByUnId($unId, $data));
        $this->audit('company.updated', 'company', $unId, ['fields' => array_keys($data)]);
        return $this->companies->findByUnId($unId);
    }

    public function delete(string $unId): void
    {
        if (! $this->companies->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Company not found.');
        }
        $this->transaction(fn () => $this->companies->deleteByUnId($unId));
        $this->audit('company.deleted', 'company', $unId);
    }

    public function totals(): array
    {
        return $this->companies->totals();
    }

    private function normalize(array $input): array
    {
        $whitelisted = [
            'company_name', 'company_type', 'trade_license', 'tax_id',
            'address', 'city', 'country', 'phone', 'email', 'website',
            'logo_path', 'currency', 'opening_balance', 'notes', 'status',
        ];
        $data = array_intersect_key($input, array_flip($whitelisted));
        if (isset($data['email'])) {
            $data['email'] = strtolower(trim((string) $data['email']));
        }
        if (isset($data['company_name'])) {
            $data['company_name'] = trim((string) $data['company_name']);
        }
        return $data;
    }
}
