<?php

namespace App\Models;

class PayrollModel extends BaseModel
{
    protected $table             = 'payroll_records';
    protected string $unIdPrefix = 'PAY';
    protected $allowedFields     = [
        'un_id', 'employee_un_id', 'company_un_id', 'pay_period',
        'basic_salary', 'allowances', 'deductions', 'advance_deduction',
        'net_salary', 'payment_method', 'bank_account_un_id',
        'paid_at', 'status', 'notes',
    ];

    protected $validationRules = [
        'employee_un_id' => 'required',
        'pay_period'     => 'required|regex_match[/^\d{4}-\d{2}$/]',
    ];
}
