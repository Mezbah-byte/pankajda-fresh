<?php

namespace App\Libraries;

use Config\Database;

/**
 * Activity logger - records audit trail entries.
 *
 * All write operations on business data should call ->log()
 * to leave an audit record (who did what, when, on which entity).
 */
class ActivityLogger
{
    public function log(
        string $action,
        string $entityType,
        ?string $entityUnId = null,
        ?string $userUnId = null,
        array $context = []
    ): void {
        $db = Database::connect();

        $request = service('request');

        $db->table('activity_logs')->insert([
            'un_id'        => generate_un_id('LOG'),
            'user_un_id'   => $userUnId,
            'action'       => $action,
            'entity_type'  => $entityType,
            'entity_un_id' => $entityUnId,
            'context'      => json_encode($context, JSON_UNESCAPED_UNICODE),
            'ip_address'   => $request->getIPAddress() ?? null,
            'user_agent'   => substr($request->getUserAgent()->getAgentString() ?? '', 0, 250),
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }
}
