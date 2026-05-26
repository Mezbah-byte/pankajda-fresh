<?php

namespace App\Models;

class SalePaymentModel extends BaseModel
{
    protected $table         = 'sale_payments';
    protected string $unIdPrefix       = 'SPM';
    protected $allowedFields = [
        'un_id', 'sale_un_id', 'customer_un_id', 'amount',
        'payment_method', 'reference_no', 'payment_date',
        'notes', 'created_by_un_id',
    ];
}
