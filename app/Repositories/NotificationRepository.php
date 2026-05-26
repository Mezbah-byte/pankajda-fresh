<?php

namespace App\Repositories;

use App\Models\NotificationModel;

class NotificationRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new NotificationModel();
    }

    /**
     * Paginated list for a specific user (includes broadcasts).
     */
    public function forUser(string $userUnId, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($userUnId) {
            $builder->groupStart()
                        ->where('user_un_id', $userUnId)
                        ->orWhere('user_un_id', null)
                    ->groupEnd();
        });
    }

    /**
     * Unread notifications for a user (includes broadcasts).
     */
    public function unread(string $userUnId, int $limit = 10): array
    {
        return $this->model
            ->where(function ($builder) use ($userUnId) {
                $builder->groupStart()
                            ->where('user_un_id', $userUnId)
                            ->orWhere('user_un_id', null)
                        ->groupEnd();
            })
            ->where('read_at', null)
            ->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Count unread for a user.
     */
    public function countUnread(string $userUnId): int
    {
        /** @var NotificationModel $model */
        $model = $this->model;
        return $model->countUnread($userUnId);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(string $unId): bool
    {
        return $this->updateByUnId($unId, ['read_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Mark all unread notifications for a user as read.
     */
    public function markAllRead(string $userUnId): int
    {
        return $this->model
            ->where(function ($builder) use ($userUnId) {
                $builder->groupStart()
                            ->where('user_un_id', $userUnId)
                            ->orWhere('user_un_id', null)
                        ->groupEnd();
            })
            ->where('read_at', null)
            ->set(['read_at' => date('Y-m-d H:i:s')])
            ->update();
    }
}
