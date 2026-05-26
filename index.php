<?php
declare(strict_types=1);
// When used as PHP built-in server router, serve existing files directly
if (PHP_SAPI === 'cli-server') {
    $__file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($__file) && $__file !== __FILE__) {
        return false;
    }
    unset($__file);
}

/********************************************
 * System:   EASY 2.0 Loginsystem
 * File:     index.php
 * PHP8-Umbau:
 *   - Security-HTTP-Header
 *   - Session vor require_once konfiguriert
 *   - Kein display_errors in Produktion
 *********************************************/

// ─── Sicherheits-HTTP-Header ────────────────────────────────────────────────
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; frame-src https://www.openstreetmap.org");
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// ─── Session starten (HTTPS-aware) ───────────────────────────────────────────
// cookie_secure wird dynamisch gesetzt: 1 bei HTTPS, 0 bei HTTP (z.B. lokale Entwicklung)
$_easy_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
            || (($_SERVER['SERVER_PORT'] ?? 80) == 443);
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure',   $_easy_https ? '1' : '0');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.gc_maxlifetime',  '1800');
session_name('EASY_2');
session_start();

// ─── Fehler-Ausgabe (Produktion: aus, Entwicklung: an) ───────────────────────
error_reporting(E_ALL);
ini_set('display_errors', '0');       // In Produktion NIEMALS auf 1!
ini_set('log_errors', '1');

// ─── Installer-Weiterleitung ──────────────────────────────────────────────────
if (!file_exists('./system/config.inc.php')) {
    if (is_dir('./install/') && file_exists('./install/index.php')) {
        header('Location: ./install/');
        exit();
    }
    http_response_code(503);
    exit('System nicht installiert!');
}

// ─── Dateien einbinden ───────────────────────────────────────────────────────
require_once './system/config.inc.php';

// ─── DB-Verbindung prüfen ────────────────────────────────────────────────────
if ($dberror) {
    http_response_code(503);
    echo '<!DOCTYPE html><html lang="de"><head><meta charset="utf-8">
    <title>Datenbankfehler</title>
    <style>body{font-family:sans-serif;display:flex;justify-content:center;
    align-items:center;height:100vh;margin:0;background:#f8f9fa;}
    .box{background:#fff;border:1px solid #dee2e6;border-radius:8px;
    padding:40px;max-width:500px;text-align:center;}
    h2{color:#dc3545;}p{color:#6c757d;}</style></head><body>
    <div class="box">
    <h2>&#9888; Datenbankfehler</h2>
    <p>Die Verbindung zur Datenbank konnte nicht hergestellt werden.</p>
    <p>Bitte überprüfe die Zugangsdaten in <code>system/config.inc.php</code>.</p>
    </div></body></html>';
    exit();
}

require_once './system/functions.inc.php';
require_once './system/functions.user.php';
require_once './system/classes.run.php';
require_once './system/classes.run.user.php';
require_once './system/run.inc.php';
require_once './system/run.user.php';
require_once './system/error_handling.php';

$sites->includeSite(true);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?php echo htmlspecialchars((string)$sites->getSiteName(), ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/modern-business.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/mlsystems.css" rel="stylesheet">
    <?php if (!empty($p) && $p === 'settings' || $p === 'news_add'): ?>
        <link href="css/summernote.min.css" rel="stylesheet">
    <?php endif; ?>
</head>
<body <?php if ($loginsystem->is_locked()) echo 'class="locked"'; ?>>

<?php if (!$loginsystem->is_locked()): ?>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar">
                    <span class="sr-only">Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="./"><?php echo htmlspecialchars((string)$loginsystem->getMainData('site_title'), ENT_QUOTES, 'UTF-8'); ?></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-navbar">
                <ul class="nav navbar-nav navbar-right">
                    <?php echo $menu->getMenu(); ?>
                </ul>
            </div>
        </div>
    </nav>

    <?php $siteFile = $sites->includeSite(); if ($siteFile) include $siteFile; ?>

    <div class="container">
        <hr>
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; <?php echo date('Y'); ?></p>
                </div>
            </div>
        </footer>
    </div>

<?php else: ?>
    <?php require_once './templates/login/locked.php'; ?>
<?php endif; ?>

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <?php if (!empty($p) && $p === 'settings' || $p === 'news_add'): ?>
        <script src="js/summernote/summernote.min.js"></script>
        <script src="js/summernote/lang/summernote-de-DE.min.js"></script>
        <script src="js/summernote-init.js"></script>
    <?php endif; ?>
    <script src="js/app.js"></script>
</body>
</html>
