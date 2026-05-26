<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class VisasSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('visas')->countAllResults() > 0) {
            return;
        }
        $companies = $this->db->table('companies')
            ->select('un_id')
            ->where('company_type', 'Visa')
            ->orWhere('company_type', 'Trading')
            ->get()->getResultArray();
        if (empty($companies)) {
            $companies = $this->db->table('companies')->select('un_id')->limit(2)->get()->getResultArray();
        }
        $now = date('Y-m-d H:i:s');
        $samples = [
            ['Saudi Arabia Work Visa',     'Saudi Arabia',     'Work',     350000],
            ['Malaysia Tourist Visa',      'Malaysia',         'Tourist',  85000],
            ['UAE Business Visa',          'UAE',              'Business', 280000],
            ['Qatar Work Visa',            'Qatar',            'Work',     320000],
            ['Singapore Tourist Visa',     'Singapore',        'Tourist',  120000],
            ['Oman Work Visa',             'Oman',             'Work',     290000],
            ['Kuwait Family Visa',         'Kuwait',           'Family',   220000],
            ['Bahrain Work Visa',          'Bahrain',          'Work',     310000],
        ];
        $beneficiaries = ['Mohammad Ali', 'Rahim Uddin', 'Karim Hossain', 'Nazrul Islam', 'Shamim Ahmed', 'Faruk Khan'];
        foreach ($samples as $i => [$name, $country, $cat, $cost]) {
            $paid = (int) ($cost * (random_int(20, 100) / 100));
            $due  = max(0, $cost - $paid);
            $status = $due === 0 ? PAYMENT_PAID : ($paid > 0 ? PAYMENT_PARTIAL : PAYMENT_DUE);
            $this->db->table('visas')->insert([
                'un_id'            => generate_un_id('VSA'),
                'company_un_id'    => $companies[$i % count($companies)]['un_id'],
                'visa_name'        => $name,
                'visa_number'      => 'V-' . strtoupper(substr(md5((string) $i), 0, 8)),
                'country'          => $country,
                'category'         => $cat,
                'beneficiary_name' => $beneficiaries[array_rand($beneficiaries)],
                'passport_no'      => 'BD' . random_int(1000000, 9999999),
                'visa_cost'        => $cost,
                'paid_amount'      => $paid,
                'due_amount'       => $due,
                'payment_status'   => $status,
                'visa_issue_date'  => date('Y-m-d', strtotime('-' . random_int(10, 200) . ' days')),
                'visa_expiry_date' => date('Y-m-d', strtotime('+' . random_int(60, 720) . ' days')),
                'status'           => STATUS_ACTIVE,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }
    }
}
