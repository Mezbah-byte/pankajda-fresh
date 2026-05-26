<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Traits\ApiResponseTrait;

/**
 * BaseApiController - parent for all REST API controllers.
 *
 * Provides JSON response helpers (via ApiResponseTrait), pagination
 * parsing, and access to the JWT-authenticated user via getAuthUser().
 */
abstract class BaseApiController extends BaseController
{
    use ApiResponseTrait;

    /**
     * Read pagination params from the query string.
     * @return array{page:int,per_page:int}
     */
    protected function parsePagination(): array
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = (int) ($this->request->getGet('per_page') ?? 20);
        $perPage = max(1, min($perPage, 100));
        return ['page' => $page, 'per_page' => $perPage];
    }

    /**
     * Get the authenticated user info attached by JwtAuthFilter.
     * Returns ['un_id' => '...', 'role' => '...', ...] or null.
     */
    protected function getAuthUser(): ?array
    {
        $user = $this->request->auth_user ?? null;
        return is_array($user) ? $user : null;
    }

    protected function authUserUnId(): ?string
    {
        return $this->getAuthUser()['un_id'] ?? null;
    }
}
