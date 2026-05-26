<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $roles = [
            ['slug' => ROLE_SUPER_ADMIN, 'name' => 'Super Admin',  'description' => 'Full system access',           'is_system' => 1],
            ['slug' => ROLE_ADMIN,       'name' => 'Admin',        'description' => 'Administrative access',         'is_system' => 1],
            ['slug' => ROLE_MANAGER,     'name' => 'Manager',      'description' => 'Manages teams and operations',  'is_system' => 0],
            ['slug' => ROLE_ACCOUNTANT,  'name' => 'Accountant',   'description' => 'Manages payments and reports',  'is_system' => 0],
            ['slug' => ROLE_STAFF,       'name' => 'Staff',        'description' => 'Standard staff access',         'is_system' => 0],
        ];
        foreach ($roles as $r) {
            // skip if exists
            if ($this->db->table('roles')->where('slug', $r['slug'])->countAllResults() > 0) {
                continue;
            }
            $this->db->table('roles')->insert(array_merge($r, [
                'un_id'      => generate_un_id('ROL'),
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        $perms = [
            ['slug' => 'company.view',   'name' => 'View companies',   'group' => 'company'],
            ['slug' => 'company.manage', 'name' => 'Manage companies', 'group' => 'company'],
            ['slug' => 'visa.view',      'name' => 'View visas',       'group' => 'visa'],
            ['slug' => 'visa.manage',    'name' => 'Manage visas',     'group' => 'visa'],
            ['slug' => 'sale.view',      'name' => 'View sales',       'group' => 'sale'],
            ['slug' => 'sale.manage',    'name' => 'Manage sales',     'group' => 'sale'],
            ['slug' => 'report.view',    'name' => 'View reports',     'group' => 'report'],
        ];
        foreach ($perms as $p) {
            if ($this->db->table('permissions')->where('slug', $p['slug'])->countAllResults() > 0) {
                continue;
            }
            $this->db->table('permissions')->insert(array_merge($p, [
                'un_id'      => generate_un_id('PRM'),
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }
}
