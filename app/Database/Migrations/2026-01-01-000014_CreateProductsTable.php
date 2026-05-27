<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'         => ['type' => 'VARCHAR', 'constraint' => 60],
            'company_un_id' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'product_name'  => ['type' => 'VARCHAR', 'constraint' => 200],
            'product_code'  => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'category'      => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'unit'          => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'kg'],
            'default_price' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'description'   => ['type' => 'TEXT', 'null' => true],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('company_un_id');
        $this->forge->addKey('category');
        $this->forge->addKey('status');
        $this->forge->addKey('product_code');
        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products', true);
    }
}
