<?php

namespace App\Models;

class VendorModel extends BaseModel
{
    protected $table             = 'vendors';
    protected string $unIdPrefix = 'VND';
    protected $allowedFields     = [
        'un_id', 'company_un_id', 'vendor_name', 'vendor_code',
        'contact_person', 'phone', 'email', 'address', 'city',
        'country', 'product_category', 'payment_terms',
        'current_payable', 'status', 'notes',
    ];

    protected $validationRules = [
        'vendor_name' => 'required|min_length[2]|max_length[200]',
        'email'       => 'permit_empty|valid_email|max_length[190]',
    ];
}
