<?php

namespace App\Models;

class PermissionModel extends BaseModel
{
    protected $table         = 'permissions';
    protected string $unIdPrefix       = 'PRM';
    protected $allowedFields = ['un_id', 'slug', 'name', 'group'];
}
