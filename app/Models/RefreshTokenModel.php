<?php

namespace App\Models;

class RefreshTokenModel extends BaseModel
{
    protected $table          = 'refresh_tokens';
    protected string $unIdPrefix        = 'RFT';
    protected $useSoftDeletes = false;
    protected $allowedFields  = [
        'un_id', 'user_un_id', 'token_hash', 'expires_at', 'revoked_at',
    ];
    protected $updatedField = '';
}
