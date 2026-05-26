<?php

namespace App\Models;

class NotificationModel extends BaseModel
{
    protected $table             = 'notifications';
    protected string $unIdPrefix = 'NTF';

    protected $allowedFields = [
        'un_id',
        'user_un_id',
        'type',
        'title',
        'body',
        'entity_type',
        'entity_un_id',
        'link',
        'read_at',
    ];

    protected $validationRules = [
        'type'  => 'required|max_length[80]',
        'title' => 'required|max_length[200]',
    ];

    /**
     * Count unread notifications for a user (or global broadcast).
     */
    public function countUnread(string $userUnId): int
    {
        return (int) $this
            ->where(function ($builder) use ($userUnId) {
                $builder->where('user_un_id', $userUnId)
                        ->orWhere('user_un_id', null);
            })
            ->where('read_at', null)
            ->where('deleted_at', null)
            ->countAllResults();
    }
}
