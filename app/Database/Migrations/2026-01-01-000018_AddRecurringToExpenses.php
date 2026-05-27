<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRecurringToExpenses extends Migration
{
    public function up()
    {
        $fields = [
            'is_recurring'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'notes'],
            'recur_interval'     => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'is_recurring'],
            'recur_next_date'    => ['type' => 'DATE', 'null' => true, 'after' => 'recur_interval'],
            'recur_end_date'     => ['type' => 'DATE', 'null' => true, 'after' => 'recur_next_date'],
            'recur_parent_un_id' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true, 'after' => 'recur_end_date'],
        ];
        $this->forge->addColumn('expenses', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('expenses', ['is_recurring', 'recur_interval', 'recur_next_date', 'recur_end_date', 'recur_parent_un_id']);
    }
}
