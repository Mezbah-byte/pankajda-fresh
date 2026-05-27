<?php

namespace App\Models;

class StockItemModel extends BaseModel
{
    protected $table             = 'stock_items';
    protected string $unIdPrefix = 'STK';

    protected $allowedFields = [
        'un_id', 'company_un_id', 'product_un_id', 'item_name',
        'category', 'unit', 'current_qty', 'min_qty', 'unit_cost', 'status',
    ];

    protected $validationRules = [
        'item_name' => 'required|min_length[2]|max_length[200]',
        'unit'      => 'required|max_length[40]',
    ];
}
