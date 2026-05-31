<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContainerUnIdToExpenses extends Migration
{
    public function up()
    {
        $this->forge->addColumn('expenses', [
            'container_un_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
                'default'    => null,
                'after'      => 'company_un_id',
            ],
            'bank_account_un_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
                'default'    => null,
                'after'      => 'payment_method',
            ],
        ]);

        $this->db->query('ALTER TABLE expenses ADD INDEX idx_expenses_container_un_id (container_un_id)');
    }

    public function down()
    {
        $this->forge->dropColumn('expenses', 'bank_account_un_id');
        $this->forge->dropColumn('expenses', 'container_un_id');
    }
}
