<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * RateLimitFilter - simple throttling using CI4's built-in throttler.
 *
 * Defaults: 30 requests per minute per IP+route. Used on auth endpoints
 * to slow down brute force attacks.
 */
class RateLimitFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $throttler = service('throttler');

        $limit  = (int) ($arguments[0] ?? 30);
        $window = (int) ($arguments[1] ?? MINUTE);

        $bucket = 'rate:' . $request->getIPAddress() . ':' . trim($request->getUri()->getPath(), '/');

        if (! $throttler->check($bucket, $limit, $window, 1)) {
            return api_error('Too many requests. Please try again later.', 429, [
                'retry_after_seconds' => $throttler->getTokenTime(),
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
