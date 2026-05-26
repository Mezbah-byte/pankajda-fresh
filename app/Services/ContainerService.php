<?php

namespace App\Services;

use App\Repositories\ContainerRepository;

/**
 * ContainerService - manages the import workflow:
 *   container -> arrival -> customs -> damage assessment -> sales.
 */
class ContainerService extends BaseService
{
    private ContainerRepository $containers;

    public function __construct(?ContainerRepository $containers = null)
    {
        $this->containers = $containers ?? new ContainerRepository();
    }

    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->containers->search($filters, $page, $perPage);
    }

    public function get(string $unId): ?array
    {
        $row = $this->containers->findByUnId($unId);
        if ($row) {
            $row['total_sold'] = $this->containers->totalSold($unId);
            $row['cost_total'] = (float) $row['cost_total'];
            $row['profit']     = $row['total_sold'] - $row['cost_total'];
        }
        return $row;
    }

    public function create(array $input): array
    {
        $data = $this->normalize($input);
        if (empty($data['container_number'])) {
            throw new \InvalidArgumentException('Container number is required.');
        }
        $unId = $this->transaction(fn () => $this->containers->create($data));
        $this->audit('container.created', 'container', $unId, [
            'container_number' => $data['container_number'],
        ]);
        return $this->containers->findByUnId($unId);
    }

    public function update(string $unId, array $input): array
    {
        if (! $this->containers->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Container not found.');
        }
        $data = $this->normalize($input);
        $this->transaction(fn () => $this->containers->updateByUnId($unId, $data));
        $this->audit('container.updated', 'container', $unId);
        return $this->containers->findByUnId($unId);
    }

    public function delete(string $unId): void
    {
        if (! $this->containers->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Container not found.');
        }
        $this->transaction(fn () => $this->containers->deleteByUnId($unId));
        $this->audit('container.deleted', 'container', $unId);
    }

    public function totals(): array
    {
        return $this->containers->totals();
    }

    private function normalize(array $input): array
    {
        $whitelisted = [
            'company_un_id', 'container_number', 'bl_number', 'product_name',
            'origin_country', 'arrival_date', 'customs_status', 'customs_clear_date',
            'total_products', 'damaged_products', 'unit',
            'cost_total', 'customs_cost', 'transport_cost', 'other_cost',
            'notes', 'status',
        ];
        return array_intersect_key($input, array_flip($whitelisted));
    }
}
