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
 * System:   EASY 2.0 Loginsystem – Dashboard
 * Branch:   loginsystem-php8
 * File:     index.php
 * Basis:    EASY_2.0_Loginsystem_0.9.8.6 (Dashboard) – PHP8-Port
 *
 * Änderungen gegenüber Original 0.9.8.6:
 *   - vendor/ entfernt → lokale Assets (css/, js/)
 *   - Security-HTTP-Header ergänzt
 *   - Session sicher konfiguriert (httponly, SameSite, strict mode)
 *   - display_errors deaktiviert (Produktionseinstellung)
 *   - CSRF-Token in Logout-Link korrigiert (htmlspecialchars)
 *   - PHP 8.x type-safe Ausgaben
 *
 * PHP 8 modifications: Copyright (C) 2026 nfsmw15 <https://nfsmw15.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 *********************************************/

// ─── Sicherheits-HTTP-Header ─────────────────────────────────────────────────
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'");
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// ─── Session sicher konfigurieren ────────────────────────────────────────────
$_easy_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? 80) == 443);
ini_set('session.cookie_httponly',  '1');
ini_set('session.cookie_secure',    $_easy_https ? '1' : '0');
ini_set('session.cookie_samesite',  'Strict');
ini_set('session.use_strict_mode',  '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.gc_maxlifetime',   '1800');
session_name('EASY_2');
session_start();

// ─── Fehler-Ausgabe ──────────────────────────────────────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', '0');  // In Produktion NIEMALS auf 1!
ini_set('log_errors', '1');

// ─── Installer-Weiterleitung ─────────────────────────────────────────────────
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

$_loggedIn = $loginsystem->login_session();
$_locked   = $loginsystem->is_locked();
$_csrfToken = htmlspecialchars((string)$loginsystem->getData('csrfToken'), ENT_QUOTES, 'UTF-8');
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

    <!-- Bootstrap (lokal) -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (lokal) -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- SB Admin (lokal, aus Dashboard-Original) -->
    <link href="css/sb-admin.css" rel="stylesheet">
    <!-- ML Systems Custom CSS -->
    <link href="css/mlsystems.css" rel="stylesheet">
    <?php if ($loginsystem->login_session() === true): ?>
    <!-- DataTables CSS (lokal) -->
    <link href="css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <?php endif; ?>
</head>
<body class="<?php if (!$_locked && $_loggedIn) echo 'fixed-nav sticky-footer'; ?> bg-dark" id="page-top">

<?php if (!$_locked): ?>
    <?php if ($_loggedIn): ?>
    <!-- Navigation (SB-Admin Sidebar-Stil) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
        <a class="navbar-brand" href="./">
            <?php echo htmlspecialchars((string)$loginsystem->getMainData('short_site_title'), ENT_QUOTES, 'UTF-8'); ?>
        </a>
        <button class="navbar-toggler navbar-toggler-right" type="button"
                data-toggle="collapse" data-target="#navbarResponsive"
                aria-controls="navbarResponsive" aria-expanded="false"
                aria-label="Navigation umschalten">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav navbar-sidenav" id="mainAccordion">
                <?php echo $menu->getMenu(); ?>
            </ul>
            <ul class="navbar-nav sidenav-toggler">
                <li class="nav-item">
                    <a class="nav-link text-center" id="sidenavToggler">
                        <i class="fa fa-fw fa-angle-left"></i>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" data-toggle="modal" data-target="#logoutModal">
                        <i class="fa fa-fw fa-sign-out"></i> Abmelden
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <?php endif; ?>

    <?php
    $siteFile = $sites->includeSite();
    if ($siteFile) {
        include $siteFile;
    }
    ?>

    <?php if ($_loggedIn): ?>
    <footer class="sticky-footer">
        <div class="container">
            <div class="text-center">
                <small>Copyright &copy; <?php echo date('Y'); ?></small>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fa fa-angle-up"></i>
    </a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog"
         aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Abmelden?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Schließen">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">M&ouml;chtest du dich wirklich ausloggen?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Abbrechen</button>
                    <a class="btn btn-primary"
                       href="?c=logout&amp;csrf=<?php echo $_csrfToken; ?>">Abmelden</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

<?php else: /* Benutzer gesperrt */ ?>
    <?php require_once './templates/login/locked.php'; ?>
<?php endif; ?>

<!-- jQuery (lokal) -->
<script src="js/jquery.js"></script>
<!-- Bootstrap (lokal) -->
<script src="js/bootstrap.min.js"></script>

<?php if ($_loggedIn): ?>
<?php if (empty($p) || $p === 'home' || $p === 'charts'): ?>
<!-- Chart.js (lokal) -->
<script src="js/chart.min.js"></script>
<!-- DataTables (lokal) -->
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap4.min.js"></script>
<?php elseif ($p === 'tables'): ?>
<!-- DataTables (lokal) -->
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap4.min.js"></script>
<?php endif; ?>
<?php endif; ?>

<?php if (!empty($p) && ($p === 'settings' || $p === 'news_add')): ?>
<!-- Summernote (lokal, für WYSIWYG-Editor) -->
<link href="css/summernote-bs4.min.css" rel="stylesheet">
<script src="js/summernote/summernote-bs4.min.js"></script>
<script src="js/summernote/lang/summernote-de-DE.min.js"></script>
<script src="js/summernote-init.js"></script>
<?php endif; ?>

<!-- SB Admin Custom JS (lokal) -->
<script src="js/sb-admin.min.js"></script>

<?php if ($_loggedIn && (empty($p) || $p === 'home' || $p === 'charts')): ?>
<script src="js/sb-admin-datatables.min.js"></script>
<script src="js/sb-admin-charts.min.js"></script>
<?php elseif ($_loggedIn && $p === 'tables'): ?>
<script src="js/sb-admin-datatables.min.js"></script>
<?php endif; ?>

</body>
</html>
