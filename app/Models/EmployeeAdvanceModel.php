<?php

namespace App\Models;

class EmployeeAdvanceModel extends BaseModel
{
    protected $table             = 'employee_advances';
    protected string $unIdPrefix = 'ADV';
    protected $allowedFields     = [
        'un_id', 'employee_un_id', 'amount', 'advance_date',
        'reason', 'repaid_amount', 'status',
    ];

    protected $validationRules = [
        'employee_un_id' => 'required',
        'amount'         => 'required|numeric|greater_than[0]',
        'advance_date'   => 'required',
    ];
}
