<?php

namespace App\Models;

class GrvModel extends BaseModel
{
    protected $table             = 'goods_return_vouchers';
    protected string $unIdPrefix = 'GRV';
    protected $allowedFields     = [
        'un_id', 'grv_no', 'customer_un_id', 'company_un_id', 'sale_un_id',
        'grv_date', 'description', 'total_amount', 'status', 'notes', 'created_by_un_id',
    ];

    protected $validationRules = [
        'customer_un_id' => 'required',
        'grv_date'       => 'required|valid_date',
        'total_amount'   => 'permit_empty|decimal',
    ];
}
