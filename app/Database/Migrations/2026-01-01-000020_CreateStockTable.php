<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'       => ['type' => 'VARCHAR', 'constraint' => 60],
            'company_un_id'  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'product_un_id'  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'item_name'      => ['type' => 'VARCHAR', 'constraint' => 200],
            'category'       => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'unit'           => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'kg'],
            'current_qty'    => ['type' => 'DECIMAL', 'constraint' => '12,3', 'default' => 0],
            'min_qty'        => ['type' => 'DECIMAL', 'constraint' => '12,3', 'default' => 0],
            'unit_cost'      => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'status'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('category');
        $this->forge->createTable('stock_items');

        $this->forge->addField([
            'id'                => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'             => ['type' => 'VARCHAR', 'constraint' => 60],
            'stock_item_un_id'  => ['type' => 'VARCHAR', 'constraint' => 60],
            'type'              => ['type' => 'VARCHAR', 'constraint' => 20],
            'quantity'          => ['type' => 'DECIMAL', 'constraint' => '12,3'],
            'unit_cost'         => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
            'reference'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'notes'             => ['type' => 'TEXT', 'null' => true],
            'txn_date'          => ['type' => 'DATE'],
            'created_by_un_id'  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('stock_item_un_id');
        $this->forge->addKey('type');
        $this->forge->createTable('stock_transactions');
    }

    public function down()
    {
        $this->forge->dropTable('stock_transactions', true);
        $this->forge->dropTable('stock_items', true);
    }
}
