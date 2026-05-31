<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContainerCartonsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'               => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'            => ['type' => 'VARCHAR', 'constraint' => 60],
            'container_un_id'  => ['type' => 'VARCHAR', 'constraint' => 60],
            'carton_number'    => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'product_name'     => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'quantity'         => ['type' => 'DECIMAL', 'constraint' => '15,3', 'default' => 0],
            'unit'             => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pcs'],
            'weight_gross'     => ['type' => 'DECIMAL', 'constraint' => '10,3', 'null' => true],
            'weight_net'       => ['type' => 'DECIMAL', 'constraint' => '10,3', 'null' => true],
            'condition'        => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'good'],
            'notes'            => ['type' => 'TEXT', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('container_un_id');
        $this->forge->addKey('condition');
        $this->forge->createTable('container_cartons');
    }

    public function down()
    {
        $this->forge->dropTable('container_cartons', true);
    }
}
