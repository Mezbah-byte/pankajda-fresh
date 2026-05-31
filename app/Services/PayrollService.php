<?php

namespace App\Services;

use App\Repositories\PayrollRepository;
use App\Repositories\EmployeeAdvanceRepository;
use App\Services\BankAccountService;
use Config\Database;

class PayrollService extends BaseService
{
    private PayrollRepository         $payroll;
    private EmployeeAdvanceRepository $advances;
    private BankAccountService        $bankAccounts;

    public function __construct(
        ?PayrollRepository $payroll = null,
        ?EmployeeAdvanceRepository $advances = null,
        ?BankAccountService $bankAccounts = null
    ) {
        $this->payroll      = $payroll      ?? new PayrollRepository();
        $this->advances     = $advances     ?? new EmployeeAdvanceRepository();
        $this->bankAccounts = $bankAccounts ?? new BankAccountService();
    }

    /**
     * Paginated list of payroll records with optional filters.
     */
    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->payroll->search($filters, $page, $perPage);
    }

    /**
     * Get a single payroll record by un_id.
     */
    public function get(string $unId): ?array
    {
        return $this->payroll->findByUnId($unId);
    }

    /**
     * Generate a payroll record. Calculates net_salary automatically.
     * Throws InvalidArgumentException if a record already exists for employee+period.
     */
    public function generate(array $input): array
    {
        $data = $this->normalizePayroll($input);

        // Auto-calculate net salary
        $data['net_salary'] = round(
            (float) ($data['basic_salary']      ?? 0)
            + (float) ($data['allowances']       ?? 0)
            - (float) ($data['deductions']       ?? 0)
            - (float) ($data['advance_deduction'] ?? 0),
            2
        );

        // Check for duplicate
        $existing = $this->payroll->findByEmployeePeriod(
            $data['employee_un_id'],
            $data['pay_period']
        );
        if ($existing) {
            throw new \InvalidArgumentException(
                'Payroll for this employee and period already exists.'
            );
        }

        $advanceDeduction = (float) ($data['advance_deduction'] ?? 0);

        $unId = $this->transaction(function () use ($data, $advanceDeduction) {
            $id = $this->payroll->create($data);
            if ($advanceDeduction > 0) {
                $this->advances->applyRepayment($data['employee_un_id'], $advanceDeduction);
            }
            return $id;
        });

        $this->audit('payroll.generated', 'payroll', $unId, [
            'employee_un_id' => $data['employee_un_id'],
            'pay_period'     => $data['pay_period'],
            'net_salary'     => $data['net_salary'],
        ]);

        return $this->payroll->findByUnId($unId);
    }

    /**
     * Mark a payroll record as paid.
     */
    public function markPaid(string $unId, array $input = []): array
    {
        if (! $this->payroll->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Payroll record not found.');
        }

        $data = [
            'status'  => 'paid',
            'paid_at' => date('Y-m-d H:i:s'),
        ];

        if (! empty($input['payment_method'])) {
            $data['payment_method'] = $input['payment_method'];
        }
        if (! empty($input['bank_account_un_id'])) {
            $data['bank_account_un_id'] = $input['bank_account_un_id'];
        }
        if (! empty($input['notes'])) {
            $data['notes'] = $input['notes'];
        }

        $bankUnId  = $input['bank_account_un_id'] ?? null;
        $netSalary = (float) ($this->payroll->findByUnId($unId)['net_salary'] ?? 0);

        $this->transaction(function () use ($unId, $data, $bankUnId, $netSalary) {
            $this->payroll->updateByUnId($unId, $data);
            if ($bankUnId && $netSalary > 0) {
                $this->bankAccounts->adjustBalance($bankUnId, $netSalary, 'debit');
            }
        });

        $this->audit('payroll.paid', 'payroll', $unId);

        return $this->payroll->findByUnId($unId);
    }

    /**
     * Soft-delete a payroll record.
     */
    public function delete(string $unId): bool
    {
        if (! $this->payroll->existsByUnId($unId)) {
            throw new \InvalidArgumentException('Payroll record not found.');
        }
        $result = $this->transaction(fn () => $this->payroll->deleteByUnId($unId));
        $this->audit('payroll.deleted', 'payroll', $unId);
        return $result;
    }

    /**
     * Paginated advances for a specific employee.
     */
    public function advances(?string $empUnId, int $page = 1, int $perPage = 20): array
    {
        return $this->advances->forEmployee($empUnId, $page, $perPage);
    }

    /**
     * Create a new advance record. Returns the new un_id.
     */
    public function addAdvance(array $input): string
    {
        $data = array_intersect_key($input, array_flip([
            'employee_un_id', 'amount', 'advance_date', 'reason',
        ]));
        $data['repaid_amount'] = 0;
        $data['status']        = 'outstanding';

        $unId = $this->transaction(fn () => $this->advances->create($data));

        $this->audit('employee.advance_given', 'employee_advance', $unId, [
            'employee_un_id' => $data['employee_un_id'],
            'amount'         => $data['amount'],
        ]);

        return $unId;
    }

    /**
     * Summary stats for a given period (YYYY-MM).
     */
    public function summary(string $period): array
    {
        $db = Database::connect();

        $base = $db->table('payroll_records')
            ->where('pay_period', $period)
            ->where('deleted_at', null);

        $totals = (clone $base)
            ->select('SUM(basic_salary) AS total_basic, SUM(net_salary) AS total_net, COUNT(*) AS total_count')
            ->get()->getRowArray();

        $paid = (clone $base)
            ->where('status', 'paid')
            ->countAllResults(false);

        $pending = (clone $base)
            ->where('status', 'pending')
            ->countAllResults(false);

        return [
            'total_basic'   => (float) ($totals['total_basic'] ?? 0),
            'total_net'     => (float) ($totals['total_net']   ?? 0),
            'total_count'   => (int)   ($totals['total_count'] ?? 0),
            'paid_count'    => (int)   $paid,
            'pending_count' => (int)   $pending,
        ];
    }

    private function normalizePayroll(array $input): array
    {
        $allowed = [
            'employee_un_id', 'company_un_id', 'pay_period',
            'basic_salary', 'allowances', 'deductions', 'advance_deduction',
            'payment_method', 'bank_account_un_id', 'status', 'notes',
        ];
        return array_intersect_key($input, array_flip($allowed));
    }
}
