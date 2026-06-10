<?php

namespace App\Models;

class VisaExtraCostModel extends BaseModel
{
    protected $table             = 'visa_extra_costs';
    protected string $unIdPrefix = 'VEC';
    protected $allowedFields     = [
        'un_id', 'visa_un_id', 'description', 'amount',
    ];
}
