<?php

namespace App\Services;

use App\Repositories\BankAccountRepository;

class BankAccountService extends BaseService
{
    private BankAccountRepository $repo;

    public function __construct(?BankAccountRepository $repo = null)
    {
        $this->repo = $repo ?? new BankAccountRepository();
    }

    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->repo->search($filters, $page, $perPage);
    }

    public function get(string $unId): ?array
    {
        return $this->repo->findByUnId($unId);
    }

    public function active(): array
    {
        return $this->repo->findActive();
    }

    public function create(array $input): array
    {
        $data = $this->normalize($input);
        $data['current_balance'] = $data['current_balance'] ?? $data['opening_balance'] ?? 0;
        $unId = $this->transaction(fn () => $this->repo->create($data));
        $this->audit('bank_account.created', 'bank_account', $unId, ['name' => $data['account_name'] ?? '']);
        return $this->repo->findByUnId($unId);
    }

    public function update(string $unId, array $input): array
    {
        if (! $this->repo->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Bank account not found.');
        }
        $data = $this->normalize($input);
        $this->transaction(fn () => $this->repo->updateByUnId($unId, $data));
        $this->audit('bank_account.updated', 'bank_account', $unId);
        return $this->repo->findByUnId($unId);
    }

    public function delete(string $unId): bool
    {
        if (! $this->repo->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Bank account not found.');
        }
        $result = $this->repo->deleteByUnId($unId);
        $this->audit('bank_account.deleted', 'bank_account', $unId);
        return $result;
    }

    public function adjustBalance(string $unId, float $amount, string $direction = 'credit'): void
    {
        $account = $this->repo->findByUnId($unId);
        if (! $account) {
            throw new \InvalidArgumentException('Bank account not found.');
        }
        $current = (float) $account['current_balance'];
        $new     = $direction === 'credit' ? $current + $amount : $current - $amount;
        if ($new < 0) {
            throw new \InvalidArgumentException('Insufficient balance in account.');
        }
        $this->repo->updateByUnId($unId, ['current_balance' => $new]);
    }

    private function normalize(array $input): array
    {
        return array_intersect_key($input, array_flip([
            'company_un_id', 'account_name', 'bank_name', 'account_number',
            'branch', 'routing_number', 'account_type', 'opening_balance',
            'current_balance', 'currency', 'status', 'notes',
        ]));
    }
}
