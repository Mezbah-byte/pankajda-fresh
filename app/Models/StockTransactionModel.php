<?php

namespace App\Models;

class StockTransactionModel extends BaseModel
{
    protected $table             = 'stock_transactions';
    protected string $unIdPrefix = 'STT';

    protected $useSoftDeletes = false;
    protected $useTimestamps  = true;
    protected $updatedField   = '';   // no updated_at column
    protected $deletedField   = '';

    protected $allowedFields = [
        'un_id', 'stock_item_un_id', 'type', 'quantity',
        'unit_cost', 'reference', 'notes', 'txn_date', 'created_by_un_id',
    ];

    protected $validationRules = [
        'stock_item_un_id' => 'required',
        'type'             => 'required|in_list[in,out,adjustment]',
        'quantity'         => 'required|numeric|greater_than[0]',
    ];
}
