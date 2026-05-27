<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBankAccountsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'           => ['type' => 'VARCHAR', 'constraint' => 60],
            'company_un_id'   => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'account_name'    => ['type' => 'VARCHAR', 'constraint' => 200],
            'bank_name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'account_number'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'branch'          => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'routing_number'  => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'account_type'    => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'current'],
            'opening_balance' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'current_balance' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'currency'        => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'BDT'],
            'status'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'notes'           => ['type' => 'TEXT', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('company_un_id');
        $this->forge->addKey('status');
        $this->forge->createTable('bank_accounts');
    }

    public function down()
    {
        $this->forge->dropTable('bank_accounts', true);
    }
}
