<?php
declare(strict_types=1);
/********************************************
 * System:   EASY 2.0 Loginsystem
 * File:     system/config.example.inc.php
 *
 * Kopiere diese Datei nach system/config.inc.php
 * und trage deine eigenen Werte ein.
 *
 * Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 *********************************************/

// ─── Datenbank-Zugangsdaten ──────────────────────────────────────────────────
$db_config = [
    'host'     => '127.0.0.1',
    'user'     => '[DB_USER]',
    'passwd'   => '[DB_PASSWORD]',
    'database' => '[DB_NAME]',
    'prefix'   => '[DB_PREFIX]',
];

define('Prefix',       $db_config['prefix']);
define('EASY_VERSION', '1.0.0');

// ─── Verschlüsselung (AES-256-GCM) ──────────────────────────────────────────
// Ersetze diese Werte durch zufällige 64-stellige Hex-Werte:
// php -r "echo bin2hex(random_bytes(32));"
define('ENCRYPT_KEY',   '[REPLACE_WITH_RANDOM_64_HEX]');
define('COOKIE_SECRET', '[REPLACE_WITH_RANDOM_64_HEX]');

// ─── Session-Härtung ────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? 80) == 443);
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure',   $isHttps ? '1' : '0');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies','1');
    ini_set('session.gc_maxlifetime',  '1800');
}

// ─── PDO-Verbindung ──────────────────────────────────────────────────────────
$dberror = true;

try {
    $dsn = 'mysql:host=' . $db_config['host']
         . ';dbname='    . $db_config['database']
         . ';charset=utf8mb4';
    $pdo = new \PDO($dsn, $db_config['user'], $db_config['passwd'], [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
        \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
    ]);
    $dberror = false;
} catch (\PDOException $e) {
    error_log('DB-Verbindungsfehler: ' . $e->getMessage());
}
