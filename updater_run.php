<?php
declare(strict_types=1);
/**
 * EASY 2.0 — Live-Updater Runner
 * Gibt jeden Schritt sofort ans Browser aus (Streaming).
 * Wird von templates/adm/update.php aufgerufen (POST).
 */

// ─── Session & Bootstrap ──────────────────────────────────────────────────────
$_easy_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
            || (($_SERVER['SERVER_PORT'] ?? 80) == 443);
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure',   $_easy_https ? '1' : '0');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_name('EASY_2');
session_start();

if (!file_exists('./system/config.inc.php')) {
    http_response_code(403); exit('Nicht installiert.');
}

require_once './system/config.inc.php';
require_once './system/functions.inc.php';
require_once './system/functions.user.php';
require_once './system/classes.run.php';

// ─── Sicherheits-Checks ───────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?p=update'); exit();
}

$__csrf  = $_POST['csrf'] ?? '';
$__token = $_SESSION['ml_csrfToken'] ?? '';
if (empty($__csrf) || empty($__token) || !hash_equals($__token, $__csrf)) {
    http_response_code(403); exit('Ungültiger CSRF-Token.');
}
if (!$loginsystem->auditRight('mainsave')) {
    http_response_code(403); exit('Keine Berechtigung.');
}

$updater = new updater();
$action  = $_POST['action'] ?? '';

// ─── Streaming-Helfer ─────────────────────────────────────────────────────────
function streamFlush(): void
{
    if (ob_get_level() > 0) ob_flush();
    flush();
}

function streamLog(string $type, string $msg): void
{
    echo '<script>addLog(' . json_encode($type) . ',' . json_encode($msg) . ');</script>' . "\n";
    streamFlush();
}

// ─── Output-Puffer deaktivieren ───────────────────────────────────────────────
header('X-Accel-Buffering: no');
header('Cache-Control: no-cache, no-store');
header('Content-Type: text/html; charset=utf-8');
while (ob_get_level() > 0) ob_end_flush();

$_siteName = htmlspecialchars((string)$loginsystem->getMainData('site_title'));
$_bsTheme  = ($_COOKIE['easy2_theme'] ?? 'light') === 'dark' ? 'dark' : 'light';

