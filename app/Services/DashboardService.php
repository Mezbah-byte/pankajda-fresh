<?php

namespace App\Services;

use Config\Database;

/**
 * DashboardService - aggregates KPIs and chart data for the admin home.
 *
 * Each lookup is a simple count/sum; we keep them defensive so a missing
 * table or no rows simply yields zero rather than raising.
 */
class DashboardService extends BaseService
{
    public function summary(): array
    {
        $db = Database::connect();

        $stats = [
            'companies'     => $this->safeCount($db, 'companies'),
            'visas'         => $this->safeCount($db, 'visas', ['status' => 'active']),
            'containers'    => $this->safeCount($db, 'containers'),
            'customers'     => $this->safeCount($db, 'customers'),
            'total_sales'   => $this->safeSum($db, 'sales', 'total_amount'),
            'total_due'     => $this->safeSum($db, 'sales', 'due_amount'),
            'expense_month' => $this->safeSum($db, 'expenses', 'amount', [
                ['expense_date >=', date('Y-m-01')],
            ]),
            'farm_profit'   => $this->safeSum($db, 'farm_projects', 'profit'),
        ];

        $recent = [];
        if ($db->tableExists('activity_logs')) {
            $recent = $db->table('activity_logs')
                ->orderBy('id', 'DESC')
                ->limit(8)
                ->get()->getResultArray();
        }

        return [
            'stats'          => $stats,
            'recentActivity' => $recent,
            'chart'          => $this->salesChart($db),
        ];
    }

    private function safeCount($db, string $table, array $where = []): int
    {
        if (! $db->tableExists($table)) {
            return 0;
        }
        $b = $db->table($table)->where('deleted_at', null);
        foreach ($where as $k => $v) {
            $b->where($k, $v);
        }
        return $b->countAllResults();
    }

    private function safeSum($db, string $table, string $col, array $whereTuples = []): float
    {
        if (! $db->tableExists($table)) {
            return 0.0;
        }
        $b = $db->table($table)->selectSum($col, 'total')->where('deleted_at', null);
        foreach ($whereTuples as $t) {
            $b->where($t[0], $t[1]);
        }
        $row = $b->get()->getRowArray();
        return (float) ($row['total'] ?? 0);
    }

    private function salesChart($db): array
    {
        $labels = [];
        $values = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('M d', strtotime($d));
            if (! $db->tableExists('sales')) {
                $values[] = 0;
                continue;
            }
            $row = $db->table('sales')->selectSum('total_amount', 'total')
                ->where('sale_date', $d)->where('deleted_at', null)
                ->get()->getRowArray();
            $values[] = (float) ($row['total'] ?? 0);
        }
        return ['labels' => $labels, 'values' => $values];
    }
}
