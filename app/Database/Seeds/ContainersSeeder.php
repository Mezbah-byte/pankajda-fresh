<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ContainersSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('containers')->countAllResults() > 0) {
            return;
        }
        $companies = $this->db->table('companies')->select('un_id')->get()->getResultArray();
        if (empty($companies)) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $products = ['Onion', 'Potato', 'Garlic', 'Ginger', 'Apple', 'Orange', 'Pomegranate', 'Dates'];
        $origins  = ['India', 'China', 'Egypt', 'Pakistan', 'Turkey', 'South Africa'];
        for ($i = 0; $i < 8; $i++) {
            $product = $products[array_rand($products)];
            $total   = random_int(15000, 28000);
            $damaged = random_int(50, 800);
            $cost    = random_int(800000, 2400000);
            $this->db->table('containers')->insert([
                'un_id'              => generate_un_id('CNT'),
                'company_un_id'      => $companies[$i % count($companies)]['un_id'],
                'container_number'   => 'PNDA' . random_int(1000000, 9999999),
                'bl_number'          => 'BL' . random_int(100000, 999999),
                'product_name'       => $product,
                'origin_country'     => $origins[array_rand($origins)],
                'arrival_date'       => date('Y-m-d', strtotime('-' . random_int(2, 90) . ' days')),
                'customs_status'     => ['cleared', 'pending', 'cleared', 'cleared'][array_rand([0,1,2,3])],
                'customs_clear_date' => date('Y-m-d', strtotime('-' . random_int(1, 80) . ' days')),
                'total_products'     => $total,
                'damaged_products'   => $damaged,
                'unit'               => 'kg',
                'cost_total'         => $cost,
                'customs_cost'       => (int) ($cost * 0.08),
                'transport_cost'     => (int) ($cost * 0.04),
                'other_cost'         => (int) ($cost * 0.02),
                'status'             => ['received', 'in_transit', 'received', 'sold'][array_rand([0,1,2,3])],
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }
    }
}
