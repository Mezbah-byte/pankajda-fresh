<?php

namespace App\Services;

use App\Repositories\VendorRepository;
use App\Services\BankAccountService;
use Config\Database;

class VendorService extends BaseService
{
    private VendorRepository   $vendors;
    private BankAccountService $bankAccounts;

    public function __construct(?VendorRepository $vendors = null, ?BankAccountService $bankAccounts = null)
    {
        $this->vendors      = $vendors      ?? new VendorRepository();
        $this->bankAccounts = $bankAccounts ?? new BankAccountService();
    }

    /**
     * Paginated list of vendors with optional filters.
     */
    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->vendors->search($filters, $page, $perPage);
    }

    /**
     * Get a single vendor by un_id.
     */
    public function get(string $unId): ?array
    {
        return $this->vendors->findByUnId($unId);
    }

    /**
     * Create a new vendor. Returns the created vendor array.
     */
    public function create(array $input): array
    {
        $data = $this->normalize($input);
        $unId = $this->transaction(fn () => $this->vendors->create($data));

        $this->audit('vendor.created', 'vendor', $unId, [
            'vendor_name' => $data['vendor_name'] ?? '',
        ]);

        return $this->vendors->findByUnId($unId);
    }

    /**
     * Update an existing vendor.
     */
    public function update(string $unId, array $input): array
    {
        if (! $this->vendors->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Vendor not found.');
        }

        $data = $this->normalize($input);
        $this->transaction(fn () => $this->vendors->updateByUnId($unId, $data));
        $this->audit('vendor.updated', 'vendor', $unId);

        return $this->vendors->findByUnId($unId);
    }

    /**
     * Soft-delete a vendor.
     */
    public function delete(string $unId): bool
    {
        if (! $this->vendors->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Vendor not found.');
        }

        $result = $this->transaction(fn () => $this->vendors->deleteByUnId($unId));
        $this->audit('vendor.deleted', 'vendor', $unId);

        return $result;
    }

    /**
     * Record a payment to a vendor, reduce current_payable.
     * Returns the new payment un_id.
     */
    public function addPayment(string $vendorUnId, array $input): string
    {
        $vendor = $this->vendors->findByUnId($vendorUnId);
        if (! $vendor) {
            throw new \InvalidArgumentException('Vendor not found.');
        }

        $amount = (float) ($input['amount'] ?? 0);
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Payment amount must be greater than zero.');
        }

        $paymentData = array_intersect_key($input, array_flip([
            'amount', 'payment_date', 'payment_method', 'reference_no', 'notes',
        ]));
        $paymentData['vendor_un_id'] = $vendorUnId;

        $newPayable   = max(0, (float) $vendor['current_payable'] - $amount);
        $bankUnId     = $input['bank_account_un_id'] ?? null;

        $payUnId = $this->transaction(function () use ($vendorUnId, $paymentData, $newPayable, $bankUnId, $amount) {
            $unId = $this->vendors->createPayment($paymentData);
            $this->vendors->updateByUnId($vendorUnId, ['current_payable' => $newPayable]);
            if ($bankUnId) {
                $this->bankAccounts->adjustBalance($bankUnId, $amount, 'debit');
            }
            return $unId;
        });

        $this->audit('vendor.payment', 'vendor', $vendorUnId, [
            'payment_un_id' => $payUnId,
            'amount'        => $amount,
        ]);

        return $payUnId;
    }

    /**
     * Paginated payments for a vendor.
     */
    public function payments(string $vendorUnId, int $page = 1, int $perPage = 20): array
    {
        return $this->vendors->paymentsForVendor($vendorUnId, $page, $perPage);
    }

    /**
     * Aggregate totals: vendor count, payable, paid.
     */
    public function totals(): array
    {
        $db    = Database::connect();
        $count = (int) $db->table('vendors')->where('deleted_at', null)->countAllResults();
        $row   = $db->table('vendors')
            ->selectSum('current_payable', 'total_payable')
            ->where('deleted_at', null)
            ->get()->getRowArray();
        $paidRow = $db->table('vendor_payments')->selectSum('amount', 'total_paid')->get()->getRowArray();

        return [
            'count'         => $count,
            'total_payable' => (float) ($row['total_payable'] ?? 0),
            'total_paid'    => (float) ($paidRow['total_paid'] ?? 0),
        ];
    }

    private function normalize(array $input): array
    {
        $allowed = [
            'company_un_id', 'vendor_name', 'vendor_code', 'contact_person',
            'phone', 'email', 'address', 'city', 'country',
            'product_category', 'payment_terms', 'current_payable', 'status', 'notes',
        ];
        return array_intersect_key($input, array_flip($allowed));
    }
}
