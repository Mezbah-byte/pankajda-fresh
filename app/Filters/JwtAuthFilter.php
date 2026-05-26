<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * JwtAuthFilter - decodes the Bearer token, attaches the user payload
 * to the request as $request->auth_user, and rejects anonymous calls.
 *
 * Skips routes that have already been declared public (auth/login,
 * auth/register, auth/refresh).
 */
class JwtAuthFilter implements FilterInterface
{
    private array $publicPaths = [
        'api/v1/auth/login',
        'api/v1/auth/register',
        'api/v1/auth/refresh',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $path = trim($request->getUri()->getPath(), '/');

        foreach ($this->publicPaths as $public) {
            if ($path === $public) {
                return;
            }
        }

        $token = service('jwt')->extractFromRequest($request);
        if ($token === null) {
            return api_unauthorized('Missing or malformed Authorization header.');
        }

        try {
            $payload = service('jwt')->decode($token);
        } catch (\Throwable $e) {
            return api_unauthorized('Invalid or expired token.');
        }

        if (($payload['type'] ?? null) !== 'access') {
            return api_unauthorized('Wrong token type.');
        }

        // Look up the user to fetch role + active status
        $user = service('auth')->resolveUserByUnId($payload['sub'] ?? '');
        if ($user === null) {
            return api_unauthorized('User no longer exists.');
        }
        if (($user['status'] ?? STATUS_ACTIVE) !== STATUS_ACTIVE) {
            return api_forbidden('Account is not active.');
        }

        $request->auth_user = [
            'un_id' => $user['un_id'],
            'name'  => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'role'  => $user['role'] ?? null,
        ];
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
