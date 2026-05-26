<?php

namespace App\Traits;

/**
 * UnIdTrait - auto-assigns a public un_id on insert.
 *
 * Mix into a CI4 Model to attach a beforeInsert callback that fills
 * the un_id field if missing. Subclasses can override $unIdPrefix.
 */
trait UnIdTrait
{
    /**
     * Optional 2-3 letter prefix prepended to generated UUIDs.
     */
    protected string $unIdPrefix = '';

    /**
     * Hook for CI4 Model beforeInsert callback.
     */
    protected function attachUnId(array $data): array
    {
        if (! isset($data['data']['un_id']) || empty($data['data']['un_id'])) {
            $data['data']['un_id'] = generate_un_id($this->unIdPrefix ?: null);
        }
        return $data;
    }
}
