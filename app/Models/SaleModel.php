<?php

namespace App\Models;

class SaleModel extends BaseModel
{
    protected $table         = 'sales';
    protected string $unIdPrefix       = 'SAL';
    protected $allowedFields = [
        'un_id', 'invoice_no', 'company_un_id', 'customer_un_id',
        'container_un_id', 'sale_type', 'sale_date',
        'subtotal', 'discount', 'tax', 'total_amount',
        'paid_amount', 'due_amount', 'payment_status',
        'notes', 'created_by_un_id',
    ];

    protected $validationRules = [
        'invoice_no'  => 'required',
        'sale_date'   => 'required',
        'sale_type'   => 'required|in_list[cash,credit]',
    ];
}
