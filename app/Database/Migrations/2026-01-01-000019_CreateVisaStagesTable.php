<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVisaStagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'               => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'            => ['type' => 'VARCHAR', 'constraint' => 60],
            'visa_un_id'       => ['type' => 'VARCHAR', 'constraint' => 60],
            'stage'            => ['type' => 'VARCHAR', 'constraint' => 80],
            'notes'            => ['type' => 'TEXT', 'null' => true],
            'stage_date'       => ['type' => 'DATE'],
            'changed_by_un_id' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('visa_un_id');
        $this->forge->addKey('stage');
        $this->forge->createTable('visa_stages');
    }

    public function down()
    {
        $this->forge->dropTable('visa_stages', true);
    }
}
