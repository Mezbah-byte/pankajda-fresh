<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGrvItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'          => ['type' => 'VARCHAR', 'constraint' => 60],
            'grv_un_id'      => ['type' => 'VARCHAR', 'constraint' => 60],
            'product_un_id'  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'product_name'   => ['type' => 'VARCHAR', 'constraint' => 200],
            'unit'           => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'pcs'],
            'quantity'       => ['type' => 'DECIMAL', 'constraint' => '12,3', 'default' => 0],
            'unit_price'     => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'reason'         => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('grv_un_id');
        $this->forge->addKey('product_un_id');
        $this->forge->createTable('grv_items');
    }

    public function down()
    {
        $this->forge->dropTable('grv_items', true);
    }
}
