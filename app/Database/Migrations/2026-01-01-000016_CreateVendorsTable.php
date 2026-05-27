<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVendorsTable extends Migration
{
    public function up()
    {
        // --- vendors ---
        $this->forge->addField([
            'id'               => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'            => ['type' => 'VARCHAR', 'constraint' => 60],
            'company_un_id'    => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'vendor_name'      => ['type' => 'VARCHAR', 'constraint' => 200],
            'vendor_code'      => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'contact_person'   => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'phone'            => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'email'            => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'address'          => ['type' => 'TEXT', 'null' => true],
            'city'             => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'country'          => ['type' => 'VARCHAR', 'constraint' => 80, 'default' => 'Bangladesh'],
            'product_category' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'payment_terms'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'current_payable'  => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'status'           => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'notes'            => ['type' => 'TEXT', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('company_un_id');
        $this->forge->addKey('status');
        $this->forge->createTable('vendors');

        // --- vendor_payments ---
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'          => ['type' => 'VARCHAR', 'constraint' => 60],
            'vendor_un_id'   => ['type' => 'VARCHAR', 'constraint' => 60],
            'amount'         => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'payment_date'   => ['type' => 'DATE'],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'cash'],
            'reference_no'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'notes'          => ['type' => 'TEXT', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('vendor_un_id');
        $this->forge->createTable('vendor_payments');
    }

    public function down()
    {
        $this->forge->dropTable('vendor_payments', true);
        $this->forge->dropTable('vendors', true);
    }
}
