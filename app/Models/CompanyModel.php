<?php

namespace App\Models;

class CompanyModel extends BaseModel
{
    protected $table         = 'companies';
    protected string $unIdPrefix       = 'CMP';
    protected $allowedFields = [
        'un_id', 'company_name', 'company_type', 'trade_license', 'tax_id',
        'address', 'city', 'country', 'phone', 'email', 'website',
        'logo_path', 'currency', 'opening_balance', 'notes', 'status',
    ];

    protected $validationRules = [
        'company_name' => 'required|min_length[2]|max_length[200]',
        'email'        => 'permit_empty|valid_email|max_length[190]',
        'phone'        => 'permit_empty|max_length[30]',
        'currency'     => 'permit_empty|max_length[8]',
        'status'       => 'permit_empty|in_list[active,inactive,pending]',
    ];
}
