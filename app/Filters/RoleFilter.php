<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * RoleFilter - guard a route by required role(s).
 *
 * Usage in routes:
 *   $routes->get('admin/companies', '...', ['filter' => 'role:admin,super_admin']);
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = $request->auth_user ?? null;
        if (! is_array($user)) {
            return api_unauthorized();
        }
        $allowed = $arguments ?? [];
        if (! empty($allowed) && ! in_array($user['role'] ?? '', $allowed, true)) {
            return api_forbidden('Your role does not have access to this resource.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
