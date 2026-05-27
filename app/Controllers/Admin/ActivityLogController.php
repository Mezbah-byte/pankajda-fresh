<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Database;

class ActivityLogController extends BaseController
{
    public function index()
    {
        $db      = Database::connect();
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 25;
        $filters = [
            'action'      => $this->request->getGet('action'),
            'entity_type' => $this->request->getGet('entity_type'),
            'user_un_id'  => $this->request->getGet('user_un_id'),
            'date_from'   => $this->request->getGet('date_from'),
            'date_to'     => $this->request->getGet('date_to'),
        ];

        $builder = $db->table('activity_logs');

        if (! empty($filters['action'])) {
            $builder->like('action', $filters['action']);
        }
        if (! empty($filters['entity_type'])) {
            $builder->where('entity_type', $filters['entity_type']);
        }
        if (! empty($filters['user_un_id'])) {
            $builder->where('user_un_id', $filters['user_un_id']);
        }
        if (! empty($filters['date_from'])) {
            $builder->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        if (! empty($filters['date_to'])) {
            $builder->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        $total  = (clone $builder)->countAllResults(false);
        $offset = ($page - 1) * $perPage;
        $logs   = $builder->orderBy('id', 'DESC')->limit($perPage, $offset)->get()->getResultArray();

        // Users for filter dropdown
        $users = $db->table('users')
            ->select('un_id, name')
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Distinct entity types
        $entityTypes = $db->table('activity_logs')
            ->select('entity_type')
            ->distinct()
            ->where('entity_type IS NOT NULL', null, false)
            ->orderBy('entity_type', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/activity_logs/index', [
            'title'       => 'Activity Logs',
            'logs'        => $logs,
            'users'       => $users,
            'entityTypes' => array_column($entityTypes, 'entity_type'),
            'filters'     => $filters,
            'pagination'  => [
                'page'      => $page,
                'per_page'  => $perPage,
                'total'     => $total,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }
}
