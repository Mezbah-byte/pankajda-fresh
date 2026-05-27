<?php

namespace App\Models;

class VendorPaymentModel extends BaseModel
{
    protected $table             = 'vendor_payments';
    protected string $unIdPrefix = 'VPY';
    protected $allowedFields     = [
        'un_id', 'vendor_un_id', 'amount', 'payment_date',
        'payment_method', 'reference_no', 'notes',
    ];

    protected $validationRules = [
        'vendor_un_id'  => 'required',
        'amount'        => 'required|numeric|greater_than[0]',
        'payment_date'  => 'required',
    ];
}
