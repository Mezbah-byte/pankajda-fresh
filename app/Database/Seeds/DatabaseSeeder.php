<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * DatabaseSeeder - top-level seeder that calls all module seeders.
 *
 * Order matters: roles & users come first, then companies (referenced
 * by every other table via company_un_id), then the business data.
 *
 * Run via: php spark db:seed DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Identity & access
        $this->call('RolesSeeder');
        $this->call('UsersSeeder');

        // Master data
        $this->call('CompaniesSeeder');
        $this->call('CustomersSeeder');
        $this->call('EmployeesSeeder');

        // Operations
        $this->call('VisasSeeder');
        $this->call('ContainersSeeder');
        $this->call('SalesSeeder');
        $this->call('ExpensesSeeder');
        $this->call('FarmProjectsSeeder');
        $this->call('SettingsSeeder');
    }
}
