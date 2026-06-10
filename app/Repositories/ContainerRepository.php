<?php

namespace App\Repositories;

use App\Models\ContainerModel;
use Config\Database;

class ContainerRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new ContainerModel();
    }

    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $q = $filters['q'];
                $builder->groupStart()
                    ->like('container_number', $q)
                    ->orLike('bl_number', $q)
                    ->orLike('product_name', $q)
                    ->groupEnd();
            }
            if (! empty($filters['company_un_id'])) {
                $builder->where('company_un_id', $filters['company_un_id']);
            }
            if (! empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
            if (! empty($filters['customs_status'])) {
                $builder->where('customs_status', $filters['customs_status']);
            }
        });
    }

    public function listGroupedByCompany(array $filters): array
    {
        $db = Database::connect();
        $builder = $db->table('containers c')
            ->select('c.*, co.company_name')
            ->join('companies co', 'co.un_id = c.company_un_id', 'left')
            ->where('c.deleted_at', null);

        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $builder->groupStart()
                ->like('c.container_number', $q)
                ->orLike('c.bl_number', $q)
                ->orLike('c.product_name', $q)
                ->orLike('co.company_name', $q)
                ->groupEnd();
        }
        if (! empty($filters['customs_status'])) {
            $builder->where('c.customs_status', $filters['customs_status']);
        }
        if (! empty($filters['status'])) {
            $builder->where('c.status', $filters['status']);
        }

        $containers = $builder
            ->orderBy('co.company_name', 'ASC')
            ->orderBy('c.id', 'DESC')
            ->get()->getResultArray();

        $grouped = [];
        $serial  = [];
        foreach ($containers as $c) {
            $key = $c['company_un_id'] ?? '__none__';
            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'company_un_id' => $c['company_un_id'],
                    'company_name'  => $c['company_name'] ?? 'No Supplier',
                    'containers'    => [],
                ];
                $serial[$key] = 0;
            }
            $serial[$key]++;
            $c['serial'] = $serial[$key];
            $grouped[$key]['containers'][] = $c;
        }

        return array_values($grouped);
    }

    public function totalSold(string $containerUnId): float
    {
        $db  = Database::connect();
        $row = $db->table('sales')->selectSum('total_amount', 'total')
            ->where('container_un_id', $containerUnId)
            ->where('deleted_at', null)
            ->get()->getRowArray();
        return (float) ($row['total'] ?? 0);
    }

    public function totalExpenses(string $containerUnId): float
    {
        $db  = Database::connect();
        $row = $db->table('expenses')->selectSum('amount', 'total')
            ->where('container_un_id', $containerUnId)
            ->where('deleted_at', null)
            ->get()->getRowArray();
        return (float) ($row['total'] ?? 0);
    }

    public function totals(): array
    {
        $db = Database::connect();
        $count    = $this->model->where('deleted_at', null)->countAllResults(false);
        $cleared  = $this->model->where('customs_status', 'cleared')->where('deleted_at', null)->countAllResults();
        $row = $db->table('containers')->selectSum('cost_total', 'cost')
            ->where('deleted_at', null)->get()->getRowArray();
        return [
            'count'   => $count,
            'cleared' => $cleared,
            'cost'    => (float) ($row['cost'] ?? 0),
        ];
    }
}
