<?php

namespace App\Repositories;

use App\Models\UserModel;

class UserRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new UserModel();
    }

    public function findByEmail(string $email): ?array
    {
        $row = $this->model->where('email', strtolower(trim($email)))->first();
        return $row ?: null;
    }

    public function emailExists(string $email): bool
    {
        return $this->model->where('email', strtolower(trim($email)))->countAllResults() > 0;
    }

    public function touchLastLogin(string $unId): void
    {
        $this->model->where('un_id', $unId)
            ->set(['last_login_at' => date('Y-m-d H:i:s')])
            ->update();
    }
}
