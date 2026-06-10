<?php

namespace App\Repositories;

use App\Models\ContainerCartonModel;

class ContainerCartonRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new ContainerCartonModel();
    }

    public function forContainer(string $containerUnId): array
    {
        return $this->model
            ->where('container_un_id', $containerUnId)
            ->where('deleted_at', null)
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function forContainerBulk(array $containerUnIds): array
    {
        if (empty($containerUnIds)) return [];
        return $this->model
            ->whereIn('container_un_id', $containerUnIds)
            ->where('deleted_at', null)
            ->orderBy('container_un_id', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function totalQuantity(string $containerUnId): float
    {
        $row = $this->model->builder()
            ->select('SUM(quantity) AS total', false)
            ->where('container_un_id', $containerUnId)
            ->where('deleted_at', null)
            ->get()->getRowArray();
        return (float) ($row['total'] ?? 0);
    }

    public function damagedCount(string $containerUnId): int
    {
        return (int) $this->model
            ->where('container_un_id', $containerUnId)
            ->where('condition', 'damaged')
            ->where('deleted_at', null)
            ->countAllResults();
    }
}
