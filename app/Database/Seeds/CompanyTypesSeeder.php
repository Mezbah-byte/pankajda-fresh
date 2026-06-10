<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CompanyTypesSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('company_types')->countAllResults() > 0) {
            return;
        }
        $now   = date('Y-m-d H:i:s');
        $names = [
            'Trading', 'Import / Export', 'Farm', 'Service',
            'Manufacturing', 'Retail', 'Wholesale', 'Construction',
            'Technology', 'Logistics', 'Other',
        ];
        foreach ($names as $i => $name) {
            $this->db->table('company_types')->insert([
                'un_id'      => generate_un_id('CTP'),
                'name'       => $name,
                'sort_order' => ($i + 1) * 10,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
