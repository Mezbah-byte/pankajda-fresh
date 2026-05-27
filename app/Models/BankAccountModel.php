<?php

namespace App\Models;

class BankAccountModel extends BaseModel
{
    protected $table             = 'bank_accounts';
    protected string $unIdPrefix = 'BNK';
    protected $allowedFields     = [
        'un_id', 'company_un_id', 'account_name', 'bank_name',
        'account_number', 'branch', 'routing_number', 'account_type',
        'opening_balance', 'current_balance', 'currency', 'status', 'notes',
    ];

    protected $validationRules = [
        'account_name'   => 'required|min_length[2]|max_length[200]',
        'bank_name'      => 'required|min_length[2]|max_length[150]',
        'account_number' => 'required|max_length[100]',
    ];
}
