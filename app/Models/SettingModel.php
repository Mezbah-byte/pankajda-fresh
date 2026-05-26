<?php

namespace App\Models;

class SettingModel extends BaseModel
{
    protected $table              = 'settings';
    protected string $unIdPrefix  = 'SET';
    protected $useSoftDeletes     = false;
    protected $allowedFields      = ['un_id', 'key', 'value', 'type', 'group'];
    protected $deletedField       = '';
}
