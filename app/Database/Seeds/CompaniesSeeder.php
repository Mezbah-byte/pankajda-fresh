<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('companies')->countAllResults() > 0) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $rows = [
            ['Pankaj Da Trading Ltd.',  'Trading',     'Dhaka',     'BDT', 'active'],
            ['Pankaj Da Visa Services', 'Visa',        'Dhaka',     'BDT', 'active'],
            ['Pankaj Da Imports',       'Import',      'Chittagong','BDT', 'active'],
            ['Pankaj Da Farms',         'Agriculture', 'Khulna',    'BDT', 'active'],
            ['Pankaj Da Fresh Produce', 'Trading',     'Sylhet',    'BDT', 'pending'],
        ];
        foreach ($rows as [$name, $type, $city, $cur, $status]) {
            $this->db->table('companies')->insert([
                'un_id'         => generate_un_id('CMP'),
                'company_name'  => $name,
                'company_type'  => $type,
                'address'       => $city,
                'city'          => $city,
                'country'       => 'Bangladesh',
                'currency'      => $cur,
                'status'        => $status,
                'phone'         => '+8801' . random_int(700000000, 999999999),
                'email'         => strtolower(str_replace([' ', '.'], '', $name)) . '@example.com',
                'opening_balance' => random_int(0, 500000),
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }
}
