<?php

namespace App\Models;

class FarmProjectModel extends BaseModel
{
    protected $table              = 'farm_projects';
    protected string $unIdPrefix  = 'FRM';
    protected $allowedFields      = [
        'un_id', 'company_un_id', 'project_name', 'item_name',
        'quantity', 'quantity_unit', 'start_date', 'end_date',
        'total_rate', 'production_amount', 'production_unit',
        'sale_amount', 'profit', 'status', 'notes',
    ];

    protected $validationRules = [
        'project_name' => 'required|min_length[2]|max_length[200]',
    ];
}
