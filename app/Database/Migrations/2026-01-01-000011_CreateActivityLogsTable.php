<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'        => ['type' => 'VARCHAR', 'constraint' => 60],
            'user_un_id'   => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'action'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'entity_type'  => ['type' => 'VARCHAR', 'constraint' => 80],
            'entity_un_id' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'context'      => ['type' => 'JSON', 'null' => true],
            'ip_address'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'user_agent'   => ['type' => 'VARCHAR', 'constraint' => 250, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('user_un_id');
        $this->forge->addKey('entity_type');
        $this->forge->addKey('entity_un_id');
        $this->forge->addKey('created_at');
        $this->forge->createTable('activity_logs');

        // Settings
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'      => ['type' => 'VARCHAR', 'constraint' => 60],
            'key'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'value'      => ['type' => 'TEXT', 'null' => true],
            'type'       => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'string'],
            'group'      => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'general'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addUniqueKey('key');
        $this->forge->createTable('settings');

        // Refresh tokens (server-side store of issued refresh tokens for revocation)
        $this->forge->addField([
            'id'           => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'        => ['type' => 'VARCHAR', 'constraint' => 60],
            'user_un_id'   => ['type' => 'VARCHAR', 'constraint' => 60],
            'token_hash'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'expires_at'   => ['type' => 'DATETIME'],
            'revoked_at'   => ['type' => 'DATETIME', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('user_un_id');
        $this->forge->addKey('token_hash');
        $this->forge->createTable('refresh_tokens');
    }

    public function down()
    {
        $this->forge->dropTable('refresh_tokens', true);
        $this->forge->dropTable('settings', true);
        $this->forge->dropTable('activity_logs', true);
    }
}
