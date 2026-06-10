<?php

namespace App\Repositories;

use App\Models\VisaModel;
use App\Models\VisaExtraCostModel;
use App\Models\VisaPaymentModel;
use Config\Database;

class VisaRepository extends BaseRepository
{
    private VisaPaymentModel $payments;
    private VisaExtraCostModel $extraCosts;

    protected function bootModel(): void
    {
        $this->model      = new VisaModel();
        $this->payments   = new VisaPaymentModel();
        $this->extraCosts = new VisaExtraCostModel();
    }

    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $q = $filters['q'];
                $builder->groupStart()
                    ->like('visa_name', $q)
                    ->orLike('beneficiary_name', $q)
                    ->orLike('passport_no', $q)
                    ->orLike('visa_number', $q)
                    ->groupEnd();
            }
            if (! empty($filters['company_un_id'])) {
                $builder->where('company_un_id', $filters['company_un_id']);
            }
            if (! empty($filters['payment_status'])) {
                $builder->where('payment_status', $filters['payment_status']);
            }
            if (! empty($filters['country'])) {
                $builder->where('country', $filters['country']);
            }
            if (! empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
        });
    }

    public function recordPayment(string $visaUnId, array $payment): string
    {
        $payment['visa_un_id'] = $visaUnId;
        $id = $this->payments->insert($payment, true);
        if ($id === false) {
            throw new \RuntimeException('Failed to record visa payment.');
        }
        $row = $this->payments->find($id);
        return $row['un_id'];
    }

    public function paymentsFor(string $visaUnId): array
    {
        return $this->payments->where('visa_un_id', $visaUnId)
            ->where('deleted_at', null)
            ->orderBy('payment_date', 'DESC')
            ->findAll();
    }

    public function extraCostsFor(string $visaUnId): array
    {
        return $this->extraCosts->where('visa_un_id', $visaUnId)
            ->where('deleted_at', null)
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function addExtraCost(string $visaUnId, array $data): string
    {
        $data['visa_un_id'] = $visaUnId;
        $id = $this->extraCosts->insert($data, true);
        if ($id === false) {
            throw new \RuntimeException('Failed to add extra cost.');
        }
        return $this->extraCosts->find($id)['un_id'];
    }

    public function deleteExtraCost(string $unId): bool
    {
        return $this->extraCosts->deleteByUnId($unId);
    }

    public function sumExtraCosts(string $visaUnId): float
    {
        $row = $this->extraCosts->builder()
            ->selectSum('amount', 'total')
            ->where('visa_un_id', $visaUnId)
            ->where('deleted_at', null)
            ->get()->getRowArray();
        return (float) ($row['total'] ?? 0);
    }

    public function totalPaid(string $visaUnId): float
    {
        $row = $this->payments->builder()
            ->selectSum('amount', 'total')
            ->where('visa_un_id', $visaUnId)
            ->where('deleted_at', null)
            ->get()->getRowArray();
        return (float) ($row['total'] ?? 0);
    }

    public function totals(): array
    {
        $db = Database::connect();
        $total   = $this->model->where('deleted_at', null)->countAllResults(false);
        $active  = $this->model->where('status', 'active')->where('deleted_at', null)->countAllResults();
        $rowDue  = $db->table('visas')->selectSum('due_amount', 'total')
            ->where('deleted_at', null)->get()->getRowArray();
        return [
            'total'  => $total,
            'active' => $active,
            'due'    => (float) ($rowDue['total'] ?? 0),
        ];
    }
}
