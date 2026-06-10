<?php

namespace App\Models;

class CountryModel extends BaseModel
{
    protected $table         = 'countries';
    protected string $unIdPrefix  = 'CTY';
    protected $allowedFields = ['un_id', 'name', 'iso_code', 'sort_order', 'is_active'];

    protected $validationRules = [
        'name'     => 'required|min_length[2]|max_length[100]',
        'iso_code' => 'permit_empty|max_length[3]',
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
