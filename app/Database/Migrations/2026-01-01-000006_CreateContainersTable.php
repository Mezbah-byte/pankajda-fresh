<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContainersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                 => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'              => ['type' => 'VARCHAR', 'constraint' => 60],
            'company_un_id'      => ['type' => 'VARCHAR', 'constraint' => 60],
            'container_number'   => ['type' => 'VARCHAR', 'constraint' => 80],
            'bl_number'          => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'product_name'       => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'origin_country'     => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'arrival_date'       => ['type' => 'DATE', 'null' => true],
            'customs_status'     => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'pending'],
            'customs_clear_date' => ['type' => 'DATE', 'null' => true],
            'total_products'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'damaged_products'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'unit'               => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'kg'],
            'cost_total'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'customs_cost'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'transport_cost'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'other_cost'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'notes'              => ['type' => 'TEXT', 'null' => true],
            'status'             => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'in_transit'],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('company_un_id');
        $this->forge->addKey('container_number');
        $this->forge->addKey('status');
        $this->forge->createTable('containers');
    }

    public function down()
    {
        $this->forge->dropTable('containers', true);
    }
}
