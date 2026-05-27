<?php

namespace App\Services;

use Config\Database;

class CustomerLedgerService extends BaseService
{
    /**
     * Build full ledger for one customer.
     * Returns transactions array (chronological), running balance, summary.
     */
    public function ledger(string $customerUnId, ?string $from = null, ?string $to = null): array
    {
        $db = Database::connect();

        // Get customer
        $customer = $db->table('customers')
            ->where('un_id', $customerUnId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if (! $customer) {
            return [];
        }

        $from = $from ?: date('Y-01-01');  // default: start of year
        $to   = $to   ?: date('Y-m-d');

        // Sales in period
        $sales = $db->table('sales')
            ->where('customer_un_id', $customerUnId)
            ->where('deleted_at', null)
            ->where('sale_date >=', $from)
            ->where('sale_date <=', $to)
            ->orderBy('sale_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        // Payments in period (from sale_payments table joined to sales)
        $payments = $db->table('sale_payments sp')
            ->join('sales s', 's.un_id = sp.sale_un_id')
            ->select('sp.*, sp.payment_date AS txn_date')
            ->where('s.customer_un_id', $customerUnId)
            ->where('s.deleted_at', null)
            ->where('sp.deleted_at', null)
            ->where('sp.payment_date >=', $from)
            ->where('sp.payment_date <=', $to)
            ->orderBy('sp.payment_date', 'ASC')
            ->orderBy('sp.id', 'ASC')
            ->get()
            ->getResultArray();

        // Build chronological transaction list
        $transactions = [];

        foreach ($sales as $s) {
            $transactions[] = [
                'date'        => $s['sale_date'],
                'type'        => 'sale',
                'reference'   => $s['invoice_no'] ?? '',
                'un_id'       => $s['un_id'],
                'debit'       => (float) ($s['total_amount'] ?? 0),
                'credit'      => 0.0,
                'description' => 'Invoice #' . ($s['invoice_no'] ?? $s['un_id']),
            ];
        }

        foreach ($payments as $p) {
            $transactions[] = [
                'date'        => $p['txn_date'] ?? $p['payment_date'],
                'type'        => 'payment',
                'reference'   => $p['reference_no'] ?? '',
                'un_id'       => $p['un_id'],
                'debit'       => 0.0,
                'credit'      => (float) ($p['amount'] ?? 0),
                'description' => 'Payment via ' . ucfirst(str_replace('_', ' ', $p['payment_method'] ?? 'cash')),
            ];
        }

        // Sort by date (then by debit first so sales appear before same-day payments)
        usort($transactions, function ($a, $b) {
            $cmp = strcmp($a['date'], $b['date']);
            if ($cmp !== 0) return $cmp;
            // sales (debit > 0) before payments on the same day
            return $b['debit'] <=> $a['debit'];
        });

        // Add running balance (positive = customer owes us)
        $openingBalance = (float) ($customer['opening_balance'] ?? 0);
        $balance        = $openingBalance;

        foreach ($transactions as &$t) {
            $balance    += $t['debit'] - $t['credit'];
            $t['balance'] = $balance;
        }
        unset($t);

        $totalDebit  = array_sum(array_column($transactions, 'debit'));
        $totalCredit = array_sum(array_column($transactions, 'credit'));

        return [
            'customer'        => $customer,
            'from'            => $from,
            'to'              => $to,
            'opening_balance' => $openingBalance,
            'transactions'    => $transactions,
            'total_debit'     => $totalDebit,
            'total_credit'    => $totalCredit,
            'closing_balance' => $openingBalance + $totalDebit - $totalCredit,
        ];
    }
}
