<?php

namespace App\Services;

use App\Repositories\EmployeeRepository;

class EmployeeService extends BaseService
{
    private EmployeeRepository $employees;

    public function __construct(?EmployeeRepository $employees = null)
    {
        $this->employees = $employees ?? new EmployeeRepository();
    }

    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->employees->search($filters, $page, $perPage);
    }

    public function get(string $unId): ?array
    {
        return $this->employees->findByUnId($unId);
    }

    public function create(array $input): array
    {
        $data = $this->normalize($input);
        $unId = $this->transaction(fn () => $this->employees->create($data));
        $this->audit('employee.created', 'employee', $unId, ['name' => $data['name'] ?? '']);
        return $this->employees->findByUnId($unId);
    }

    public function update(string $unId, array $input): array
    {
        if (! $this->employees->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Employee not found.');
        }
        $data = $this->normalize($input);
        $this->transaction(fn () => $this->employees->updateByUnId($unId, $data));
        $this->audit('employee.updated', 'employee', $unId);
        return $this->employees->findByUnId($unId);
    }

    public function delete(string $unId): void
    {
        if (! $this->employees->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Employee not found.');
        }
        $this->transaction(fn () => $this->employees->deleteByUnId($unId));
        $this->audit('employee.deleted', 'employee', $unId);
    }

    public function totals(): array
    {
        return $this->employees->totals();
    }

    private function normalize(array $input): array
    {
        $whitelisted = [
            'company_un_id', 'employee_code', 'name', 'designation',
            'department', 'phone', 'email', 'national_id', 'address',
            'salary', 'joined_at', 'status', 'photo_path', 'notes',
        ];
        $data = array_intersect_key($input, array_flip($whitelisted));
        if (isset($data['email'])) $data['email'] = strtolower(trim((string) $data['email']));
        if (isset($data['name']))  $data['name']  = trim((string) $data['name']);
        return $data;
    }
}
