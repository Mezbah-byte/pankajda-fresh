<?php

namespace App\Repositories;

use App\Models\VendorModel;
use App\Models\VendorPaymentModel;

class VendorRepository extends BaseRepository
{
    private VendorPaymentModel $paymentModel;

    protected function bootModel(): void
    {
        $this->model        = new VendorModel();
        $this->paymentModel = new VendorPaymentModel();
    }

    /**
     * Paginated search with optional filters.
     *
     * @param array $filters  Supports: q (vendor_name/vendor_code/contact_person), status, company_un_id
     */
    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $q = $filters['q'];
                $builder->groupStart()
                    ->like('vendor_name', $q)
                    ->orLike('vendor_code', $q)
                    ->orLike('contact_person', $q)
                    ->groupEnd();
            }
            if (! empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
            if (! empty($filters['company_un_id'])) {
                $builder->where('company_un_id', $filters['company_un_id']);
            }
        });
    }

    /**
     * Paginated payment history for a specific vendor.
     */
    public function paymentsForVendor(string $vendorUnId, int $page = 1, int $perPage = 20): array
    {
        $perPage = max(1, $perPage);
        $offset  = max(0, ($page - 1) * $perPage);

        $builder = $this->paymentModel->builder();
        $builder->where('vendor_un_id', $vendorUnId)
                ->where('deleted_at', null);

        $total = (clone $builder)->countAllResults(false);

        $items = $builder
            ->orderBy('payment_date', 'DESC')
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

    /**
     * Create a vendor payment record.
     */
    public function createPayment(array $data): string
    {
        $id = $this->paymentModel->insert($data, true);
        if ($id === false) {
            throw new \RuntimeException('Insert failed: ' . implode(', ', $this->paymentModel->errors()));
        }
        $row = $this->paymentModel->find($id);
        return $row['un_id'] ?? '';
    }
}
