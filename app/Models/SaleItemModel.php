<?php

namespace App\Models;

class SaleItemModel extends BaseModel
{
    protected $table         = 'sale_items';
    protected string $unIdPrefix       = 'ITM';
    protected $allowedFields = [
        'un_id', 'sale_un_id', 'product_name', 'quantity',
        'unit', 'unit_price', 'vat', 'total',
    ];
}
