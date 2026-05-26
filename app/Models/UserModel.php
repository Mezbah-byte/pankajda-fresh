<?php

namespace App\Models;

class UserModel extends BaseModel
{
    protected $table         = 'users';
    protected string $unIdPrefix       = 'USR';
    protected $allowedFields = [
        'un_id', 'name', 'email', 'phone', 'password_hash',
        'role', 'avatar_path', 'last_login_at', 'status',
    ];

    protected $validationRules = [
        'name'          => 'required|max_length[150]',
        'email'         => 'required|valid_email|max_length[190]',
        'password_hash' => 'required',
    ];
}
