<?php

use CodeIgniter\HTTP\ResponseInterface;

/**
 * API Response Helper functions.
 *
 * Standardized JSON response structure across all API endpoints.
 * Always returns:
 *   { success: bool, message: string, data: mixed, meta: object|null, errors: object|null }
 */

if (! function_exists('api_success')) {
    function api_success($data = null, string $message = 'OK', int $status = 200, ?array $meta = null): ResponseInterface
    {
        return service('response')
            ->setStatusCode($status)
            ->setJSON([
                'success' => true,
                'message' => $message,
                'data'    => $data,
                'meta'    => $meta,
                'errors'  => null,
            ]);
    }
}

if (! function_exists('api_error')) {
    function api_error(string $message, int $status = 400, $errors = null): ResponseInterface
    {
        return service('response')
            ->setStatusCode($status)
            ->setJSON([
                'success' => false,
                'message' => $message,
                'data'    => null,
                'meta'    => null,
                'errors'  => $errors,
            ]);
    }
}

if (! function_exists('api_paginated')) {
    function api_paginated(array $items, int $page, int $perPage, int $total, string $message = 'OK'): ResponseInterface
    {
        return api_success($items, $message, 200, [
            'pagination' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => max(1, (int) ceil($total / max(1, $perPage))),
            ],
        ]);
    }
}

if (! function_exists('api_validation_error')) {
    function api_validation_error(array $errors, string $message = 'Validation failed'): ResponseInterface
    {
        return api_error($message, 422, $errors);
    }
}

if (! function_exists('api_unauthorized')) {
    function api_unauthorized(string $message = 'Unauthorized'): ResponseInterface
    {
        return api_error($message, 401);
    }
}

if (! function_exists('api_forbidden')) {
    function api_forbidden(string $message = 'Forbidden'): ResponseInterface
    {
        return api_error($message, 403);
    }
}

if (! function_exists('api_not_found')) {
    function api_not_found(string $message = 'Resource not found'): ResponseInterface
    {
        return api_error($message, 404);
    }
}
