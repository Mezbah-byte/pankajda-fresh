<?php

namespace App\Models;

class ContainerCartonModel extends BaseModel
{
    protected $table         = 'container_cartons';
    protected string $unIdPrefix       = 'CRT';
    protected $allowedFields = [
        'un_id', 'container_un_id', 'carton_number', 'product_name',
        'quantity', 'unit', 'weight_gross', 'weight_net',
        'condition', 'notes',
    ];
}
