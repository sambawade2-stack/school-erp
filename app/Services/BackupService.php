<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class BackupService
{
    private const MAX_BACKUPS = 10;
    private const BACKUP_DIR  = 'backups';

    private string $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/' . self::BACKUP_DIR);
    }

    /* ── Sauvegarde ───────────────────────────────────────────────────────── */

    public function backup(bool $logAction = true): array
    {
        try {
            $this->ensureBackupDirectory();

            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename  = "backup_{$timestamp}.sql";
            $filePath  = $this->backupPath . '/' . $filename;

            $this->runMysqldump($filePath);

            if (!File::exists($filePath) || File::size($filePath) === 0) {
                throw new Exception('mysqldump a produit un fichier vide ou inexistant.');
            }

            $this->cleanOldBackups();

            if ($logAction) {
                $this->logAction('backup_created', $filename, $this->getFileSize($filePath));
            }

            return [
                'success'   => true,
                'message'   => "✅ Sauvegarde MySQL créée : $filename",
                'file'      => $filename,
                'size_mb'   => $this->getFileSize($filePath),
                'timestamp' => now(),
            ];
        } catch (Exception $e) {
            Log::error('Erreur sauvegarde MySQL', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return ['success' => false, 'message' => '❌ ' . $e->getMessage()];
        }
    }

    /* ── Restauration ─────────────────────────────────────────────────────── */

    public function restore(string $filename): array
    {
        try {
            if (!$this->isValidFilename($filename)) {
                throw new Exception('Nom de fichier invalide.');
            }

            $filePath = $this->backupPath . '/' . $filename;

            if (!File::exists($filePath)) {
                throw new Exception('Fichier de sauvegarde introuvable.');
            }

            // Sauvegarde de sécurité avant restauration
            $security = $this->backup(logAction: false);

            $this->runMysqlRestore($filePath);

            $this->logAction('backup_restored', $filename, null, $security['file'] ?? null);

            return [
                'success'        => true,
                'message'        => "✅ Base restaurée depuis : $filename",
                'security_backup'=> $security['file'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Erreur restauration MySQL', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => '❌ ' . $e->getMessage()];
        }
    }

    /* ── Liste ────────────────────────────────────────────────────────────── */

    public function list(): array
    {
        if (!File::isDirectory($this->backupPath)) {
            return [];
        }

        $backups = [];
        foreach (File::files($this->backupPath) as $file) {
            $name = $file->getFilename();
            if (!preg_match('/^backup_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql$/', $name)) {
                continue;
            }
            $mtime     = File::lastModified($file->getPathname());
            $backups[] = [
                'name'           => $name,
                'size_mb'        => $this->getFileSize($file->getPathname()),
                'date'           => $mtime,
                'date_formatted' => Carbon::createFromTimestamp($mtime)->format('d/m/Y H:i:s'),
                'date_relative'  => Carbon::createFromTimestamp($mtime)->diffForHumans(),
            ];
        }

        usort($backups, fn($a, $b) => $b['date'] <=> $a['date']);
        return $backups;
    }

    /* ── Suppression ──────────────────────────────────────────────────────── */

    public function delete(string $filename): array
    {
        if (!$this->isValidFilename($filename)) {
            return ['success' => false, 'message' => 'Nom de fichier invalide.'];
        }

        $path = $this->backupPath . '/' . $filename;
        if (!File::exists($path)) {
            return ['success' => false, 'message' => 'Fichier introuvable.'];
        }

        File::delete($path);
        $this->logAction('backup_deleted', $filename);
        return ['success' => true, 'message' => '✅ Sauvegarde supprimée.'];
    }

    /* ── Téléchargement ───────────────────────────────────────────────────── */

    public function download(string $filename)
    {
        if (!$this->isValidFilename($filename)) {
            return null;
        }

        $path = $this->backupPath . '/' . $filename;
        if (!File::exists($path)) {
            return null;
        }

        $this->logAction('backup_downloaded', $filename);

        return response()->download($path, $filename, ['Content-Type' => 'application/sql']);
    }

    /* ── Stats ────────────────────────────────────────────────────────────── */

    public function getStats(): array
    {
        $backups   = $this->list();
        $totalSize = array_sum(array_column($backups, 'size_mb'));

        return [
            'total_count'   => count($backups),
            'total_size_mb' => round($totalSize, 2),
            'latest_backup' => $backups[0] ?? null,
            'max_backups'   => self::MAX_BACKUPS,
        ];
    }

    /* ── Privé ────────────────────────────────────────────────────────────── */

    private function runMysqldump(string $outputFile): void
    {
        $host     = config('database.connections.mysql.host', '127.0.0.1');
        $port     = config('database.connections.mysql.port', 3306);
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $passwordArg = $password ? '-p' . escapeshellarg($password) : '';

        $cmd = sprintf(
            'mysqldump -h %s -P %s -u %s %s --single-transaction --routines --triggers %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg((string) $port),
            escapeshellarg($username),
            $passwordArg,
            escapeshellarg($database),
            escapeshellarg($outputFile)
        );

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new Exception('mysqldump a échoué (code ' . $returnCode . '). Vérifiez que mysqldump est installé.');
        }
    }

    private function runMysqlRestore(string $inputFile): void
    {
        $host     = config('database.connections.mysql.host', '127.0.0.1');
        $port     = config('database.connections.mysql.port', 3306);
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $passwordArg = $password ? '-p' . escapeshellarg($password) : '';

        $cmd = sprintf(
            'mysql -h %s -P %s -u %s %s %s < %s 2>&1',
            escapeshellarg($host),
            escapeshellarg((string) $port),
            escapeshellarg($username),
            $passwordArg,
            escapeshellarg($database),
            escapeshellarg($inputFile)
        );

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new Exception('Restauration MySQL échouée (code ' . $returnCode . ').');
        }
    }

    private function ensureBackupDirectory(): void
    {
        if (!File::isDirectory($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }

        if (!File::isWritable($this->backupPath)) {
            throw new Exception('Dossier de sauvegarde non accessible en écriture.');
        }
    }

    private function cleanOldBackups(): void
    {
        $backups = $this->list();
        if (count($backups) > self::MAX_BACKUPS) {
            foreach (array_slice($backups, self::MAX_BACKUPS) as $old) {
                File::delete($this->backupPath . '/' . $old['name']);
            }
        }
    }

    private function isValidFilename(string $filename): bool
    {
        return (bool) preg_match('/^backup_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql$/', $filename)
            && !str_contains($filename, '..')
            && !str_contains($filename, '/');
    }

    private function getFileSize(string $path): float
    {
        return round(File::size($path) / 1024 / 1024, 2);
    }

    private function logAction(string $action, string $filename, ?float $sizeMb = null, ?string $relatedFile = null): void
    {
        Log::info('Backup', [
            'action'       => $action,
            'file'         => $filename,
            'size_mb'      => $sizeMb,
            'related_file' => $relatedFile,
            'user_id'      => Auth::id(),
            'ip'           => request()->ip(),
        ]);
    }
}
