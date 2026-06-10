<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $defaults = [
            // General
            ['key' => 'site.name',              'value' => 'Pankaj Da Business',           'type' => 'string',   'group' => 'general'],
            ['key' => 'site.tagline',           'value' => 'Modern ERP for Modern Business','type' => 'string',   'group' => 'general'],
            ['key' => 'site.email',             'value' => 'info@pankajda.example',        'type' => 'string',   'group' => 'general'],
            ['key' => 'site.phone',             'value' => '+880 1700-000000',             'type' => 'string',   'group' => 'general'],
            ['key' => 'site.address',           'value' => 'Dhaka, Bangladesh',            'type' => 'string',   'group' => 'general'],
            ['key' => 'site.website',           'value' => '',                             'type' => 'string',   'group' => 'general'],

            // Finance
            ['key' => 'finance.currency',       'value' => 'BDT',                          'type' => 'string',   'group' => 'finance'],
            ['key' => 'finance.currency_symbol','value' => '৳',                            'type' => 'string',   'group' => 'finance'],
            ['key' => 'finance.tax_rate',       'value' => '0',                            'type' => 'number',   'group' => 'finance'],
            ['key' => 'finance.fiscal_year_start','value' => '01-01',                      'type' => 'string',   'group' => 'finance'],

            // Invoice
            ['key' => 'invoice.prefix',         'value' => 'INV-',                         'type' => 'string',   'group' => 'invoice'],
            ['key' => 'invoice.start_no',       'value' => '1001',                         'type' => 'number',   'group' => 'invoice'],
            ['key' => 'invoice.due_days',       'value' => '30',                           'type' => 'number',   'group' => 'invoice'],
            ['key' => 'invoice.footer_text',    'value' => 'Thank you for your business!', 'type' => 'string',   'group' => 'invoice'],
            ['key' => 'invoice.terms',          'value' => '',                             'type' => 'text',     'group' => 'invoice'],
            ['key' => 'invoice.show_bank_details','value' => '1',                          'type' => 'string',   'group' => 'invoice'],

            // System
            ['key' => 'system.date_format',     'value' => 'd M Y',                        'type' => 'string',   'group' => 'system'],
            ['key' => 'system.timezone',        'value' => 'Asia/Dhaka',                   'type' => 'string',   'group' => 'system'],
            ['key' => 'system.items_per_page',  'value' => '15',                           'type' => 'number',   'group' => 'system'],
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
