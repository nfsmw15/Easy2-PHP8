<?php
declare(strict_types=1);
/********************************************
 * System:   EASY 2.0 Loginsystem
 * File:     install/index.php
 *
 * FIX v3:
 *  - Session-Cookie-Secure nur wenn HTTPS aktiv
 *  - $dberror-Logik repariert (keine Endlosschleife mehr)
 *  - mysqlCondition() nutzt jetzt $pdo aus config statt neu zu verbinden
 *  - Kein sleep(1) + Redirect-Loop mehr
 *********************************************/

// ── Session starten (HTTPS-aware) ─────────────────────────────────────────────
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? 80) == 443);

ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure',   $isHttps ? '1' : '0');  // HTTP-Installer fix!
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

session_name('EASY_2_INSTALL');
session_start();

// ── Sicherheits-Header ────────────────────────────────────────────────────────
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store, no-cache');
header('X-Robots-Tag: noindex, nofollow');

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// ── Datenbankverbindung versuchen ─────────────────────────────────────────────
$dberror   = true;
$pdo       = null;
$db_config = [
    'host'     => '[host]',
    'user'     => '[user]',
    'passwd'   => '[pass]',
    'database' => '[dbna]',
    'prefix'   => '[pref]',
];

// Wenn config.inc.php bereits existiert, einlesen
if (file_exists('../system/config.inc.php')) {
    // config.inc.php überschreibt $db_config, $dberror und $pdo
    // Aber: session.cookie_secure wurde oben schon korrekt gesetzt,
    // die ini_set-Aufrufe in config.inc.php kommen zu spät → ignorieren wir
    // Daher: nur die DB-Variablen übernehmen, nicht alles ausführen
    $configContent = file_get_contents('../system/config.inc.php');

    // DB-Zugangsdaten per Regex extrahieren, ohne die ganze config auszuführen
    // (verhindert dass session.cookie_secure nochmal gesetzt wird)
    if (preg_match("/'host'\s*=>\s*'([^']+)'/", $configContent, $m)) $db_config['host']     = $m[1];
    if (preg_match("/'user'\s*=>\s*'([^']+)'/", $configContent, $m)) $db_config['user']     = $m[1];
    if (preg_match("/'passwd'\s*=>\s*'([^']+)'/", $configContent, $m)) $db_config['passwd']  = $m[1];
    if (preg_match("/'database'\s*=>\s*'([^']+)'/", $configContent, $m)) $db_config['database'] = $m[1];
    if (preg_match("/'prefix'\s*=>\s*'([^']+)'/", $configContent, $m)) $db_config['prefix']  = $m[1];

    // Nur verbinden wenn keine Platzhalter mehr drin sind
    $placeholders = ['[host]', '[user]', '[pass]', '[dbna]', '[pref]'];
    $hasPlaceholders = false;
    foreach ($db_config as $key => $val) {
        if (in_array($val, $placeholders, true)) {
            $hasPlaceholders = true;
            break;
        }
    }

    if (!$hasPlaceholders) {
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

            // Prefix als Konstante definieren (wird von install.php gebraucht)
            if (!defined('Prefix')) {
                define('Prefix', $db_config['prefix']);
            }
        } catch (\PDOException $e) {
            error_log('Installer DB-Fehler: ' . $e->getMessage());
            $dberror = true;
        }
    }
} else {
    // Noch keine config → Prefix-Dummy damit install.php nicht crasht
    if (!defined('Prefix')) {
        define('Prefix', 'install_placeholder');
    }
}

require_once './setup/install.php';

$version = '1.0.0';
$error   = null;
$p       = length($_GET['p'] ?? '', 16);
$c       = length($_GET['c'] ?? '', 16);

// ── Aktionen ausführen ────────────────────────────────────────────────────────
$install = new install();
$tmp     = new template();

// conditions() auf BEIDEN Objekten ausführen
// $install->condition wird in step4.php für den Submit-Button gebraucht
$install->conditions(null, false);
// template ruft conditions() intern nochmal auf in includeFile() – das ist ok

if ($p === 'step2'   && $c === 'mysql')        $error = $install->mysqlConnection();
if ($p === 'step2.2' && $c === 'mysql_ignore') $error = $install->createMysqlTableIgnoreExists();
if ($p === 'step2.2' && $c === 'mysql_del')    $error = $install->createMysqlTableDeleteExists();
if ($p === 'step3'   && $c === 'mainsettings') $error = $install->saveMainsettings();
if ($p === 'step4'   && $c === 'create_user')  $error = $install->createUser();
if ($p === 'step5'   && $c === 'replace')      $error = $install->replace_impressum();

// ── Fehlermeldung formatieren ─────────────────────────────────────────────────
if (!empty($error)) {
    $error = '<div class="row"><div class="col-lg-12">
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Schließen"></button>
            &#9888; ' . $error . '
        </div></div></div>';
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>Installation &ndash; EASY 2.0</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/simple-sidebar.css" rel="stylesheet">
    <link href="css/loginsystem.css" rel="stylesheet">
</head>
<body>
<div id="wrapper">
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li class="sidebar-brand"><a>EASY 2.0 v.<?php echo htmlspecialchars($version, ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li><a target="_blank" href="https://github.com/nfsmw15/Easy2-PHP8">GitHub</a></li>
        </ul>
        <span style="position:absolute;bottom:0;padding:10px;color:#999">
            Copyright &copy; <?php echo date('Y'); ?> Andreas P.
        </span>
    </div>
    <?php include $tmp->includeFile(); ?>
</div>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
