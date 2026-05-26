<?php

namespace App\Libraries;

use Ramsey\Uuid\Uuid;

/**
 * UUID generator service.
 *
 * Wraps ramsey/uuid behind a thin service so callers can be unit-tested
 * with a mock. Use via `service('uuid')->generate(...)`.
 */
class UuidGenerator
{
    /**
     * Generate a public unique identifier.
     *
     * @param string|null $prefix Optional 2-3 letter prefix for readability.
     */
    public function generate(?string $prefix = null): string
    {
        $uuid = Uuid::uuid4()->toString();
        return $prefix ? strtoupper($prefix) . '-' . $uuid : $uuid;
    }

    public function isValid(string $value): bool
    {
        if (str_contains($value, '-') && substr_count($value, '-') === 5) {
            $value = substr($value, strpos($value, '-') + 1);
        }
        return Uuid::isValid($value);
    }
}
