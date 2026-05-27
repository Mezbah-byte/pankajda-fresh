<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePayrollTable extends Migration
{
    public function up()
    {
        // --- payroll_records ---
        $this->forge->addField([
            'id'                  => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'               => ['type' => 'VARCHAR', 'constraint' => 60],
            'employee_un_id'      => ['type' => 'VARCHAR', 'constraint' => 60],
            'company_un_id'       => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'pay_period'          => ['type' => 'VARCHAR', 'constraint' => 20],
            'basic_salary'        => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'allowances'          => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'deductions'          => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'advance_deduction'   => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'net_salary'          => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'payment_method'      => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'cash'],
            'bank_account_un_id'  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'paid_at'             => ['type' => 'DATETIME', 'null' => true],
            'status'              => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'notes'               => ['type' => 'TEXT', 'null' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('employee_un_id');
        $this->forge->addKey('pay_period');
        $this->forge->createTable('payroll_records');

        // --- employee_advances ---
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'          => ['type' => 'VARCHAR', 'constraint' => 60],
            'employee_un_id' => ['type' => 'VARCHAR', 'constraint' => 60],
            'amount'         => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'advance_date'   => ['type' => 'DATE'],
            'reason'         => ['type' => 'TEXT', 'null' => true],
            'repaid_amount'  => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'status'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'outstanding'],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('employee_un_id');
        $this->forge->createTable('employee_advances');
    }

    public function down()
    {
        $this->forge->dropTable('payroll_records', true);
        $this->forge->dropTable('employee_advances', true);
    }
}
