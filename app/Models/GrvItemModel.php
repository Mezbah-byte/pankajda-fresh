<?php

namespace App\Models;

class GrvItemModel extends BaseModel
{
    protected $table             = 'grv_items';
    protected string $unIdPrefix = 'GRI';
    protected $allowedFields     = [
        'un_id', 'grv_un_id', 'product_un_id', 'product_name',
        'unit', 'quantity', 'unit_price', 'reason',
    ];
}
