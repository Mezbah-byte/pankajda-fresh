<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FarmProjectsSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('farm_projects')->countAllResults() > 0) {
            return;
        }
        $farmCompanies = $this->db->table('companies')
            ->select('un_id')
            ->where('company_type', 'Agriculture')
            ->get()->getResultArray();
        if (empty($farmCompanies)) {
            $farmCompanies = $this->db->table('companies')->select('un_id')->limit(1)->get()->getResultArray();
        }
        if (empty($farmCompanies)) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $projects = [
            ['Boro Rice Field',     'Boro Rice',  12.5, 'acre',  450000, 18000, 'kg', 720000],
            ['Winter Vegetables',   'Cauliflower', 5.0, 'acre',  180000,  8500, 'kg', 295000],
            ['Mango Orchard',       'Mango',      18.0, 'acre',  280000, 12000, 'kg', 480000],
            ['Potato Cultivation',  'Potato',      8.0, 'acre',  220000, 26000, 'kg', 410000],
        ];

        foreach ($projects as $i => [$name, $crop, $size, $unit, $cost, $prod, $prodUnit, $sale]) {
            $profit = $sale - $cost;
            $projectUnId = generate_un_id('FRM');
            $this->db->table('farm_projects')->insert([
                'un_id'             => $projectUnId,
                'company_un_id'     => $farmCompanies[$i % count($farmCompanies)]['un_id'],
                'project_name'      => $name,
                'crop_name'         => $crop,
                'land_size'         => $size,
                'land_unit'         => $unit,
                'start_date'        => date('Y-m-d', strtotime('-' . random_int(60, 200) . ' days')),
                'end_date'          => date('Y-m-d', strtotime('+' . random_int(0, 90) . ' days')),
                'total_cost'        => $cost,
                'production_amount' => $prod,
                'production_unit'   => $prodUnit,
                'sale_amount'       => $sale,
                'profit'            => $profit,
                'status'            => 'active',
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            // a couple of activities per project
            $activities = [
                ['workers', 'Land preparation',  ['worker_count' => 8],                                 ['cost' => 18000]],
                ['seeds',   'Seed sowing',       ['seed_name' => $crop . ' Seeds', 'seed_quantity' => $size * 50, 'seed_unit' => 'kg'], ['cost' => 22000]],
                ['workers', 'Harvesting',        ['worker_count' => 12],                                ['cost' => 38000]],
            ];
            foreach ($activities as $j => [$type, $desc, $extra, $costData]) {
                $this->db->table('farm_activities')->insert(array_merge([
                    'un_id'              => generate_un_id('FAC'),
                    'farm_project_un_id' => $projectUnId,
                    'activity_type'      => $type,
                    'activity_date'      => date('Y-m-d', strtotime("-" . (40 - $j * 14) . " days")),
                    'description'        => $desc,
                    'cost'               => $costData['cost'],
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ], $extra));
            }
        }
    }
}