$_titles = [
    'backup'      => 'Backup wird erstellt…',
    'backup_only' => 'Backup wird erstellt…',
    'download'    => 'Update wird heruntergeladen…',
    'install'     => 'Update wird installiert…',
    'restore'     => 'Backup wird eingespielt…',
];
$_pageTitle = $_titles[$action] ?? 'Vorgang läuft…';
?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="<?php echo $_bsTheme; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $_pageTitle; ?> — <?php echo $_siteName; ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        #log-box {
            background: #0d1117;
            color: #c9d1d9;
            font-family: ui-monospace, 'SFMono-Regular', Consolas, monospace;
            font-size: .85rem;
            border-radius: .5rem;
            padding: 1rem 1.25rem;
            min-height: 200px;
            max-height: 55vh;
            overflow-y: auto;
        }
        #log-box li { padding: 2px 0; }
        .log-ok   { color: #3fb950; }
        .log-warn { color: #d29922; }
        .log-err  { color: #f85149; }
    </style>
</head>
<body class="p-4">
<div class="container" style="max-width:780px;">
    <h4 class="mb-3">
        <span class="spinner-border spinner-border-sm me-2 text-primary" id="spinner"></span>
        <span id="page-title"><?php echo $_pageTitle; ?></span>
    </h4>
    <div id="log-box"><ul class="list-unstyled mb-0" id="log-list"></ul></div>
    <div id="result-area" class="mt-3" style="display:none;"></div>
</div>
<script>
function addLog(type, msg) {
    var li = document.createElement('li');
    li.className = 'log-' + type;
    var icons = {ok: '✓', warn: '⚠', err: '✗'};
    li.innerHTML = (icons[type] || '·') + ' ' + msg;
    document.getElementById('log-list').appendChild(li);
    var box = document.getElementById('log-box');
    box.scrollTop = box.scrollHeight;
}
function done(ok, msg) {
    document.getElementById('spinner').style.display = 'none';
    document.getElementById('page-title').textContent = ok ? 'Abgeschlossen' : 'Fehler';
    var cls = ok ? 'alert-success' : 'alert-danger';
    document.getElementById('result-area').innerHTML =
        '<div class="alert ' + cls + '">' + msg + '</div>' +
        '<a href="index.php?p=update" class="btn btn-primary">Zurück zur Update-Seite</a>';
    document.getElementById('result-area').style.display = '';
    if (ok) setTimeout(function(){ window.location.href = 'index.php?p=update'; }, 4000);
}
</script>
<?php
streamFlush();

// ─── Aktionen ─────────────────────────────────────────────────────────────────
$emit = 'streamLog';
$ok   = false;
$msg  = '';

set_time_limit(300);

try {
    switch ($action) {

        case 'backup':
            $__ver = htmlspecialchars(length($_POST['target_version'] ?? '', 32));
            $__url = trim($_POST['download_url'] ?? '');
            streamLog('ok', 'Wartungsmodus wird aktiviert…');
            $updater->enableMaintenance();
            streamLog('ok', 'Wartungsmodus aktiv.');
            $r = $updater->createBackup($emit);
            if ($r['success']) {
                $_SESSION['updater_step']           = 'backup';
                $_SESSION['updater_backup_file']    = $r['file'];
                $_SESSION['updater_target_version'] = $__ver;
                $_SESSION['updater_download_url']   = $__url;
                $ok  = true;
                $msg = 'Backup erfolgreich. Weiter mit <strong>Schritt 2: Update herunterladen</strong>.';
            } else {
                $updater->disableMaintenance();
                $msg = 'Backup fehlgeschlagen: ' . $r['error'];
            }
            break;

        case 'backup_only':
            $r = $updater->createBackup($emit);
            if ($r['success']) {
                $_SESSION['updater_step']        = 'backup_only_done';
                $_SESSION['updater_backup_file'] = $r['file'];
                $ok  = true;
                $msg = 'Backup erfolgreich erstellt.';
            } else {
                $msg = 'Backup fehlgeschlagen: ' . $r['error'];
            }
            break;

        case 'download':
            $__url = $_SESSION['updater_download_url'] ?? '';
            $r = $updater->downloadRelease($__url, $emit);
            if ($r['success']) {
                $_SESSION['updater_step']     = 'download';
                $_SESSION['updater_zip_file'] = $r['file'];
                $ok  = true;
                $msg = 'Download abgeschlossen. Weiter mit <strong>Schritt 3: Installieren</strong>.';
            } else {
                $msg = 'Download fehlgeschlagen: ' . $r['error'];
            }
            break;

        case 'install':
            $__zip = $_SESSION['updater_zip_file'] ?? '';
            $r = $updater->installUpdate($__zip, $emit);
            if ($r['success']) {
                streamLog('ok', 'Wartungsmodus wird deaktiviert…');
                $updater->disableMaintenance();
                $_SESSION['updater_step'] = 'done';
                unset($_SESSION['updater_zip_file'], $_SESSION['updater_download_url'], $_SESSION['updater_backup_file']);
                $ok  = true;
                $msg = 'Update erfolgreich installiert!';
            } else {
                $msg = 'Installation fehlgeschlagen: ' . $r['error'];
            }
            break;

        case 'restore':
            $__fn = basename(trim($_POST['backup_filename'] ?? ''));
            streamLog('ok', 'Wartungsmodus wird aktiviert…');
            $updater->enableMaintenance();
            streamLog('ok', 'Wartungsmodus aktiv.');
            $r = $updater->restoreBackup($__fn, $emit);
            streamLog('ok', 'Wartungsmodus wird deaktiviert…');
            $updater->disableMaintenance();
            if ($r['success']) {
                $ok  = true;
                $msg = 'Backup erfolgreich eingespielt!';
            } else {
                $msg = 'Wiederherstellung fehlgeschlagen: ' . $r['error'];
            }
            break;

        default:
            $msg = 'Unbekannte Aktion.';
    }
} catch (\Throwable $e) {
    $msg = 'Unerwarteter Fehler: ' . htmlspecialchars($e->getMessage());
}

echo '<script>done(' . ($ok ? 'true' : 'false') . ',' . json_encode($msg) . ');</script>' . "\n";
streamFlush();
?>
</body>
</html>
