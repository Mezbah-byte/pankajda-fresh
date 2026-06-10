<?php

namespace App\Models;

class ProductModel extends BaseModel
{
    protected $table             = 'products';
    protected string $unIdPrefix = 'PRD';
    protected $allowedFields     = [
        'un_id', 'company_un_id', 'vendor_un_id', 'product_name', 'product_code',
        'category', 'unit', 'default_price', 'cost_price', 'description', 'status',
    ];

    protected $validationRules = [
        'product_name' => 'required|min_length[2]|max_length[200]',
    ];
}
