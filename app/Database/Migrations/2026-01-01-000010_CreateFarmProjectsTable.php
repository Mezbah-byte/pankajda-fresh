<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFarmProjectsTable extends Migration
{
    public function up()
    {
        // Farm projects
        $this->forge->addField([
            'id'                => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'             => ['type' => 'VARCHAR', 'constraint' => 60],
            'company_un_id'     => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'project_name'      => ['type' => 'VARCHAR', 'constraint' => 200],
            'crop_name'         => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'land_size'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'land_unit'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'acre'],
            'start_date'        => ['type' => 'DATE', 'null' => true],
            'end_date'          => ['type' => 'DATE', 'null' => true],
            'total_cost'        => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'production_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'production_unit'   => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'kg'],
            'sale_amount'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'profit'            => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'status'            => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'active'],
            'notes'             => ['type' => 'TEXT', 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('company_un_id');
        $this->forge->addKey('status');
        $this->forge->createTable('farm_projects');

        // Farm activities (workers, seeds, costs)
        $this->forge->addField([
            'id'                  => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'               => ['type' => 'VARCHAR', 'constraint' => 60],
            'farm_project_un_id'  => ['type' => 'VARCHAR', 'constraint' => 60],
            'activity_type'       => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'general'],
            'activity_date'       => ['type' => 'DATE'],
            'description'         => ['type' => 'TEXT', 'null' => true],
            'worker_name'         => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'worker_count'        => ['type' => 'INT', 'default' => 0],
            'seed_name'           => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'seed_quantity'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'seed_unit'           => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'cost'                => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'notes'               => ['type' => 'TEXT', 'null' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('farm_project_un_id');
        $this->forge->addKey('activity_type');
        $this->forge->addKey('activity_date');
        $this->forge->createTable('farm_activities');
    }

    public function down()
    {
        $this->forge->dropTable('farm_activities', true);
        $this->forge->dropTable('farm_projects', true);
    }
}
