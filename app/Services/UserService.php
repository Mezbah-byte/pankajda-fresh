<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService extends BaseService
{
    private UserRepository $users;

    public function __construct(?UserRepository $users = null)
    {
        $this->users = $users ?? new UserRepository();
    }

    /**
     * Paginated list of users with optional filters.
     */
    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        $result = $this->users->search($filters, $page, $perPage);
        $result['items'] = array_map([$this, 'stripPassword'], $result['items']);
        return $result;
    }

    /**
     * Get a single user by un_id. Returns null if not found.
     */
    public function get(string $unId): ?array
    {
        $user = $this->users->findByUnId($unId);
        if ($user === null) {
            return null;
        }
        return $this->stripPassword($user);
    }

    /**
     * Create a new user. Hashes password automatically.
     */
    public function create(array $input): array
    {
        $data = $this->normalize($input);
        $data['email']         = strtolower(trim($data['email'] ?? ''));
        $data['password_hash'] = password_hash($input['password'], PASSWORD_DEFAULT);
        $data['status']        = $data['status'] ?? 'active';

        $unId = $this->transaction(fn () => $this->users->create($data));

        $this->audit('user.created', 'user', $unId, [
            'name'  => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'role'  => $data['role'] ?? '',
        ]);

        return $this->stripPassword($this->users->findByUnId($unId));
    }

    /**
     * Update user profile fields. Password is NOT updated here.
     */
    public function update(string $unId, array $input): array
    {
        if (! $this->users->existsByUnId($unId)) {
            throw new \InvalidArgumentException('User not found.');
        }

        $data          = $this->normalize($input);
        $data['email'] = strtolower(trim($data['email'] ?? ''));
        // Never allow password_hash to be set through update()
        unset($data['password_hash'], $data['password']);

        $this->transaction(fn () => $this->users->updateByUnId($unId, $data));

        $this->audit('user.updated', 'user', $unId, [
            'name'  => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
        ]);

        return $this->stripPassword($this->users->findByUnId($unId));
    }

    /**
     * Soft-delete a user.
     */
    public function delete(string $unId): void
    {
        if (! $this->users->existsByUnId($unId)) {
            throw new \InvalidArgumentException('User not found.');
        }

        $this->transaction(fn () => $this->users->deleteByUnId($unId));
        $this->audit('user.deleted', 'user', $unId);
    }

    /**
     * Change a user's password (separate from update).
     */
    public function changePassword(string $unId, string $newPassword): void
    {
        if (! $this->users->existsByUnId($unId)) {
            throw new \InvalidArgumentException('User not found.');
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->transaction(fn () => $this->users->updateByUnId($unId, ['password_hash' => $hash]));
        $this->audit('user.password_changed', 'user', $unId);
    }

    /**
     * Strip password_hash from a user row before returning to controllers.
     */
    private function stripPassword(array $user): array
    {
        unset($user['password_hash']);
        return $user;
    }

    /**
     * Whitelist allowed fields for insert/update.
     */
    private function normalize(array $input): array
    {
        $whitelisted = ['name', 'email', 'role', 'status', 'avatar', 'avatar_path', 'phone'];
        return array_intersect_key($input, array_flip($whitelisted));
    }
}
