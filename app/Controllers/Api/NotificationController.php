<?php

namespace App\Controllers\Api;

use App\Services\NotificationService;

/**
 * REST endpoints for the notification feed.
 *
 * GET    /api/v1/notifications           → paginated list for auth user
 * GET    /api/v1/notifications/unread    → up to 20 unread
 * GET    /api/v1/notifications/count     → {"unread": N}
 * PUT    /api/v1/notifications/{un_id}/read   → mark one read
 * PUT    /api/v1/notifications/read-all  → mark all read
 * DELETE /api/v1/notifications/{un_id}   → dismiss
 */
class NotificationController extends BaseApiController
{
    private NotificationService $service;

    public function __construct()
    {
        $this->service = new NotificationService();
    }

    public function index()
    {
        $userUnId = $this->authUserUnId();
        if (! $userUnId) {
            return $this->failUnauthorized();
        }
        $pg = $this->parsePagination();
        $result = $this->service->forUser($userUnId, $pg['page'], $pg['per_page']);
        return $this->paginated($result['items'], $result['page'], $result['per_page'], $result['total']);
    }

    public function unread()
    {
        $userUnId = $this->authUserUnId();
        if (! $userUnId) {
            return $this->failUnauthorized();
        }
        return $this->ok($this->service->unread($userUnId, 20));
    }

    public function count()
    {
        $userUnId = $this->authUserUnId();
        if (! $userUnId) {
            return $this->failUnauthorized();
        }
        return $this->ok(['unread' => $this->service->countUnread($userUnId)]);
    }

    public function markRead(string $unId)
    {
        $n = $this->service->get($unId);
        if (! $n) {
            return $this->failNotFound('Notification not found.');
        }
        $this->service->markRead($unId);
        return $this->ok(['un_id' => $unId, 'read' => true]);
    }

    public function markAllRead()
    {
        $userUnId = $this->authUserUnId();
        if (! $userUnId) {
            return $this->failUnauthorized();
        }
        $count = $this->service->markAllRead($userUnId);
        return $this->ok(['marked_read' => $count]);
    }

    public function delete(string $unId)
    {
        $n = $this->service->get($unId);
        if (! $n) {
            return $this->failNotFound('Notification not found.');
        }
        $this->service->dismiss($unId);
        return $this->ok(['dismissed' => true]);
    }
}
