<?php

namespace App\Repositories;

use App\Models\UserModel;

class UserRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new UserModel();
    }

    /**
     * Paginated search with optional filters.
     */
    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $builder->groupStart()
                    ->like('name', $filters['q'])
                    ->orLike('email', $filters['q'])
                    ->groupEnd();
            }
            if (! empty($filters['role'])) {
                $builder->where('role', $filters['role']);
            }
            if (! empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
        });
    }

    /**
     * Find a user by email address (case-insensitive).
     */
    public function findByEmail(string $email): ?array
    {
        $row = $this->model->where('email', strtolower(trim($email)))->first();
        return $row ?: null;
    }

    /**
     * Check whether an email already exists, optionally excluding a given user.
     */
    public function emailExists(string $email, ?string $excludeUnId = null): bool
    {
        $query = $this->model->where('email', strtolower(trim($email)))->where('deleted_at', null);
        if ($excludeUnId !== null) {
            $query->where('un_id !=', $excludeUnId);
        }
        return $query->countAllResults() > 0;
    }

    /**
     * Update the last_login_at timestamp for a user.
     */
    public function touchLastLogin(string $unId): void
    {
        $this->model
            ->where('un_id', $unId)
            ->set(['last_login_at' => date('Y-m-d H:i:s')])
            ->update();
    }
}
