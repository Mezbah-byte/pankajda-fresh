<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'        => ['type' => 'VARCHAR', 'constraint' => 60],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 150],
            'email'        => ['type' => 'VARCHAR', 'constraint' => 190],
            'phone'        => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'password_hash'=> ['type' => 'VARCHAR', 'constraint' => 255],
            'role'         => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'staff'],
            'avatar_path'  => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'last_login_at'=> ['type' => 'DATETIME', 'null' => true],
            'status'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addUniqueKey('email');
        $this->forge->addKey('role');
        $this->forge->addKey('status');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}
