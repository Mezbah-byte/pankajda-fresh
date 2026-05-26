<?php

namespace App\Models;

class FarmProjectModel extends BaseModel
{
    protected $table              = 'farm_projects';
    protected string $unIdPrefix  = 'FRM';
    protected $allowedFields      = [
        'un_id', 'company_un_id', 'project_name', 'crop_name',
        'land_size', 'land_unit', 'start_date', 'end_date',
        'total_cost', 'production_amount', 'production_unit',
        'sale_amount', 'profit', 'status', 'notes',
    ];

    protected $validationRules = [
        'project_name' => 'required|min_length[2]|max_length[200]',
    ];
}
