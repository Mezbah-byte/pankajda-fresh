<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCountriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'      => ['type' => 'VARCHAR', 'constraint' => 60],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'iso_code'   => ['type' => 'VARCHAR', 'constraint' => 3, 'null' => true],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('name');
        $this->forge->addKey('is_active');
        $this->forge->createTable('countries');
    }

    public function down()
    {
        $this->forge->dropTable('countries', true);
    }
}
