<?php

namespace App\Models;

class VisaModel extends BaseModel
{
    protected $table         = 'visas';
    protected string $unIdPrefix       = 'VSA';
    protected $allowedFields = [
        'un_id', 'company_un_id', 'visa_name', 'visa_number',
        'country', 'from_country', 'category', 'beneficiary_name', 'passport_no',
        'visa_cost', 'paid_amount', 'due_amount', 'payment_status',
        'visa_issue_date', 'visa_expiry_date',
        'work_permit_number', 'work_permit_issue_date', 'work_permit_expiry_date',
        'purchase_price', 'selling_price', 'extra_costs', 'profit',
        'status', 'notes',
    ];

    protected $validationRules = [
        'visa_name'      => 'required|min_length[2]|max_length[200]',
        'company_un_id'  => 'required',
        'visa_cost'      => 'permit_empty|numeric',
        'payment_status' => 'permit_empty|in_list[paid,partial,due]',
    ];
}
