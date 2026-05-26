<?php

namespace App\Models;

class FarmActivityModel extends BaseModel
{
    protected $table              = 'farm_activities';
    protected string $unIdPrefix  = 'FAC';
    protected $allowedFields      = [
        'un_id', 'farm_project_un_id', 'activity_type', 'activity_date',
        'description', 'worker_name', 'worker_count',
        'seed_name', 'seed_quantity', 'seed_unit', 'cost', 'notes',
    ];
}
