<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'           => ['type' => 'VARCHAR', 'constraint' => 60],
            'company_un_id'   => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'customer_code'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'customer_name'   => ['type' => 'VARCHAR', 'constraint' => 200],
            'phone'           => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'email'           => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'address'         => ['type' => 'TEXT', 'null' => true],
            'city'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'opening_balance' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'current_due'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'credit_limit'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'notes'           => ['type' => 'TEXT', 'null' => true],
            'status'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('company_un_id');
        $this->forge->addKey('customer_name');
        $this->forge->addKey('phone');
        $this->forge->createTable('customers');
    }

    public function down()
    {
        $this->forge->dropTable('customers', true);
    }
}
