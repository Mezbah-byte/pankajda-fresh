<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,

        // Custom filters
        'jwt'       => \App\Filters\JwtAuthFilter::class,
        'role'      => \App\Filters\RoleFilter::class,
        'apiCors'   => \App\Filters\ApiCorsFilter::class,
        'rateLimit' => \App\Filters\RateLimitFilter::class,
        'webAuth'   => \App\Filters\WebAuthFilter::class,
    ];

    public array $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
        ],
        'after' => [
            // 'honeypot',
            'secureheaders',
            'toolbar',
        ],
    ];

    public array $methods = [];

    public array $filters = [
        // API routes (JSON, JWT)
        'apiCors' => ['before' => ['api/*'], 'after' => ['api/*']],
        'jwt'     => ['before' => ['api/v1/*']],

        // Web admin routes
        'webAuth' => ['before' => ['admin/*']],
    ];
}
