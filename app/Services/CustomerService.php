<?php

namespace App\Services;

use App\Repositories\CustomerRepository;

class CustomerService extends BaseService
{
    private CustomerRepository $customers;

    public function __construct(?CustomerRepository $customers = null)
    {
        $this->customers = $customers ?? new CustomerRepository();
    }

    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->customers->search($filters, $page, $perPage);
    }

    public function get(string $unId): ?array
    {
        return $this->customers->findByUnId($unId);
    }

    public function create(array $input): array
    {
        $data = $this->normalize($input);
        // current_due seeded from opening_balance for new customers
        $data['current_due'] = (float) ($data['opening_balance'] ?? 0);
        $unId = $this->transaction(fn () => $this->customers->create($data));
        $this->audit('customer.created', 'customer', $unId, ['name' => $data['customer_name']]);
        return $this->customers->findByUnId($unId);
    }

    public function update(string $unId, array $input): array
    {
        if (! $this->customers->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Customer not found.');
        }
        $data = $this->normalize($input);
        // never overwrite current_due via update — that's managed by sales/payments
        unset($data['current_due']);
        $this->transaction(fn () => $this->customers->updateByUnId($unId, $data));
        $this->audit('customer.updated', 'customer', $unId);
        return $this->customers->findByUnId($unId);
    }

    public function delete(string $unId): void
    {
        if (! $this->customers->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Customer not found.');
        }
        $this->transaction(fn () => $this->customers->deleteByUnId($unId));
        $this->audit('customer.deleted', 'customer', $unId);
    }

    public function totals(): array
    {
        return $this->customers->totals();
    }

    private function normalize(array $input): array
    {
        $whitelisted = [
            'company_un_id', 'customer_code', 'customer_name', 'phone',
            'email', 'address', 'city', 'opening_balance', 'credit_limit',
            'notes', 'status',
        ];
        $data = array_intersect_key($input, array_flip($whitelisted));
        if (isset($data['email'])) $data['email'] = strtolower(trim((string) $data['email']));
        if (isset($data['customer_name'])) $data['customer_name'] = trim((string) $data['customer_name']);
        return $data;
    }
}
