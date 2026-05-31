<?php

namespace App\Controllers\Api;

use Config\Database;

class ActivityLogController extends BaseApiController
{
    public function index()
    {
        $pg      = $this->parsePagination();
        $filters = [
            'action'      => $this->request->getGet('action'),
            'entity_type' => $this->request->getGet('entity_type'),
            'user_un_id'  => $this->request->getGet('user_un_id'),
            'date_from'   => $this->request->getGet('date_from'),
            'date_to'     => $this->request->getGet('date_to'),
        ];

        $db      = Database::connect();
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
        $offset = ($pg['page'] - 1) * $pg['per_page'];
        $items  = $builder->orderBy('id', 'DESC')->limit($pg['per_page'], $offset)->get()->getResultArray();

        return $this->paginated($items, $pg['page'], $pg['per_page'], $total);
    }

    public function entityTypes()
    {
        $db    = Database::connect();
        $rows  = $db->table('activity_logs')
            ->select('entity_type')
            ->distinct()
            ->where('entity_type IS NOT NULL', null, false)
            ->orderBy('entity_type', 'ASC')
            ->get()
            ->getResultArray();

        return $this->ok(array_column($rows, 'entity_type'), 'Entity types');
    }
}
