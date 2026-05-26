<?php

namespace App\Services;

use App\Repositories\NotificationRepository;

/**
 * NotificationService - create, list, and dismiss notifications.
 *
 * Usage from any service:
 *   service('notifications')->notify([
 *       'type'        => 'sale.created',
 *       'title'       => 'New Sale #SAL-xxx',
 *       'body'        => 'Customer Rahim placed a cash order of ৳ 5,000.',
 *       'entity_type' => 'sale',
 *       'entity_un_id'=> $saleUnId,
 *       'link'        => site_url('admin/sales/' . $saleUnId),
 *       // 'user_un_id' => '...'   omit = broadcast to all admins
 *   ]);
 */
class NotificationService extends BaseService
{
    private NotificationRepository $repo;

    public function __construct(?NotificationRepository $repo = null)
    {
        $this->repo = $repo ?? new NotificationRepository();
    }

    // ------------------------------------------------------------------
    // Write
    // ------------------------------------------------------------------

    /**
     * Create a notification.
     *
     * @param array{type:string,title:string,body?:string,user_un_id?:string,
     *              entity_type?:string,entity_un_id?:string,link?:string} $data
     */
    public function notify(array $data): string
    {
        $payload = array_intersect_key($data, array_flip([
            'user_un_id', 'type', 'title', 'body',
            'entity_type', 'entity_un_id', 'link',
        ]));
        return $this->repo->create($payload);
    }

    /**
     * Mark one notification as read (by un_id).
     */
    public function markRead(string $unId): bool
    {
        return $this->repo->markRead($unId);
    }

    /**
     * Mark all unread notifications for a user as read.
     */
    public function markAllRead(string $userUnId): int
    {
        return $this->repo->markAllRead($userUnId);
    }

    /**
     * Soft-delete a notification.
     */
    public function dismiss(string $unId): bool
    {
        return $this->repo->deleteByUnId($unId);
    }

    // ------------------------------------------------------------------
    // Read
    // ------------------------------------------------------------------

    public function forUser(string $userUnId, int $page = 1, int $perPage = 20): array
    {
        return $this->repo->forUser($userUnId, $page, $perPage);
    }

    public function unread(string $userUnId, int $limit = 10): array
    {
        return $this->repo->unread($userUnId, $limit);
    }

    public function countUnread(string $userUnId): int
    {
        return $this->repo->countUnread($userUnId);
    }

    public function get(string $unId): ?array
    {
        return $this->repo->findByUnId($unId);
    }
}
