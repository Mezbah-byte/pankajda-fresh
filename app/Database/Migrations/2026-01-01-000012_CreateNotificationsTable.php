<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'         => ['type' => 'VARCHAR', 'constraint' => 60],
            'user_un_id'    => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],  // null = broadcast
            'type'          => ['type' => 'VARCHAR', 'constraint' => 80],                   // e.g. 'sale.created'
            'title'         => ['type' => 'VARCHAR', 'constraint' => 200],
            'body'          => ['type' => 'TEXT', 'null' => true],
            'entity_type'   => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'entity_un_id'  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'link'          => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'read_at'       => ['type' => 'DATETIME', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('user_un_id');
        $this->forge->addKey('type');
        $this->forge->addKey('read_at');
        $this->forge->addKey('created_at');
        $this->forge->createTable('notifications');
    }

    public function down()
    {
        $this->forge->dropTable('notifications', true);
    }
}
