<?php

namespace App\Models;

class ExpenseModel extends BaseModel
{
    protected $table              = 'expenses';
    protected string $unIdPrefix  = 'EXP';
    protected $allowedFields      = [
        'un_id', 'company_un_id', 'container_un_id', 'expense_title', 'category',
        'amount', 'expense_date', 'payment_method', 'bank_account_un_id', 'reference_no',
        'notes', 'attachment_path', 'created_by_un_id',
    ];

    protected $validationRules = [
        'expense_title' => 'required|min_length[2]|max_length[200]',
        'amount'        => 'required|numeric',
        'expense_date'  => 'required',
    ];
}
