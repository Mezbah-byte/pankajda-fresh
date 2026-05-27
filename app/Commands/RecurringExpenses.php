<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * Spawn recurring expense entries.
 * Schedule: php spark expenses:recur  (daily cron)
 */
class RecurringExpenses extends BaseCommand
{
    protected $group       = 'app';
    protected $name        = 'expenses:recur';
    protected $description = 'Generate pending recurring expense entries.';

    public function run(array $params): void
    {
        $db      = Database::connect();
        $today   = date('Y-m-d');
        $created = 0;
        $skipped = 0;

        $due = $db->table('expenses')
            ->where('is_recurring', 1)
            ->where('deleted_at', null)
            ->where('recur_next_date <=', $today)
            ->groupStart()
                ->where('recur_end_date IS NULL')
                ->orWhere('recur_end_date >=', $today)
            ->groupEnd()
            ->get()->getResultArray();

        foreach ($due as $exp) {
            // Compute new next date
            $nextDate = match ($exp['recur_interval'] ?? 'monthly') {
                'daily'     => date('Y-m-d', strtotime($exp['recur_next_date'] . ' +1 day')),
                'weekly'    => date('Y-m-d', strtotime($exp['recur_next_date'] . ' +1 week')),
                'monthly'   => date('Y-m-d', strtotime($exp['recur_next_date'] . ' +1 month')),
                'quarterly' => date('Y-m-d', strtotime($exp['recur_next_date'] . ' +3 months')),
                'yearly'    => date('Y-m-d', strtotime($exp['recur_next_date'] . ' +1 year')),
                default     => date('Y-m-d', strtotime($exp['recur_next_date'] . ' +1 month')),
            };

            // Check end date
            if (! empty($exp['recur_end_date']) && $nextDate > $exp['recur_end_date']) {
                // Disable recurring — reached end
                $db->table('expenses')
                   ->where('un_id', $exp['un_id'])
                   ->update(['is_recurring' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
                $skipped++;
                continue;
            }

            // Generate new un_id
            $newUnId = 'EXP-' . strtoupper(substr(bin2hex(random_bytes(8)), 0, 8));

            $db->table('expenses')->insert([
                'un_id'            => $newUnId,
                'expense_title'    => $exp['expense_title'],
                'amount'           => $exp['amount'],
                'category'         => $exp['category'],
                'expense_date'     => $exp['recur_next_date'],
                'payment_method'   => $exp['payment_method'],
                'reference_no'     => $exp['reference_no'],
                'notes'            => $exp['notes'],
                'company_un_id'    => $exp['company_un_id'],
                'is_recurring'     => 0,                       // child = not recurring
                'recur_parent_un_id' => $exp['un_id'],
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);

            // Advance the next date on parent
            $db->table('expenses')
               ->where('un_id', $exp['un_id'])
               ->update(['recur_next_date' => $nextDate, 'updated_at' => date('Y-m-d H:i:s')]);

            $created++;
        }

        CLI::write("Recurring expenses: {$created} created, {$skipped} ended.", 'green');
    }
}
