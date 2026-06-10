<?php

namespace App\Repositories;

use App\Models\FarmActivityModel;
use App\Models\FarmProjectModel;
use Config\Database;

class FarmProjectRepository extends BaseRepository
{
    private FarmActivityModel $activities;

    protected function bootModel(): void
    {
        $this->model      = new FarmProjectModel();
        $this->activities = new FarmActivityModel();
    }

    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $q = $filters['q'];
                $builder->groupStart()
                    ->like('project_name', $q)
                    ->orLike('item_name', $q)
                    ->groupEnd();
            }
            if (! empty($filters['company_un_id'])) {
                $builder->where('company_un_id', $filters['company_un_id']);
            }
            if (! empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
        });
    }

    public function activitiesFor(string $projectUnId): array
    {
        return $this->activities->where('farm_project_un_id', $projectUnId)
            ->where('deleted_at', null)
            ->orderBy('activity_date', 'DESC')
            ->findAll();
    }

    public function addActivity(array $data): string
    {
        $id = $this->activities->insert($data, true);
        if ($id === false) {
            throw new \RuntimeException('Failed to add activity.');
        }
        return $this->activities->find($id)['un_id'];
    }

    public function totalActivityCost(string $projectUnId): float
    {
        $row = $this->activities->builder()
            ->selectSum('rate', 'total')
            ->where('farm_project_un_id', $projectUnId)
            ->where('deleted_at', null)
            ->get()->getRowArray();
        return (float) ($row['total'] ?? 0);
    }

    public function totals(): array
    {
        $db = Database::connect();
        $count   = $this->model->where('deleted_at', null)->countAllResults(false);
        $row = $db->table('farm_projects')
            ->selectSum('total_rate', 'cost')
            ->selectSum('sale_amount', 'sale')
            ->selectSum('profit', 'profit')
            ->where('deleted_at', null)->get()->getRowArray();
        return [
            'count'  => $count,
            'cost'   => (float) ($row['cost'] ?? 0),
            'sale'   => (float) ($row['sale'] ?? 0),
            'profit' => (float) ($row['profit'] ?? 0),
        ];
    }
}
