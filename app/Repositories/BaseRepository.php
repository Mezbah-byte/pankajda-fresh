<?php

namespace App\Repositories;

use App\Models\BaseModel;

/**
 * BaseRepository - thin layer above the Model.
 *
 * Repositories expose a stable, business-oriented API that hides the
 * underlying ORM. Services depend on repositories (not models), so
 * we can switch the storage backend (DB, cache, third-party API) per
 * method without touching service code.
 */
abstract class BaseRepository
{
    protected BaseModel $model;

    /**
     * Subclasses must set $this->model in their constructor.
     */
    abstract protected function bootModel(): void;

    public function __construct()
    {
        $this->bootModel();
    }

    public function findByUnId(string $unId): ?array
    {
        return $this->model->findByUnId($unId);
    }

    public function existsByUnId(string $unId): bool
    {
        return $this->model->where('un_id', $unId)->countAllResults() > 0;
    }

    public function idFromUnId(string $unId): ?int
    {
        return $this->model->idFromUnId($unId);
    }

    /**
     * Insert and return the new public un_id.
     *
     * @throws \RuntimeException on failure
     */
    public function create(array $data): string
    {
        $id = $this->model->insert($data, true);
        if ($id === false) {
            throw new \RuntimeException('Insert failed: ' . implode(', ', $this->model->errors()));
        }
        $row = $this->model->find($id);
        return $row['un_id'] ?? '';
    }

    public function updateByUnId(string $unId, array $data): bool
    {
        $id = $this->model->idFromUnId($unId);
        if ($id === null) {
            return false;
        }
        return (bool) $this->model->update($id, $data);
    }

    public function deleteByUnId(string $unId): bool
    {
        return $this->model->deleteByUnId($unId);
    }

    /**
     * Paginated list with optional filter callback applied to the
     * builder before counting / fetching. Returns:
     *   ['items' => [], 'total' => N, 'page' => P, 'per_page' => PP]
     */
    public function paginate(int $page = 1, int $perPage = 20, ?callable $filter = null): array
    {
        $builder = $this->model->builder();
        // soft-delete filter (the builder is already scoped to this model's table)
        $builder->where('deleted_at', null);
        if ($filter) {
            $filter($builder);
        }

        $total = (clone $builder)->countAllResults(false);

        $offset = max(0, ($page - 1) * $perPage);
        $items = $builder
            ->orderBy('id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }

    public function getModel(): BaseModel
    {
        return $this->model;
    }
}
