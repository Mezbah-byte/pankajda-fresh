<?php

namespace App\Models;

class EmployeeModel extends BaseModel
{
    protected $table              = 'employees';
    protected string $unIdPrefix  = 'EMP';
    protected $allowedFields      = [
        'un_id', 'company_un_id', 'employee_code', 'name',
        'designation', 'department', 'phone', 'email',
        'national_id', 'address', 'salary', 'joined_at',
        'status', 'photo_path', 'notes',
    ];

    protected $validationRules = [
        'name'  => 'required|min_length[2]|max_length[150]',
        'email' => 'permit_empty|valid_email|max_length[190]',
    ];
}
