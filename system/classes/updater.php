<?php
declare(strict_types=1);
/********************************************
 * System:   EASY 2.0 Loginsystem
 * Class:    updater
 * File:     updater.php
 *
 * PHP 8 modifications: Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 *********************************************/

class updater extends loginsystem
{
    private string $rootDir;
    private string $tmpDir;

    private const MAINTENANCE_FLAG = 'maintenance.flag';
    private const DOWNLOAD_FILE    = 'update_download.zip';
    private const EXTRACT_DIR      = 'update_extract/';
    private const BACKUP_PREFIX_MANUAL = 'manual_';
    private const BACKUP_PREFIX_UPDATE = 'pre-update_';
    private const DB_BACKUP_DIR    = 'updater_backup_dir';

    // Pfade die beim Backup ausgelassen werden
    private array $backupExclude = ['tmp/', 'avatare/'];

    // Pfade die beim Installieren nie überschrieben werden
    private array $installPreserve = [
        'system/config.inc.php',
        'system/config.user.php',
        'system/functions.user.php',
        'system/run.user.php',
        'system/classes.run.user.php',
    ];

    // Verzeichnisse die beim Installieren übersprungen werden
    private array $installExclude = ['tmp/', 'avatare/'];

    public function __construct()
    {
        parent::__construct();
        $this->rootDir = dirname(dirname(__DIR__)) . '/';
        $this->tmpDir  = $this->rootDir . 'tmp/';
        $this->ensurePageRegistered();
    }

    // ─── Backup-Verzeichnis (in DB gespeichert) ───────────────────────────────

    public function getBackupDir(): string
    {
        $val = $this->getMainData(self::DB_BACKUP_DIR);
        return (is_string($val) && $val !== '' && $val !== 'Einstellung nicht gefunden!')
            ? $val
            : $this->rootDir . 'tmp/backups/';
    }

    public function setBackupDir(string $path): void
    {
        $exists = $this->pq(
            "SELECT COUNT(*) AS cnt FROM `" . Prefix . "_main` WHERE `tag` = ?",
            [self::DB_BACKUP_DIR]
        );
        $row = $exists->fetch_assoc();
        if ((int)($row['cnt'] ?? 0) > 0) {
            $this->pq("UPDATE `" . Prefix . "_main` SET `value` = ? WHERE `tag` = ?", [$path, self::DB_BACKUP_DIR]);
        } else {
            $this->pq("INSERT INTO `" . Prefix . "_main` (`tag`, `value`) VALUES (?, ?)", [self::DB_BACKUP_DIR, $path]);
        }
    }

    // ─── Selbstregistrierung der Update-Seite in der DB ──────────────────────

    private function ensurePageRegistered(): void
    {
        $existing = $this->pq("SELECT `id` FROM `" . Prefix . "_sites` WHERE `filename` = 'update' LIMIT 1");
        if ($existing->num_rows === 0) {
            $this->pq(
                "INSERT INTO `" . Prefix . "_sites` (`title`, `filename`, `dir`, `start_site`, `start_site_login`, `errorsite`, `type`, `logout_site`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                ['Update', 'update', 'adm/', 0, 0, 0, 'php', 0]
            );
        }
    }

    // ─── Wartungsmodus ────────────────────────────────────────────────────────

    public function enableMaintenance(): void
    {
        if (!is_dir($this->tmpDir)) {
            mkdir($this->tmpDir, 0755, true);
        }
        file_put_contents($this->tmpDir . self::MAINTENANCE_FLAG, date('Y-m-d H:i:s'));
    }

    public function disableMaintenance(): void
    {
        $flag = $this->tmpDir . self::MAINTENANCE_FLAG;
        if (file_exists($flag)) {
            unlink($flag);
        }
    }

    public function isMaintenanceActive(): bool
    {
        return file_exists($this->tmpDir . self::MAINTENANCE_FLAG);
    }

    public function getMaintenanceStartTime(): string
    {
        $flag = $this->tmpDir . self::MAINTENANCE_FLAG;
        return file_exists($flag) ? (string)file_get_contents($flag) : '';
    }

    // ─── Backup ───────────────────────────────────────────────────────────────

