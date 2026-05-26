<?php

namespace App\Models;

class RoleModel extends BaseModel
{
    protected $table         = 'roles';
    protected string $unIdPrefix       = 'ROL';
    protected $allowedFields = ['un_id', 'slug', 'name', 'description', 'is_system'];
}
