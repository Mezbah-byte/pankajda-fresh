<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $defaults = [
            ['key' => 'site.name',         'value' => 'Pankaj Da Business',          'type' => 'string', 'group' => 'general'],
            ['key' => 'site.tagline',      'value' => 'Modern ERP for Modern Business','type' => 'string','group' => 'general'],
            ['key' => 'site.email',        'value' => 'info@pankajda.example',       'type' => 'string', 'group' => 'general'],
            ['key' => 'site.phone',        'value' => '+880 1700-000000',            'type' => 'string', 'group' => 'general'],
            ['key' => 'site.address',      'value' => 'Dhaka, Bangladesh',           'type' => 'string', 'group' => 'general'],
            ['key' => 'finance.currency',  'value' => 'BDT',                         'type' => 'string', 'group' => 'finance'],
            ['key' => 'finance.tax_rate',  'value' => '0',                           'type' => 'number', 'group' => 'finance'],
            ['key' => 'invoice.prefix',    'value' => 'INV-',                        'type' => 'string', 'group' => 'invoice'],
            ['key' => 'invoice.start_no',  'value' => '1001',                        'type' => 'number', 'group' => 'invoice'],
        ];
        $now = date('Y-m-d H:i:s');
        foreach ($defaults as $row) {
            if ($this->db->table('settings')->where('key', $row['key'])->countAllResults() > 0) {
                continue;
            }
            $this->db->table('settings')->insert(array_merge($row, [
                'un_id'      => generate_un_id('SET'),
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }
}
