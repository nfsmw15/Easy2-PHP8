<?php
declare(strict_types=1);
/********************************************
 *
 * System:   EASY 2.0 Loginsystem
 * File:     config.inc.php
 * PHP8-Umbau:
 *   - mysqli → PDO mit Exceptions
 *   - Session-Härtung (HttpOnly, Secure, SameSite)
 *   - Verschlüsselungsschlüssel über Konstante
 *   - Keine @-Fehlersuppression
 *
 *********************************************/

// ─── Datenbank-Zugangsdaten ──────────────────────────────────────────────────
$db_config = [
    'host'     => '[host]',
    'user'     => '[user]',
    'passwd'   => '[pass]',
    'database' => '[dbna]',
    'prefix'   => '[pref]',
];

define('Prefix',       $db_config['prefix']);
define('EASY_VERSION', '1.1.0');
define('BS_VERSION',   3);

// ─── Verschlüsselung (AES-256-GCM) ──────────────────────────────────────────
// WICHTIG: Diesen Schlüssel nach der Installation durch einen zufälligen
// 64-stelligen Hex-Wert ersetzen! Z.B.: php -r "echo bin2hex(random_bytes(32));"
// NIEMALS den Standardwert produktiv verwenden!
define('ENCRYPT_KEY',    '[generated_after_install_32bytes_hex]');
define('COOKIE_SECRET',  '[generated_after_install_32bytes_hex_2]');

// ─── Session-Härtung vor session_start() ────────────────────────────────────
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
        \PDO::ATTR_EMULATE_PREPARES   => false, // Echte Prepared Statements erzwingen
        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
    ]);
    $dberror = false;
} catch (\PDOException $e) {
    // Fehlermeldung NIEMALS direkt ausgeben (kein Leak von DB-Infos)
    error_log('DB-Verbindungsfehler: ' . $e->getMessage());
    $dberror = true;
}
