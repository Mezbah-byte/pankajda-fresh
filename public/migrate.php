<?php
/**
 * Web migration runner for cPanel / shared hosting.
 * Hit: https://yourdomain.com/migrate.php?key=YOUR_KEY
 * DELETE this file after use.
 */

const MIGRATE_KEY = 'pankajda-migrate-2026-secret';

if (($_GET['key'] ?? '') !== MIGRATE_KEY) {
    http_response_code(403);
    die('403 Forbidden');
}

$root    = realpath(__DIR__ . '/../');
$phpBin  = PHP_BINARY ?: 'php';
$spark   = $root . '/spark';

$cmd     = escapeshellcmd($phpBin) . ' ' . escapeshellarg($spark) . ' migrate --all 2>&1';
$output  = [];
$code    = 0;
exec('cd ' . escapeshellarg($root) . ' && ' . $phpBin . ' ' . escapeshellarg($spark) . ' migrate --all 2>&1', $output, $code);

header('Content-Type: text/plain; charset=utf-8');
echo implode("\n", $output);
echo "\n\nExit code: $code\n";
echo "\n>>> DELETE public/migrate.php after use! <<<\n";
