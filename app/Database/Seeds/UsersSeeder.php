<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $users = [
            ['name' => 'Pankaj Da Admin',  'email' => 'admin@pankajda.example',     'password' => 'admin@1234', 'role' => ROLE_SUPER_ADMIN],
            ['name' => 'Manager Demo',     'email' => 'manager@pankajda.example',   'password' => 'manager@1234','role' => ROLE_MANAGER],
            ['name' => 'Accountant Demo',  'email' => 'accountant@pankajda.example','password' => 'account@1234','role' => ROLE_ACCOUNTANT],
        ];
        foreach ($users as $u) {
            if ($this->db->table('users')->where('email', $u['email'])->countAllResults() > 0) {
                continue;
            }
            $this->db->table('users')->insert([
                'un_id'         => generate_un_id('USR'),
                'name'          => $u['name'],
                'email'         => $u['email'],
                'password_hash' => password_hash($u['password'], PASSWORD_BCRYPT),
                'role'          => $u['role'],
                'status'        => STATUS_ACTIVE,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }
}
