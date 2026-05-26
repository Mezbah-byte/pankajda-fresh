<?php

namespace App\Models;

class VisaPaymentModel extends BaseModel
{
    protected $table         = 'visa_payments';
    protected string $unIdPrefix       = 'VPM';
    protected $allowedFields = [
        'un_id', 'visa_un_id', 'amount', 'payment_method',
        'reference_no', 'payment_date', 'notes', 'created_by_un_id',
    ];
}
