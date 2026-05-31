<?php

namespace App\Services;

use Config\Database;

/**
 * BaseService - root for all business services.
 *
 * Services hold business logic, orchestrate repositories, and expose
 * a high-level API to controllers. Controllers should never know about
 * Models or DB connections directly.
 */
abstract class BaseService
{
    /**
     * Run a callable inside a database transaction. Rolls back on
     * exception. Returns whatever the callable returned.
     *
     * @template T
     * @param callable(): T $work
     * @return T
     */
    protected function transaction(callable $work)
    {
        $db = Database::connect();
        $db->transStart();

        try {
            $result = $work();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }

        $db->transComplete();
        if ($db->transStatus() === false) {
            throw new \RuntimeException('Transaction failed.');
        }
        return $result;
    }

    /**
     * Convenience: log an audit entry.
     */
    protected function audit(
        string $action,
        string $entityType,
        ?string $entityUnId = null,
        array $context = []
    ): void {
        // Session-based auth (web) or JWT auth (API) — whichever is set
        $userUnId = session('user_un_id')
            ?? (service('request')->auth_user['un_id'] ?? null);
        service('activityLogger')->log($action, $entityType, $entityUnId, $userUnId, $context);
    }
}
