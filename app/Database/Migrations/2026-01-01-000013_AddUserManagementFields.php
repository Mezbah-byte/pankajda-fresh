<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserManagementFields extends Migration
{
    public function up()
    {
        // The users table already has status, last_login_at, and avatar_path
        // from migration 000001. We add the `avatar` alias column if missing.
        // If the columns already exist this migration is a no-op guard.
        $fields = [];

        $existingColumns = $this->db->getFieldNames('users');

        if (! in_array('status', $existingColumns, true)) {
            $fields['status'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'active',
                'null'       => false,
                'after'      => 'role',
            ];
        }

        if (! in_array('last_login_at', $existingColumns, true)) {
            $fields['last_login_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status',
            ];
        }

        if (! in_array('avatar', $existingColumns, true)) {
            $fields['avatar'] = [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'last_login_at',
            ];
        }

        if (! empty($fields)) {
            $this->forge->addColumn('users', $fields);
        }
    }

    public function down()
    {
        $existingColumns = $this->db->getFieldNames('users');

        if (in_array('avatar', $existingColumns, true)) {
            $this->forge->dropColumn('users', 'avatar');
        }
    }
}
