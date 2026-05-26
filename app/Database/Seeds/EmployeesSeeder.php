<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmployeesSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('employees')->countAllResults() > 0) {
            return;
        }
        $companies = $this->db->table('companies')->select('un_id')->get()->getResultArray();
        if (empty($companies)) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $employees = [
            ['Mohammad Hasan',   'Operations Manager', 'Operations',  45000],
            ['Fatima Akter',     'Accountant',         'Accounts',    38000],
            ['Abdul Karim',      'Field Supervisor',   'Operations',  28000],
            ['Nasima Begum',     'HR Officer',         'HR',          32000],
            ['Rezaul Islam',     'Sales Executive',    'Sales',       25000],
            ['Sumaiya Rahman',   'Admin Assistant',    'Admin',       22000],
            ['Mizanur Rahman',   'Driver',             'Operations',  18000],
            ['Tanvir Ahmed',     'IT Support',         'IT',          35000],
        ];
        foreach ($employees as $i => [$name, $role, $dept, $salary]) {
            $this->db->table('employees')->insert([
                'un_id'         => generate_un_id('EMP'),
                'company_un_id' => $companies[$i % count($companies)]['un_id'],
                'employee_code' => 'EMP' . str_pad((string) ($i + 1001), 5, '0', STR_PAD_LEFT),
                'name'          => $name,
                'designation'   => $role,
                'department'    => $dept,
                'phone'         => '+8801' . random_int(700000000, 999999999),
                'email'         => strtolower(str_replace(' ', '.', $name)) . '@pankajda.example',
                'salary'        => $salary,
                'joined_at'     => date('Y-m-d', strtotime('-' . random_int(30, 1500) . ' days')),
                'status'        => STATUS_ACTIVE,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }
}
