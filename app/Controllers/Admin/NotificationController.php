<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\NotificationService;

/**
 * Admin notification actions (called via AJAX from the topbar bell).
 *
 * POST /admin/notifications/{un_id}/read     → mark one as read (JSON)
 * POST /admin/notifications/read-all         → mark all as read (JSON)
 * POST /admin/notifications/{un_id}/dismiss  → soft-delete (JSON)
 * GET  /admin/notifications                  → full list page
 */
class NotificationController extends BaseController
{
    private NotificationService $service;

    public function __construct()
    {
        $this->service = new NotificationService();
    }

    /** Full-page notification centre. */
    public function index()
    {
        $userUnId = session('user_un_id') ?? '';
        $page     = max(1, (int) ($this->request->getGet('page') ?? 1));
        $result   = $this->service->forUser($userUnId, $page, 25);

        return view('admin/notifications/index', [
            'title'         => 'Notifications',
            'notifications' => $result['items'],
            'pagination'    => [
                'page'      => $result['page'],
                'per_page'  => $result['per_page'],
                'total'     => $result['total'],
                'last_page' => max(1, (int) ceil($result['total'] / max(1, $result['per_page']))),
            ],
        ]);
    }

    /** AJAX: mark one notification read. Returns JSON. */
    public function markRead(string $unId)
    {
        $this->service->markRead($unId);
        return $this->response->setJSON(['success' => true]);
    }

    /** AJAX: mark all notifications read. Returns JSON. */
    public function markAllRead()
    {
        $userUnId = session('user_un_id') ?? '';
        $count    = $this->service->markAllRead($userUnId);
        return $this->response->setJSON(['success' => true, 'count' => $count]);
    }

    /** AJAX: dismiss (soft-delete) a notification. Returns JSON. */
    public function dismiss(string $unId)
    {
        $this->service->dismiss($unId);
        return $this->response->setJSON(['success' => true]);
    }
}
