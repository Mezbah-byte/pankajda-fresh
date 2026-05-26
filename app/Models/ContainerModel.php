<?php

namespace App\Models;

class ContainerModel extends BaseModel
{
    protected $table         = 'containers';
    protected string $unIdPrefix       = 'CNT';
    protected $allowedFields = [
        'un_id', 'company_un_id', 'container_number', 'bl_number',
        'product_name', 'origin_country', 'arrival_date',
        'customs_status', 'customs_clear_date',
        'total_products', 'damaged_products', 'unit',
        'cost_total', 'customs_cost', 'transport_cost', 'other_cost',
        'notes', 'status',
    ];
}
