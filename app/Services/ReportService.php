<?php

namespace App\Services;

use Config\Database;

/**
 * ReportService - aggregates data across modules for the Reports section.
 *
 * Each method returns a flat array shape ready for view rendering and
 * for CSV/PDF export. Everything respects date_from / date_to filters.
 */
class ReportService extends BaseService
{
    /**
     * Daily sales: invoice count + total + paid + due per day.
     */
    public function salesDaily(?string $from = null, ?string $to = null): array
    {
        $db = Database::connect();
        $from = $from ?: date('Y-m-01');
        $to   = $to   ?: date('Y-m-d');

        $rows = $db->table('sales')
            ->select("sale_date, COUNT(*) AS invoices,
                      SUM(total_amount) AS total,
                      SUM(paid_amount)  AS paid,
                      SUM(due_amount)   AS due")
            ->where('deleted_at', null)
            ->where('sale_date >=', $from)
            ->where('sale_date <=', $to)
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'DESC')
            ->get()->getResultArray();

        return [
            'from' => $from, 'to' => $to,
            'rows' => $rows,
            'totals' => $this->sumRows($rows, ['invoices', 'total', 'paid', 'due']),
        ];
    }

    /**
     * Monthly sales summary (last 12 months by default).
     */
    public function salesMonthly(?string $from = null, ?string $to = null): array
    {
        $db = Database::connect();
        $from = $from ?: date('Y-m-01', strtotime('-11 months'));
        $to   = $to   ?: date('Y-m-d');

        $rows = $db->table('sales')
            ->select("DATE_FORMAT(sale_date, '%Y-%m') AS month,
                      COUNT(*) AS invoices,
                      SUM(total_amount) AS total,
                      SUM(paid_amount)  AS paid,
                      SUM(due_amount)   AS due")
            ->where('deleted_at', null)
            ->where('sale_date >=', $from)
            ->where('sale_date <=', $to)
            ->groupBy("DATE_FORMAT(sale_date, '%Y-%m')")
            ->orderBy('month', 'DESC')
            ->get()->getResultArray();

        return [
            'from' => $from, 'to' => $to,
            'rows' => $rows,
            'totals' => $this->sumRows($rows, ['invoices', 'total', 'paid', 'due']),
        ];
    }

    /**
     * Customer due report - all customers with outstanding balance.
     */
    public function customerDues(): array
    {
        $db = Database::connect();
        $rows = $db->table('customers')
            ->select('un_id, customer_name, phone, city, current_due, credit_limit, opening_balance')
            ->where('deleted_at', null)
            ->where('current_due >', 0)
            ->orderBy('current_due', 'DESC')
            ->get()->getResultArray();
        $totalDue = array_sum(array_column($rows, 'current_due'));
        return [
            'rows'      => $rows,
            'total_due' => (float) $totalDue,
            'count'     => count($rows),
        ];
    }

    /**
     * Expense report by category for a date range.
     */
    public function expenseByCategory(?string $from = null, ?string $to = null): array
    {
        $db = Database::connect();
        $from = $from ?: date('Y-m-01');
        $to   = $to   ?: date('Y-m-d');

        $rows = $db->table('expenses')
            ->select('category, COUNT(*) AS count, SUM(amount) AS total')
            ->where('deleted_at', null)
            ->where('expense_date >=', $from)
            ->where('expense_date <=', $to)
            ->groupBy('category')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();

        return [
            'from' => $from, 'to' => $to,
            'rows' => $rows,
            'totals' => $this->sumRows($rows, ['count', 'total']),
        ];
    }

    /**
     * Profit & Loss summary: sales income minus expenses minus container costs.
     */
    public function profitLoss(?string $from = null, ?string $to = null): array
    {
        $db = Database::connect();
        $from = $from ?: date('Y-m-01');
        $to   = $to   ?: date('Y-m-d');

        $salesRow = $db->table('sales')
            ->selectSum('total_amount', 'total')
            ->where('deleted_at', null)
            ->where('sale_date >=', $from)
            ->where('sale_date <=', $to)
            ->get()->getRowArray();
        $grossSales = (float) ($salesRow['total'] ?? 0);

        // Approved goods returns reduce revenue
        $returnsRow = $db->table('goods_return_vouchers')
            ->selectSum('total_amount', 'total')
            ->where('deleted_at', null)
            ->where('status', 'approved')
            ->where('grv_date >=', $from)
            ->where('grv_date <=', $to)
            ->get()->getRowArray();
        $returns = (float) ($returnsRow['total'] ?? 0);
        $sales   = $grossSales - $returns;

        $expRow = $db->table('expenses')
            ->selectSum('amount', 'total')
            ->where('deleted_at', null)
            ->where('expense_date >=', $from)
            ->where('expense_date <=', $to)
            ->get()->getRowArray();
        $expenses = (float) ($expRow['total'] ?? 0);

        $contRow = $db->table('containers')
            ->selectSum('cost_total', 'total')
            ->where('deleted_at', null)
            ->where('arrival_date >=', $from)
            ->where('arrival_date <=', $to)
            ->get()->getRowArray();
        $containerCost = (float) ($contRow['total'] ?? 0);

        $farmRow = $db->table('farm_projects')
            ->selectSum('profit', 'total')
            ->where('deleted_at', null)
            ->get()->getRowArray();
        $farmProfit = (float) ($farmRow['total'] ?? 0);

        $netProfit = $sales - $expenses - $containerCost + $farmProfit;

        return [
            'from'           => $from,
            'to'             => $to,
            'gross_sales'    => $grossSales,
            'returns'        => $returns,
            'sales'          => $sales,
            'expenses'       => $expenses,
            'container_cost' => $containerCost,
            'farm_profit'    => $farmProfit,
            'net_profit'     => $netProfit,
        ];
    }

    /**
     * Company-wise summary report.
     */
    public function companyWise(): array
    {
        $db = Database::connect();

        $companies = $db->table('companies')
            ->select('un_id, company_name')
            ->where('deleted_at', null)
            ->orderBy('company_name', 'ASC')
            ->get()->getResultArray();

        $rows = [];
        foreach ($companies as $c) {
            $salesRow = $db->table('sales')
                ->selectSum('total_amount', 'total')->selectSum('due_amount', 'due')
                ->where('company_un_id', $c['un_id'])->where('deleted_at', null)
                ->get()->getRowArray();
            $expRow = $db->table('expenses')
                ->selectSum('amount', 'total')
                ->where('company_un_id', $c['un_id'])->where('deleted_at', null)
                ->get()->getRowArray();
            $visaRow = $db->table('visas')
                ->selectSum('visa_cost', 'cost')->selectSum('due_amount', 'due')
                ->where('company_un_id', $c['un_id'])->where('deleted_at', null)
                ->get()->getRowArray();

            $rows[] = [
                'un_id'        => $c['un_id'],
                'company_name' => $c['company_name'],
                'sales'        => (float) ($salesRow['total'] ?? 0),
                'sales_due'    => (float) ($salesRow['due'] ?? 0),
                'expenses'     => (float) ($expRow['total'] ?? 0),
                'visa_cost'    => (float) ($visaRow['cost'] ?? 0),
                'visa_due'     => (float) ($visaRow['due'] ?? 0),
                'net'          => (float) ($salesRow['total'] ?? 0) - (float) ($expRow['total'] ?? 0),
            ];
        }
        return ['rows' => $rows];
    }

    /**
     * Export any rowset to CSV (downloadable).
     */
    public function rowsToCsv(array $rows, array $columns, string $filename = 'export.csv'): string
    {
        $fp = fopen('php://temp', 'w+');
        fputcsv($fp, $columns);
        foreach ($rows as $r) {
            $line = [];
            foreach ($columns as $col) {
                $line[] = $r[$col] ?? '';
            }
            fputcsv($fp, $line);
        }
        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);
        return $csv;
    }

    private function sumRows(array $rows, array $columns): array
    {
        $totals = array_fill_keys($columns, 0);
        foreach ($rows as $r) {
            foreach ($columns as $c) {
                $totals[$c] += (float) ($r[$c] ?? 0);
            }
        }
        return $totals;
    }
}
