<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * Spark command: php spark db:backup
 *
 * Dumps the active MySQL database to writable/backups/<datetime>.sql.gz
 *
 * Options:
 *   --no-gz      Write plain .sql instead of gzipped
 *   --keep N     Keep only last N backup files (default: 30)
 */
class DbBackup extends BaseCommand
{
    protected $group       = 'database';
    protected $name        = 'db:backup';
    protected $description = 'Dump the database to writable/backups/ as a gzipped SQL file.';

    protected $options = [
        '--no-gz'  => 'Write plain .sql instead of gzipped archive.',
        '--keep'   => 'Maximum number of backup files to retain (default: 30).',
    ];

    public function run(array $params): void
    {
        $cfg  = config(Database::class)->default;
        $host = $cfg['hostname'] ?? '127.0.0.1';
        $port = $cfg['port']     ?? 3306;
        $user = $cfg['username'] ?? 'root';
        $pass = $cfg['password'] ?? '';
        $db   = $cfg['database'] ?? '';

        if (! $db) {
            CLI::error('No database configured. Check .env or app/Config/Database.php.');
            return;
        }

        // Ensure mysqldump is on PATH
        exec('which mysqldump 2>/dev/null', $which);
        if (empty($which)) {
            CLI::error('mysqldump not found. Install MySQL client tools.');
            return;
        }

        $backupDir = WRITEPATH . 'backups';
        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $noGz     = array_key_exists('no-gz', $params);
        $ext      = $noGz ? '.sql' : '.sql.gz';
        $filename = date('Y-m-d_H-i-s') . '_' . $db . $ext;
        $filepath = $backupDir . '/' . $filename;

        // Build the mysqldump command
        $passArg  = $pass !== '' ? '-p' . escapeshellarg($pass) : '';
        $portArg  = "--port=" . (int) $port;
        $hostArg  = "-h " . escapeshellarg($host);
        $userArg  = "-u " . escapeshellarg($user);
        $dbArg    = escapeshellarg($db);

        if ($noGz) {
            $cmd = "mysqldump {$hostArg} {$portArg} {$userArg} {$passArg} {$dbArg} > " . escapeshellarg($filepath);
        } else {
            $cmd = "mysqldump {$hostArg} {$portArg} {$userArg} {$passArg} {$dbArg} | gzip > " . escapeshellarg($filepath);
        }

        CLI::write("Backing up [{$db}] → {$filename}", 'yellow');

        exec($cmd . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            CLI::error('mysqldump failed: ' . implode("\n", $output));
            if (is_file($filepath)) {
                unlink($filepath);
            }
            return;
        }

        $size = filesize($filepath);
        CLI::write('✓ Backup written: ' . $filepath, 'green');
        CLI::write('  Size: ' . $this->formatBytes($size));

        // Prune old backups
        $keep = (int) ($params['keep'] ?? 30);
        if ($keep > 0) {
            $this->pruneOldBackups($backupDir, $keep);
        }
    }

    private function pruneOldBackups(string $dir, int $keep): void
    {
        $files = glob($dir . '/*.sql*');
        if (! $files || count($files) <= $keep) {
            return;
        }
        usort($files, fn ($a, $b) => filemtime($a) <=> filemtime($b)); // oldest first
        $toDelete = array_slice($files, 0, count($files) - $keep);
        foreach ($toDelete as $f) {
            unlink($f);
            CLI::write('  Pruned old backup: ' . basename($f), 'dark_gray');
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
