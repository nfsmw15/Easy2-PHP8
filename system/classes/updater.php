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
    private string $configFile;
    private array  $config;

    private const GITHUB_REPO    = 'nfsmw15/Easy2-PHP8';
    private const MAINTENANCE_FLAG = 'maintenance.flag';
    private const DOWNLOAD_FILE    = 'update_download.zip';
    private const EXTRACT_DIR      = 'update_extract/';
    private const BACKUP_PREFIX    = 'backup_';

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
        $this->rootDir    = dirname(dirname(__DIR__)) . '/';
        $this->tmpDir     = $this->rootDir . 'tmp/';
        $this->configFile = $this->tmpDir . '.updater.json';
        $this->loadConfig();
        $this->ensurePageRegistered();
    }

    // ─── Konfiguration ────────────────────────────────────────────────────────

    private function loadConfig(): void
    {
        $defaults = ['backup_dir' => $this->rootDir . 'tmp/backups/'];
        if (file_exists($this->configFile)) {
            $json = json_decode((string)file_get_contents($this->configFile), true);
            $this->config = is_array($json) ? array_merge($defaults, $json) : $defaults;
        } else {
            $this->config = $defaults;
        }
    }

    public function saveConfig(array $data): void
    {
        $this->config = array_merge($this->config, $data);
        file_put_contents($this->configFile, json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    public function getConfig(?string $key = null): mixed
    {
        return $key === null ? $this->config : ($this->config[$key] ?? null);
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

    public function createBackup(): array
    {
        if (!class_exists('ZipArchive')) {
            return ['success' => false, 'error' => 'PHP ZipArchive-Extension ist nicht verfügbar.'];
        }

        $backupDir = (string)$this->getConfig('backup_dir');
        if (!is_dir($backupDir) && !mkdir($backupDir, 0755, true)) {
            return ['success' => false, 'error' => 'Backup-Verzeichnis konnte nicht erstellt werden: ' . htmlspecialchars($backupDir)];
        }

        // .htaccess schreiben falls Backup im Web-Root liegt
        $htaccess = $backupDir . '.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "Order Deny,Allow\nDeny from all\n");
        }

        $filename = self::BACKUP_PREFIX . EASY_VERSION . '_' . date('Ymd_His') . '.zip';
        $zipFile  = $backupDir . $filename;

        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return ['success' => false, 'error' => 'ZIP-Datei konnte nicht erstellt werden.'];
        }

        $this->addDirToZip($zip, $this->rootDir);
        $zip->close();

        if (!file_exists($zipFile)) {
            return ['success' => false, 'error' => 'Backup-Datei wurde nicht erstellt.'];
        }

        return [
            'success'  => true,
            'file'     => $zipFile,
            'filename' => $filename,
            'size'     => filesize($zipFile),
        ];
    }

    private function addDirToZip(ZipArchive $zip, string $baseDir): void
    {
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
        }
    }

    // ─── Download ─────────────────────────────────────────────────────────────

    public function downloadRelease(string $url): array
    {
        if (empty($url)) {
            return ['success' => false, 'error' => 'Keine Download-URL vorhanden.'];
        }

        // Nur GitHub-URLs erlauben
        if (!str_starts_with($url, 'https://api.github.com/') && !str_starts_with($url, 'https://codeload.github.com/')) {
            return ['success' => false, 'error' => 'Ungültige Download-URL.'];
        }

        $targetFile = $this->tmpDir . self::DOWNLOAD_FILE;

        // Alten Download-Versuch entfernen
        if (file_exists($targetFile)) {
            unlink($targetFile);
        }

        $ctx = stream_context_create([
            'http' => [
                'header'           => "User-Agent: Easy2-PHP8-Updater/" . EASY_VERSION . "\r\n",
                'timeout'          => 120,
                'follow_location'  => 1,
                'max_redirects'    => 10,
            ],
        ]);

        set_time_limit(180);
        $data = @file_get_contents($url, false, $ctx);

        if ($data === false || strlen($data) < 100) {
            return ['success' => false, 'error' => 'Download fehlgeschlagen oder Datei zu klein.'];
        }

        // Prüfen ob es wirklich ein ZIP ist (Magic Bytes PK)
        if (substr($data, 0, 2) !== "PK") {
            return ['success' => false, 'error' => 'Heruntergeladene Datei ist kein gültiges ZIP-Archiv.'];
        }

        file_put_contents($targetFile, $data);

        return [
            'success'  => true,
            'file'     => $targetFile,
            'size'     => strlen($data),
        ];
    }

    // ─── Installation ─────────────────────────────────────────────────────────

    public function installUpdate(string $zipFile): array
    {
        if (!class_exists('ZipArchive')) {
            return ['success' => false, 'error' => 'PHP ZipArchive-Extension ist nicht verfügbar.'];
        }

        if (!file_exists($zipFile)) {
            return ['success' => false, 'error' => 'ZIP-Datei nicht gefunden: ' . htmlspecialchars($zipFile)];
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFile) !== true) {
            return ['success' => false, 'error' => 'ZIP-Datei konnte nicht geöffnet werden.'];
        }

        $extractDir = $this->tmpDir . self::EXTRACT_DIR;

        if (is_dir($extractDir)) {
            $this->delTree($extractDir);
        }

        if (!mkdir($extractDir, 0755, true)) {
            $zip->close();
            return ['success' => false, 'error' => 'Extraktions-Verzeichnis konnte nicht erstellt werden.'];
        }

        $zip->extractTo($extractDir);
        $zip->close();

        // GitHub-Zipballs haben ein Top-Level-Verzeichnis (z.B. "nfsmw15-Easy2-PHP8-abc1234/")
        $topDirs = glob($extractDir . '*', GLOB_ONLYDIR);
        $sourceDir = (count($topDirs) === 1) ? rtrim($topDirs[0], '/') . '/' : $extractDir;

        $filesUpdated = 0;
        $errors       = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            $relativePath = substr((string)$file->getRealPath(), strlen($sourceDir));

            // Geschützte Dateien niemals überschreiben
            if (in_array($relativePath, $this->installPreserve, true)) {
                continue;
            }

            // Ausgeschlossene Verzeichnisse überspringen
            foreach ($this->installExclude as $excl) {
                if (str_starts_with($relativePath, $excl)) {
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

        // Aufräumen
        $this->delTree($extractDir);
        if (file_exists($zipFile)) {
            unlink($zipFile);
        }

        if (!empty($errors)) {
            return [
                'success'       => false,
                'error'         => 'Einige Dateien konnten nicht kopiert werden: ' . implode(', ', array_slice($errors, 0, 5)),
                'files_updated' => $filesUpdated,
            ];
        }

        return ['success' => true, 'files_updated' => $filesUpdated];
    }

    // ─── Backup-Liste ─────────────────────────────────────────────────────────

    public function listBackups(): array
    {
        $backupDir = (string)$this->getConfig('backup_dir');
        if (!is_dir($backupDir)) {
            return [];
        }

        $files = glob($backupDir . self::BACKUP_PREFIX . '*.zip') ?: [];
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