    public function createBackup(?callable $emit = null, bool $preUpdate = false): array
    {
        $log    = [];
        $addLog = function(string $type, string $msg) use (&$log, $emit): void {
            $log[] = [$type, $msg];
            if ($emit !== null) ($emit)($type, $msg);
        };

        if (!class_exists('ZipArchive')) {
            $addLog('err', 'PHP ZipArchive-Extension ist nicht verfügbar.');
            return ['success' => false, 'error' => $log[0][1], 'log' => $log];
        }

        $backupDir = $this->getBackupDir();
        $addLog('ok', 'Backup-Verzeichnis: <code>' . htmlspecialchars($backupDir) . '</code>');

        if (!is_dir($backupDir) && !mkdir($backupDir, 0755, true)) {
            $addLog('err', 'Verzeichnis konnte nicht erstellt werden.');
            return ['success' => false, 'error' => $log[array_key_last($log)][1], 'log' => $log];
        }
        $addLog('ok', 'Verzeichnis bereit.');

        $htaccess = $backupDir . '.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "Order Deny,Allow\nDeny from all\n");
            $addLog('ok', '.htaccess-Schutz gesetzt.');
        }

        $prefix   = $preUpdate ? self::BACKUP_PREFIX_UPDATE : self::BACKUP_PREFIX_MANUAL;
        $filename = $prefix . EASY_VERSION . '_' . date('Ymd_His') . '.zip';
        $zipFile  = $backupDir . $filename;
        $addLog('ok', 'Archiv wird erstellt: <code>' . htmlspecialchars($filename) . '</code>');

        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $addLog('err', 'ZIP-Datei konnte nicht angelegt werden.');
            return ['success' => false, 'error' => $log[array_key_last($log)][1], 'log' => $log];
        }

        $fileCount = $this->addDirToZip($zip, $this->rootDir);
        $addLog('ok', $fileCount . ' Dateien gepackt.');

        $addLog('ok', 'Datenbank-Dump wird erstellt…');
        try {
            $sqlDump = $this->createDatabaseDump();
            $zip->addFromString('backup_database.sql', $sqlDump);
            $addLog('ok', 'Datenbank-Dump hinzugefügt (' . self::formatBytes(strlen($sqlDump)) . ').');
        } catch (\Throwable $e) {
            $addLog('warn', 'Datenbank-Dump fehlgeschlagen: ' . htmlspecialchars($e->getMessage()));
        }

        $zip->close();

        if (!file_exists($zipFile)) {
            $addLog('err', 'Backup-Datei wurde nicht erstellt.');
            return ['success' => false, 'error' => $log[array_key_last($log)][1], 'log' => $log];
        }

        $size = (int)filesize($zipFile);
        $addLog('ok', 'Fertig: <strong>' . htmlspecialchars($filename) . '</strong> (' . self::formatBytes($size) . ')');

        return ['success' => true, 'file' => $zipFile, 'filename' => $filename, 'size' => $size, 'log' => $log];
    }

    private function addDirToZip(ZipArchive $zip, string $baseDir): int
    {
        $count = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            $realPath     = (string)$file->getRealPath();
            $relativePath = substr($realPath, strlen($baseDir));

            foreach ($this->backupExclude as $excl) {
                if (str_starts_with($relativePath, $excl)) {
                    continue 2;
                }
            }

            $zip->addFile($realPath, $relativePath);
            $count++;
        }
        return $count;
    }

    // ─── Download ─────────────────────────────────────────────────────────────

    public function downloadRelease(string $url, ?callable $emit = null): array
    {
        $log    = [];
        $addLog = function(string $type, string $msg) use (&$log, $emit): void {
            $log[] = [$type, $msg];
            if ($emit !== null) ($emit)($type, $msg);
        };

        if (empty($url)) {
            $addLog('err', 'Keine Download-URL vorhanden.');
            return ['success' => false, 'error' => $log[0][1], 'log' => $log];
        }

        if (!str_starts_with($url, 'https://api.github.com/') && !str_starts_with($url, 'https://codeload.github.com/')) {
            $addLog('err', 'Ungültige Download-URL (nur GitHub erlaubt).');
            return ['success' => false, 'error' => $log[0][1], 'log' => $log];
        }
        $addLog('ok', 'Download-URL validiert.');

        $targetFile = $this->tmpDir . self::DOWNLOAD_FILE;
        if (file_exists($targetFile)) {
            unlink($targetFile);
        }

        $addLog('ok', 'Verbindung zu GitHub wird hergestellt…');

        $ctx = stream_context_create([
            'http' => [
                'header'          => "User-Agent: Easy2-PHP8-Updater/" . EASY_VERSION . "\r\n",
                'timeout'         => 120,
                'follow_location' => 1,
                'max_redirects'   => 10,
            ],
        ]);

        set_time_limit(180);
        $data = @file_get_contents($url, false, $ctx);

        if ($data === false || strlen($data) < 100) {
            $addLog('err', 'Download fehlgeschlagen oder Datei zu klein.');
            return ['success' => false, 'error' => $log[array_key_last($log)][1], 'log' => $log];
        }

        if (substr($data, 0, 2) !== "PK") {
            $addLog('err', 'Heruntergeladene Datei ist kein gültiges ZIP-Archiv.');
            return ['success' => false, 'error' => $log[array_key_last($log)][1], 'log' => $log];
        }

        $size = strlen($data);
        file_put_contents($targetFile, $data);
        unset($data);

        $addLog('ok', 'ZIP-Signatur geprüft.');
        $addLog('ok', 'Download abgeschlossen: <strong>' . self::formatBytes($size) . '</strong>');

        return ['success' => true, 'file' => $targetFile, 'size' => $size, 'log' => $log];
    }

    // ─── Installation ─────────────────────────────────────────────────────────

    public function installUpdate(string $zipFile, ?callable $emit = null): array
    {
        $log    = [];
        $addLog = function(string $type, string $msg) use (&$log, $emit): void {
            $log[] = [$type, $msg];
            if ($emit !== null) ($emit)($type, $msg);
        };

        if (!class_exists('ZipArchive')) {
            $log[] = ['err', 'PHP ZipArchive-Extension ist nicht verfügbar.'];
            return ['success' => false, 'error' => $log[0][1], 'log' => $log];
        }

        if (!file_exists($zipFile)) {
            $log[] = ['err', 'ZIP-Datei nicht gefunden.'];
            return ['success' => false, 'error' => $log[0][1], 'log' => $log];
        }
        $log[] = ['ok', 'Update-Archiv gefunden (' . self::formatBytes((int)filesize($zipFile)) . ').'];

        $zip = new ZipArchive();
        if ($zip->open($zipFile) !== true) {
            $addLog('err', 'ZIP-Datei konnte nicht geöffnet werden.');
            return ['success' => false, 'error' => $log[array_key_last($log)][1], 'log' => $log];
        }

        $extractDir = $this->tmpDir . self::EXTRACT_DIR;
        if (is_dir($extractDir)) {
            $this->delTree($extractDir);
        }
        if (!mkdir($extractDir, 0755, true)) {
            $zip->close();
            $addLog('err', 'Extraktions-Verzeichnis konnte nicht erstellt werden.');
            return ['success' => false, 'error' => $log[array_key_last($log)][1], 'log' => $log];
        }

        $zip->extractTo($extractDir);
        $zip->close();
        $addLog('ok', 'Archiv entpackt.');

        $topDirs   = glob($extractDir . '*', GLOB_ONLYDIR);
        $sourceDir = (count($topDirs) === 1) ? rtrim($topDirs[0], '/') . '/' : $extractDir;

        $addLog('ok', 'Dateien werden installiert…');

        $filesUpdated = 0;
        $filesSkipped = 0;
        $errors       = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            $relativePath = substr((string)$file->getRealPath(), strlen($sourceDir));

            if (in_array($relativePath, $this->installPreserve, true)) {
                $filesSkipped++;
                continue;
            }
            foreach ($this->installExclude as $excl) {
                if (str_starts_with($relativePath, $excl)) {
                    $filesSkipped++;
                    continue 2;
                }
            }

            $destFile = $this->rootDir . $relativePath;
            $destDir  = dirname($destFile);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            if (copy((string)$file->getRealPath(), $destFile)) {
                $filesUpdated++;
            } else {
                $errors[] = $relativePath;
            }
        }

        $this->delTree($extractDir);
        if (file_exists($zipFile)) {
            unlink($zipFile);
        }

        $addLog('ok', $filesUpdated . ' Dateien aktualisiert.');
        if ($filesSkipped > 0) {
            $addLog('warn', $filesSkipped . ' Dateien übersprungen (geschützt).');
        }
        if (!empty($errors)) {
            $addLog('err', count($errors) . ' Dateien konnten nicht kopiert werden: ' . implode(', ', array_slice($errors, 0, 3)));
            return ['success' => false, 'error' => $log[array_key_last($log)][1], 'files_updated' => $filesUpdated, 'log' => $log];
        }

        $addLog('ok', 'Installation abgeschlossen.');
        return ['success' => true, 'files_updated' => $filesUpdated, 'log' => $log];
    }

    // ─── Backup-Liste ─────────────────────────────────────────────────────────

    public function listBackups(): array
    {
        $backupDir = $this->getBackupDir();
        if (!is_dir($backupDir)) {
            return [];
        }

        $files = array_merge(
            glob($backupDir . self::BACKUP_PREFIX_MANUAL . '*.zip') ?: [],
            glob($backupDir . self::BACKUP_PREFIX_UPDATE . '*.zip') ?: []
        );
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'path'     => $file,
                'size'     => (int)filesize($file),
                'mtime'    => (int)filemtime($file),
            ];
        }

        usort($backups, static fn($a, $b) => $b['mtime'] - $a['mtime']);
        return $backups;
    }

    // ─── Backup löschen ──────────────────────────────────────────────────────

    public function deleteBackup(string $filename): bool
    {
        if (!preg_match('/^(manual|pre-update)_[a-zA-Z0-9._\-]+\.zip$/', $filename)) {
            return false;
        }
        $backupDir = realpath($this->getBackupDir());
        if ($backupDir === false) {
            return false;
        }
        $file = $backupDir . DIRECTORY_SEPARATOR . $filename;
        return file_exists($file) && unlink($file);
    }

    // ─── Backup herunterladen ─────────────────────────────────────────────────

    public function sendBackupDownload(string $filename): bool
    {
        if (!preg_match('/^(manual|pre-update)_[a-zA-Z0-9._\-]+\.zip$/', $filename)) {
            return false;
        }
        $backupDir = realpath($this->getBackupDir());
        if ($backupDir === false) {
            return false;
        }
        $file = $backupDir . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($file)) {
            return false;
        }
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: no-cache, must-revalidate');
        readfile($file);
        return true;
    }

    // ─── Datenbank-Dump ──────────────────────────────────────────────────────

    private function createDatabaseDump(): string
    {
        $sql  = "-- EASY 2.0 Database Backup\n";
        $sql .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n";

        $tables = $this->pq("SHOW TABLES");
        while ($row = $tables->fetch_assoc()) {
            $table = (string)reset($row);

            $create = $this->pq("SHOW CREATE TABLE `$table`");
            $cRow   = $create->fetch_assoc();
            $sql   .= "DROP TABLE IF EXISTS `$table`;\n";
            $sql   .= ($cRow['Create Table'] ?? '') . ";\n\n";

            $rows = $this->pq("SELECT * FROM `$table`");
            while ($dataRow = $rows->fetch_assoc()) {
                $vals = array_map(
                    static fn($v) => $v === null ? 'NULL' : "'" . addslashes((string)$v) . "'",
                    $dataRow
                );
                $sql .= "INSERT INTO `$table` VALUES (" . implode(', ', $vals) . ");\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $sql;
    }

    private function restoreDatabaseDump(string $sqlContent): array
    {
        $log      = [];
        $stmtCount = 0;
        $errors   = [];

        // Statements aufteilen (rudimentär, reicht für mysqldump-artige Dumps)
        $statements = array_filter(
            array_map('trim', preg_split('/;\s*\n/u', $sqlContent) ?: []),
            static fn($s) => $s !== '' && !str_starts_with($s, '--')
        );

        foreach ($statements as $stmt) {
            $result = $this->pq($stmt . ';');
            if ($result === false) {
                $errors[] = substr($stmt, 0, 60) . '…';
            } else {
                $stmtCount++;
            }
        }

        if (!empty($errors)) {
            $log[] = ['warn', count($errors) . ' SQL-Statements fehlgeschlagen: ' . htmlspecialchars(implode(', ', array_slice($errors, 0, 3)))];
        }
        $log[] = ['ok', $stmtCount . ' SQL-Statements ausgeführt.'];

        return ['success' => empty($errors), 'log' => $log];
    }

    // ─── Rollback aus Backup ─────────────────────────────────────────────────

    public function restoreBackup(string $filename, ?callable $emit = null): array
    {
        $log    = [];
        $addLog = function(string $type, string $msg) use (&$log, $emit): void {
            $log[] = [$type, $msg];
            if ($emit !== null) ($emit)($type, $msg);
        };

        if (!class_exists('ZipArchive')) {
            $addLog('err', 'PHP ZipArchive-Extension ist nicht verfügbar.');
            return ['success' => false, 'error' => $log[0][1], 'log' => $log];
        }

        if (!preg_match('/^(manual|pre-update)_[a-zA-Z0-9._\-]+\.zip$/', $filename)) {
            $addLog('err', 'Ungültiger Backup-Dateiname.');
            return ['success' => false, 'error' => $log[0][1], 'log' => $log];
        }

        $backupDir = realpath($this->getBackupDir());
        if ($backupDir === false) {
            $addLog('err', 'Backup-Verzeichnis nicht gefunden.');
            return ['success' => false, 'error' => $log[0][1], 'log' => $log];
        }
        $zipFile = $backupDir . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($zipFile)) {
            $addLog('err', 'Backup-Datei nicht gefunden: <code>' . htmlspecialchars($filename) . '</code>');
            return ['success' => false, 'error' => $log[0][1], 'log' => $log];
        }
        $addLog('ok', 'Backup gefunden: <code>' . htmlspecialchars($filename) . '</code> (' . self::formatBytes((int)filesize($zipFile)) . ')');

        $zip = new ZipArchive();
        if ($zip->open($zipFile) !== true) {
            $addLog('err', 'ZIP-Datei konnte nicht geöffnet werden.');
            return ['success' => false, 'error' => $log[array_key_last($log)][1], 'log' => $log];
        }

        $extractDir = $this->tmpDir . 'restore_extract/';
        if (is_dir($extractDir)) {
            $this->delTree($extractDir);
        }
        if (!mkdir($extractDir, 0755, true)) {
            $zip->close();
            $addLog('err', 'Extraktions-Verzeichnis konnte nicht erstellt werden.');
            return ['success' => false, 'error' => $log[array_key_last($log)][1], 'log' => $log];
        }

        $sqlContent = $zip->getFromName('backup_database.sql');
        $zip->extractTo($extractDir);
        $zip->close();
        $addLog('ok', 'Archiv entpackt.');

        $addLog('ok', 'Dateien werden wiederhergestellt…');
        $filesRestored = 0;
        $filesSkipped  = 0;
        $errors        = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($extractDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            $relativePath = substr((string)$file->getRealPath(), strlen($extractDir));

            if ($relativePath === 'backup_database.sql') {
                continue;
            }
            if (in_array($relativePath, $this->installPreserve, true)) {
                $filesSkipped++;
                continue;
            }

            $destFile = $this->rootDir . $relativePath;
            $destDir  = dirname($destFile);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            if (copy((string)$file->getRealPath(), $destFile)) {
                $filesRestored++;
            } else {
                $errors[] = $relativePath;
            }
        }

        $this->delTree($extractDir);

        $addLog('ok', $filesRestored . ' Dateien wiederhergestellt.');
        if ($filesSkipped > 0) {
            $addLog('warn', $filesSkipped . ' Dateien übersprungen (geschützt).');
        }
        if (!empty($errors)) {
            $addLog('err', count($errors) . ' Dateien konnten nicht kopiert werden: ' . implode(', ', array_slice($errors, 0, 3)));
        }

        // Datenbank wiederherstellen
        if ($sqlContent !== false && $sqlContent !== '') {
            $addLog('ok', 'Datenbank-Dump gefunden — wird wiederhergestellt…');
            $dbResult = $this->restoreDatabaseDump($sqlContent);
            foreach ($dbResult['log'] as [$t, $m]) {
                $addLog($t, $m);
            }
        } else {
            $addLog('warn', 'Kein Datenbank-Dump im Backup (älteres Backup ohne DB-Sicherung).');
        }

        $addLog('ok', 'Wiederherstellung abgeschlossen.');

        if (!empty($errors)) {
            return ['success' => false, 'error' => 'Datei-Fehler beim Einspielen.', 'files_restored' => $filesRestored, 'log' => $log];
        }

        return ['success' => true, 'files_restored' => $filesRestored, 'log' => $log];
    }

    // ─── GitHub-Version aus Session holen ────────────────────────────────────

    public function getAvailableVersion(): array
    {
        $branch   = defined('EASY_BRANCH') ? EASY_BRANCH : 'bs5';
        $cacheKey = 'easy2_gh_version_' . $branch;
        $version  = $_SESSION[$cacheKey] ?? '';
        $url      = $_SESSION[$cacheKey . '_url'] ?? '';
        return ['version' => $version, 'url' => $url];
    }

    // ─── Hilfsmethoden ───────────────────────────────────────────────────────

    private function delTree(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }
        $files = array_diff((array)scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            is_dir("$dir/$file") ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    public static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 0) . ' KB';
        }
        return $bytes . ' B';
    }
}
