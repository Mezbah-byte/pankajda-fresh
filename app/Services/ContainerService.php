<?php

namespace App\Services;

use App\Repositories\ContainerRepository;
use App\Repositories\ContainerCartonRepository;

/**
 * ContainerService - manages the import workflow:
 *   container -> arrival -> customs -> damage assessment -> sales.
 */
class ContainerService extends BaseService
{
    private ContainerRepository $containers;
    private ContainerCartonRepository $cartons;

    public function __construct(?ContainerRepository $containers = null)
    {
        $this->containers = $containers ?? new ContainerRepository();
        $this->cartons    = new ContainerCartonRepository();
    }

    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->containers->search($filters, $page, $perPage);
    }

    public function get(string $unId): ?array
    {
        $row = $this->containers->findByUnId($unId);
        if ($row) {
            $row['total_sold']       = $this->containers->totalSold($unId);
            $row['cost_total']       = (float) $row['cost_total'];
            $row['linked_expenses']  = $this->containers->totalExpenses($unId);
            $row['total_cost']       = $row['cost_total'] + $row['linked_expenses'];
            $row['profit']           = $row['total_sold'] - $row['total_cost'];
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

    // ── Cartons ──────────────────────────────────────────────────────────────

    public function cartonsFor(string $containerUnId): array
    {
        return $this->cartons->forContainer($containerUnId);
    }

    public function addCarton(string $containerUnId, array $input): array
    {
        if (! $this->containers->existsByUnId($containerUnId)) {
            throw new \InvalidArgumentException('Container not found.');
        }
        $data = $this->normalizeCarton($input);
        $data['container_un_id'] = $containerUnId;
        $unId = $this->transaction(fn () => $this->cartons->create($data));
        $this->audit('container.carton.added', 'container', $containerUnId, ['carton_un_id' => $unId]);
        return $this->cartons->findByUnId($unId);
    }

    public function updateCarton(string $containerUnId, string $cartonUnId, array $input): array
    {
        $carton = $this->cartons->findByUnId($cartonUnId);
        if (! $carton || $carton['container_un_id'] !== $containerUnId) {
            throw new \InvalidArgumentException('Carton not found.');
        }
        $data = $this->normalizeCarton($input);
        $this->transaction(fn () => $this->cartons->updateByUnId($cartonUnId, $data));
        $this->audit('container.carton.updated', 'container', $containerUnId, ['carton_un_id' => $cartonUnId]);
        return $this->cartons->findByUnId($cartonUnId);
    }

    public function deleteCarton(string $containerUnId, string $cartonUnId): void
    {
        $carton = $this->cartons->findByUnId($cartonUnId);
        if (! $carton || $carton['container_un_id'] !== $containerUnId) {
            throw new \InvalidArgumentException('Carton not found.');
        }
        $this->transaction(fn () => $this->cartons->deleteByUnId($cartonUnId));
        $this->audit('container.carton.deleted', 'container', $containerUnId, ['carton_un_id' => $cartonUnId]);
    }

    private function normalizeCarton(array $input): array
    {
        $allowed = ['carton_number', 'product_name', 'quantity', 'unit', 'weight_gross', 'weight_net', 'condition', 'notes'];
        return array_intersect_key($input, array_flip($allowed));
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
