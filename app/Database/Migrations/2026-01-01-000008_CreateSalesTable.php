<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSalesTable extends Migration
{
    public function up()
    {
        // Sales / Invoices
        $this->forge->addField([
            'id'              => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'           => ['type' => 'VARCHAR', 'constraint' => 60],
            'invoice_no'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'company_un_id'   => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'customer_un_id'  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'container_un_id' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'sale_type'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'cash'],
            'sale_date'       => ['type' => 'DATE'],
            'subtotal'        => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'discount'        => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'tax'             => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total_amount'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'paid_amount'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'due_amount'      => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'payment_status'  => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'due'],
            'notes'           => ['type' => 'TEXT', 'null' => true],
            'created_by_un_id'=> ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addUniqueKey('invoice_no');
        $this->forge->addKey('customer_un_id');
        $this->forge->addKey('container_un_id');
        $this->forge->addKey('payment_status');
        $this->forge->addKey('sale_date');
        $this->forge->createTable('sales');

        // Sale items
        $this->forge->addField([
            'id'           => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'        => ['type' => 'VARCHAR', 'constraint' => 60],
            'sale_un_id'   => ['type' => 'VARCHAR', 'constraint' => 60],
            'product_name' => ['type' => 'VARCHAR', 'constraint' => 200],
            'quantity'     => ['type' => 'DECIMAL', 'constraint' => '15,3', 'default' => 0],
            'unit'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'kg'],
            'unit_price'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total'        => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('sale_un_id');
        $this->forge->createTable('sale_items');

        // Sale payments
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'          => ['type' => 'VARCHAR', 'constraint' => 60],
            'sale_un_id'     => ['type' => 'VARCHAR', 'constraint' => 60],
            'customer_un_id' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
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
        $this->forge->addKey('sale_un_id');
        $this->forge->addKey('customer_un_id');
        $this->forge->createTable('sale_payments');
    }

    public function down()
    {
        $this->forge->dropTable('sale_payments', true);
        $this->forge->dropTable('sale_items', true);
        $this->forge->dropTable('sales', true);
    }
}
