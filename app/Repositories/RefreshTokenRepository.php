<?php

namespace App\Repositories;

use App\Models\RefreshTokenModel;

class RefreshTokenRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new RefreshTokenModel();
    }

    public function store(string $userUnId, string $rawToken, int $ttlSeconds): void
    {
        $this->model->insert([
            'user_un_id' => $userUnId,
            'token_hash' => hash('sha256', $rawToken),
            'expires_at' => date('Y-m-d H:i:s', time() + $ttlSeconds),
        ]);
    }

    public function isValid(string $rawToken): bool
    {
        $row = $this->model->where('token_hash', hash('sha256', $rawToken))
            ->where('revoked_at', null)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->first();
        return (bool) $row;
    }

    public function revoke(string $rawToken): void
    {
        $this->model->where('token_hash', hash('sha256', $rawToken))
            ->set(['revoked_at' => date('Y-m-d H:i:s')])
            ->update();
    }

    public function revokeAllForUser(string $userUnId): void
    {
        $this->model->where('user_un_id', $userUnId)
            ->where('revoked_at', null)
            ->set(['revoked_at' => date('Y-m-d H:i:s')])
            ->update();
    }
}
