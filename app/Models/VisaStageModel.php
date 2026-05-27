<?php

namespace App\Models;

class VisaStageModel extends BaseModel
{
    protected $table             = 'visa_stages';
    protected string $unIdPrefix = 'VST';

    protected $useSoftDeletes = false;
    protected $useTimestamps  = true;
    protected $updatedField   = '';
    protected $deletedField   = '';

    protected $allowedFields = [
        'un_id', 'visa_un_id', 'stage', 'notes', 'stage_date', 'changed_by_un_id',
    ];

    protected $validationRules = [
        'visa_un_id'  => 'required',
        'stage'       => 'required|max_length[80]',
        'stage_date'  => 'required|valid_date',
    ];
}
