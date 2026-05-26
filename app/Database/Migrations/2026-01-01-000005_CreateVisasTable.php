<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVisasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                  => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'               => ['type' => 'VARCHAR', 'constraint' => 60],
            'company_un_id'       => ['type' => 'VARCHAR', 'constraint' => 60],
            'visa_name'           => ['type' => 'VARCHAR', 'constraint' => 200],
            'visa_number'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'country'             => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'category'            => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'beneficiary_name'    => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'passport_no'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'visa_cost'           => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'paid_amount'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'due_amount'          => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'payment_status'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'due'],
            'visa_issue_date'     => ['type' => 'DATE', 'null' => true],
            'visa_expiry_date'    => ['type' => 'DATE', 'null' => true],
            'status'              => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'active'],
            'notes'               => ['type' => 'TEXT', 'null' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('company_un_id');
        $this->forge->addKey('payment_status');
        $this->forge->addKey('status');
        $this->forge->createTable('visas');

        // Visa payments
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'          => ['type' => 'VARCHAR', 'constraint' => 60],
            'visa_un_id'     => ['type' => 'VARCHAR', 'constraint' => 60],
            'amount'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'cash'],
            'reference_no'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'payment_date'   => ['type' => 'DATE', 'null' => true],
            'notes'          => ['type' => 'TEXT', 'null' => true],
            'created_by_un_id'=> ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('visa_un_id');
        $this->forge->createTable('visa_payments');
    }

    public function down()
    {
        $this->forge->dropTable('visa_payments', true);
        $this->forge->dropTable('visas', true);
    }
}
