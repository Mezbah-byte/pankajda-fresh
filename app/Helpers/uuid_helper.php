<?php

/**
 * UUID Helper functions.
 *
 * Public unique IDs used across all tables for relationships.
 * Backed by ramsey/uuid (UUID v4 by default).
 */

if (! function_exists('generate_un_id')) {
    /**
     * Generate a public unique identifier (UUID v4).
     *
     * @param string|null $prefix Optional 2-3 letter prefix for human readability
     */
    function generate_un_id(?string $prefix = null): string
    {
        $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        return $prefix ? strtoupper($prefix) . '-' . $uuid : $uuid;
    }
}

if (! function_exists('is_un_id')) {
    /**
     * Validate that a string is a usable un_id.
     */
    function is_un_id(?string $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }
        // Strip optional prefix
        if (str_contains($value, '-') && substr_count($value, '-') === 5) {
            $value = substr($value, strpos($value, '-') + 1);
        }
        return \Ramsey\Uuid\Uuid::isValid($value);
    }
}

if (! function_exists('short_un_id')) {
    /**
     * Take an un_id and return a short truncated form for display.
     */
    function short_un_id(string $unId, int $length = 8): string
    {
        $clean = str_replace('-', '', $unId);
        return strtoupper(substr($clean, 0, $length));
    }
}
