<?php

namespace App\Models;

class CompanyTypeModel extends BaseModel
{
    protected $table         = 'company_types';
    protected string $unIdPrefix  = 'CTP';
    protected $allowedFields = ['un_id', 'name', 'sort_order', 'is_active'];

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]',
    ];

    public function allActive(): array
    {
        return $this->where('is_active', 1)
            ->where('deleted_at', null)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
