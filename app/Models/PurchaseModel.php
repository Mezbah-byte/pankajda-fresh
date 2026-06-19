<?php

namespace App\Models;

class PurchaseModel extends BaseModel
{
    protected $table             = 'purchases';
    protected string $unIdPrefix = 'PUR';
    protected $allowedFields     = [
        'un_id', 'purchase_no', 'vendor_un_id', 'company_un_id',
        'purchase_date', 'subtotal', 'discount', 'total_amount',
        'paid_amount', 'due_amount', 'status', 'notes', 'created_by_un_id',
    ];
}
