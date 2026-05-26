<?php

namespace App\Traits;

use CodeIgniter\HTTP\ResponseInterface;

/**
 * ApiResponseTrait - thin convenience wrappers for controllers.
 *
 * Underlying logic lives in the response_helper functions, this trait
 * simply makes them available as $this->* methods inside controllers.
 */
trait ApiResponseTrait
{
    protected function ok($data = null, string $message = 'OK', ?array $meta = null): ResponseInterface
    {
        return api_success($data, $message, 200, $meta);
    }

    protected function created($data = null, string $message = 'Created'): ResponseInterface
    {
        return api_success($data, $message, 201);
    }

    protected function noContent(): ResponseInterface
    {
        return service('response')->setStatusCode(204);
    }

    protected function failValidation(array $errors): ResponseInterface
    {
        return api_validation_error($errors);
    }

    protected function failNotFound(string $message = 'Resource not found'): ResponseInterface
    {
        return api_not_found($message);
    }

    protected function failUnauthorized(string $message = 'Unauthorized'): ResponseInterface
    {
        return api_unauthorized($message);
    }

    protected function failForbidden(string $message = 'Forbidden'): ResponseInterface
    {
        return api_forbidden($message);
    }

    protected function failServer(string $message = 'Internal server error'): ResponseInterface
    {
        return api_error($message, 500);
    }

    protected function paginated(array $items, int $page, int $perPage, int $total): ResponseInterface
    {
        return api_paginated($items, $page, $perPage, $total);
    }
}
