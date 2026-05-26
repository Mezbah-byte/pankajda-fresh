<?php

namespace App\Models;

class CustomerModel extends BaseModel
{
    protected $table         = 'customers';
    protected string $unIdPrefix       = 'CUS';
    protected $allowedFields = [
        'un_id', 'company_un_id', 'customer_code', 'customer_name',
        'phone', 'email', 'address', 'city',
        'opening_balance', 'current_due', 'credit_limit',
        'notes', 'status',
    ];

    protected $validationRules = [
        'customer_name' => 'required|min_length[2]|max_length[200]',
        'email'         => 'permit_empty|valid_email|max_length[190]',
        'phone'         => 'permit_empty|max_length[30]',
    ];
}
