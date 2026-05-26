<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'           => ['type' => 'VARCHAR', 'constraint' => 60],
            'company_un_id'   => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'employee_code'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'name'            => ['type' => 'VARCHAR', 'constraint' => 150],
            'designation'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'department'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'phone'           => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'email'           => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'national_id'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'address'         => ['type' => 'TEXT', 'null' => true],
            'salary'          => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'joined_at'       => ['type' => 'DATE', 'null' => true],
            'status'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'photo_path'      => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'notes'           => ['type' => 'TEXT', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('company_un_id');
        $this->forge->addKey('status');
        $this->forge->createTable('employees');
    }

    public function down()
    {
        $this->forge->dropTable('employees', true);
    }
}
