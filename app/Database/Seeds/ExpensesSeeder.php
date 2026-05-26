<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ExpensesSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('expenses')->countAllResults() > 0) {
            return;
        }
        $companies = $this->db->table('companies')->select('un_id')->get()->getResultArray();
        if (empty($companies)) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $items = [
            ['Office Rent',          'rent',         55000],
            ['Electricity Bill',     'utilities',     8500],
            ['Internet Bill',        'utilities',     2500],
            ['Office Supplies',      'office',        4200],
            ['Vehicle Fuel',         'transport',    12000],
            ['Marketing - Facebook', 'marketing',    15000],
            ['Staff Lunch',          'food',          3800],
            ['Bank Charges',         'banking',       1200],
            ['Customs Clearing Fee', 'customs',     22000],
            ['Cleaning Service',     'maintenance',   3500],
            ['Phone Bills',          'utilities',     2800],
            ['Stationery',           'office',        1800],
        ];
        foreach ($items as $i => [$title, $cat, $amount]) {
            $this->db->table('expenses')->insert([
                'un_id'         => generate_un_id('EXP'),
                'company_un_id' => $companies[$i % count($companies)]['un_id'],
                'expense_title' => $title,
                'category'      => $cat,
                'amount'        => $amount + random_int(-500, 500),
                'expense_date'  => date('Y-m-d', strtotime('-' . random_int(0, 28) . ' days')),
                'payment_method'=> ['cash', 'bank_transfer', 'cash', 'mfs'][array_rand([0,1,2,3])],
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }
}
