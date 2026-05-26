<?php

namespace App\Services;

use App\Repositories\FarmProjectRepository;

/**
 * FarmProjectService - manages farm projects + activities (workers, seeds, cost).
 *
 * Profit is recomputed as sale_amount - total_cost whenever a project is
 * saved or an activity is added (since activities contribute to total_cost).
 */
class FarmProjectService extends BaseService
{
    private FarmProjectRepository $projects;

    public function __construct(?FarmProjectRepository $projects = null)
    {
        $this->projects = $projects ?? new FarmProjectRepository();
    }

    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->projects->search($filters, $page, $perPage);
    }

    public function get(string $unId): ?array
    {
        $project = $this->projects->findByUnId($unId);
        if ($project) {
            $project['activities'] = $this->projects->activitiesFor($unId);
        }
        return $project;
    }

    public function create(array $input): array
    {
        $data = $this->normalize($input);
        $data['profit'] = (float) ($data['sale_amount'] ?? 0) - (float) ($data['total_cost'] ?? 0);
        $unId = $this->transaction(fn () => $this->projects->create($data));
        $this->audit('farm_project.created', 'farm_project', $unId, ['project' => $data['project_name'] ?? '']);
        return $this->projects->findByUnId($unId);
    }

    public function update(string $unId, array $input): array
    {
        $existing = $this->projects->findByUnId($unId);
        if (! $existing) {
            throw new \InvalidArgumentException('Farm project not found.');
        }
        $data = $this->normalize($input);
        $sale = (float) ($data['sale_amount'] ?? $existing['sale_amount']);
        $cost = (float) ($data['total_cost']  ?? $existing['total_cost']);
        $data['profit'] = $sale - $cost;
        $this->transaction(fn () => $this->projects->updateByUnId($unId, $data));
        $this->audit('farm_project.updated', 'farm_project', $unId);
        return $this->projects->findByUnId($unId);
    }

    public function delete(string $unId): void
    {
        if (! $this->projects->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Farm project not found.');
        }
        $this->transaction(fn () => $this->projects->deleteByUnId($unId));
        $this->audit('farm_project.deleted', 'farm_project', $unId);
    }

    /**
     * Add an activity (workers, seeds, cost). Recomputes the project's
     * total_cost and profit afterward.
     */
    public function addActivity(string $projectUnId, array $payload): array
    {
        $project = $this->projects->findByUnId($projectUnId);
        if (! $project) {
            throw new \InvalidArgumentException('Farm project not found.');
        }
        $payload['farm_project_un_id'] = $projectUnId;
        $payload['activity_date']      = $payload['activity_date'] ?? date('Y-m-d');
        $payload['cost']               = (float) ($payload['cost'] ?? 0);

        return $this->transaction(function () use ($projectUnId, $payload, $project) {
            $activityUnId = $this->projects->addActivity($payload);

            // Recompute total_cost from activities + base
            $activitySum = $this->projects->totalActivityCost($projectUnId);
            $sale        = (float) $project['sale_amount'];
            $newTotal    = $activitySum;  // cost from all activities
            $this->projects->updateByUnId($projectUnId, [
                'total_cost' => $newTotal,
                'profit'     => $sale - $newTotal,
            ]);
            $this->audit('farm_project.activity_added', 'farm_project', $projectUnId, [
                'activity_un_id' => $activityUnId,
                'cost'           => $payload['cost'],
            ]);
            return [
                'project'  => $this->projects->findByUnId($projectUnId),
                'activity' => ['un_id' => $activityUnId],
            ];
        });
    }

    public function totals(): array
    {
        return $this->projects->totals();
    }

    private function normalize(array $input): array
    {
        $whitelisted = [
            'company_un_id', 'project_name', 'crop_name',
            'land_size', 'land_unit', 'start_date', 'end_date',
            'total_cost', 'production_amount', 'production_unit',
            'sale_amount', 'status', 'notes',
        ];
        return array_intersect_key($input, array_flip($whitelisted));
    }
}
