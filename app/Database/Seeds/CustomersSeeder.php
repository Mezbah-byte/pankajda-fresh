<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomersSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('customers')->countAllResults() > 0) {
            return;
        }
        $companies = $this->db->table('companies')->select('un_id')->get()->getResultArray();
        if (empty($companies)) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $names = [
            'Karim Brothers',  'Sonali Bazar',     'Green Valley Mart',
            'Rahim & Sons',    'City Fresh Mart',  'Padma Trading',
            'Bismillah Stores','Meghna Traders',   'Modhumoti Bazar',
            'Sunrise Wholesale','Global Imports',  'Bay of Bengal Co.',
        ];
        foreach ($names as $i => $name) {
            $this->db->table('customers')->insert([
                'un_id'           => generate_un_id('CUS'),
                'company_un_id'   => $companies[$i % count($companies)]['un_id'],
                'customer_code'   => 'CUS' . str_pad((string) ($i + 1001), 5, '0', STR_PAD_LEFT),
                'customer_name'   => $name,
                'phone'           => '+8801' . random_int(700000000, 999999999),
                'email'           => strtolower(str_replace([' ', '&', '.'], '', $name)) . '@example.com',
                'city'            => ['Dhaka', 'Chittagong', 'Sylhet', 'Khulna', 'Rajshahi'][array_rand([0,1,2,3,4])],
                'opening_balance' => random_int(0, 50000),
                'current_due'     => random_int(0, 80000),
                'credit_limit'    => random_int(50000, 300000),
                'status'          => STATUS_ACTIVE,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }
    }
}
