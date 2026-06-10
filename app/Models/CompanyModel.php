<?php

namespace App\Models;

class CompanyModel extends BaseModel
{
    protected $table         = 'companies';
    protected string $unIdPrefix       = 'CMP';
    protected $allowedFields = [
        'un_id', 'company_name', 'company_type', 'trade_license', 'tax_id',
        'address', 'city', 'country', 'phone', 'fax', 'contact_person', 'email', 'website',
        'logo_path', 'currency', 'opening_balance', 'notes', 'status',
        'bank_name', 'bank_account', 'bank_routing', 'established_date',
    ];

    protected $validationRules = [
        'company_name'     => 'required|min_length[2]|max_length[200]',
        'email'            => 'permit_empty|valid_email|max_length[190]',
        'phone'            => 'permit_empty|max_length[30]',
        'fax'              => 'permit_empty|max_length[30]',
        'currency'         => 'permit_empty|max_length[8]',
        'status'           => 'permit_empty|in_list[active,inactive,pending]',
        'established_date' => 'permit_empty|valid_date[Y-m-d]',
    ];
}
