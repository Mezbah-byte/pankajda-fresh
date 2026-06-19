<?php

namespace App\Models;

class PurchaseItemModel extends BaseModel
{
    protected $table             = 'purchase_items';
    protected string $unIdPrefix = 'PUI';
    protected $allowedFields     = [
        'un_id', 'purchase_un_id', 'product_un_id', 'product_name',
        'unit', 'quantity', 'unit_cost', 'total',
    ];
}
