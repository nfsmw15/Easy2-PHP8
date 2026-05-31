<?php
declare(strict_types=1);
/**
 * EASY 2.0 — Live-Updater Terminal
 *
 * POST          → CSRF prüfen, Params speichern, Redirect zu ?run=TOKEN
 * GET ?run=TOKEN → Terminal-Seite; alle Schritte laufen hier, kein Seitenwechsel
 * GET ?stream=TOKEN&step=STEP → SSE-Endpunkt für einen Schritt
 */

// ─── Session ──────────────────────────────────────────────────────────────────
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

if (!$loginsystem->auditRight('mainsave')) {
    header('Location: index.php'); exit();
}

// ─── Modus ───────────────────────────────────────────────────────────────────
if (isset($_GET['stream'])) {
    $mode = 'stream';
} elseif (isset($_GET['run'])) {
    $mode = 'ui';
} else {
    $mode = 'post';
}

// ═══════════════════════════════════════════════════════════════════════════════
// MODUS 1 — POST: CSRF prüfen, Token erzeugen, Redirect
// ═══════════════════════════════════════════════════════════════════════════════
if ($mode === 'post') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?p=update'); exit();
    }
    $__csrf  = $_POST['csrf'] ?? '';
    $__token = $_SESSION['ml_csrfToken'] ?? '';
    if (empty($__csrf) || !hash_equals($__token, $__csrf)) {
        http_response_code(403); exit('Ungültiger CSRF-Token.');
    }

    $sessionToken = bin2hex(random_bytes(16));
    $_SESSION['updater_token']  = $sessionToken;
    $_SESSION['updater_action'] = $_POST['action'] ?? '';
    $_SESSION['updater_params'] = [
        'target_version'  => length($_POST['target_version']  ?? '', 32),
        'download_url'    => trim($_POST['download_url']    ?? ''),
        'backup_filename' => basename(trim($_POST['backup_filename'] ?? '')),
    ];

    header('Location: updater_run.php?run=' . urlencode($sessionToken));
    exit();
}

// ═══════════════════════════════════════════════════════════════════════════════
// MODUS 2 — UI: Terminal-Seite (alle Schritte, kein Seitenwechsel)
// ═══════════════════════════════════════════════════════════════════════════════
if ($mode === 'ui') {
    $runToken = $_GET['run'] ?? '';
    if ($runToken !== ($_SESSION['updater_token'] ?? '')) {
        header('Location: index.php?p=update'); exit();
    }

    $action    = (string)($_SESSION['updater_action'] ?? '');
    $params    = (array)($_SESSION['updater_params']  ?? []);
    $siteName  = htmlspecialchars((string)$loginsystem->getMainData('site_title'));
    $bsTheme   = ($_COOKIE['easy2_theme'] ?? 'light') === 'dark' ? 'dark' : 'light';

    $titles = [
        'backup'      => 'Update wird eingespielt…',
        'backup_only' => 'Backup wird erstellt…',
        'restore'     => 'Backup wird eingespielt…',
    ];
    $pageTitle = htmlspecialchars($titles[$action] ?? 'Vorgang läuft…');

    // Schrittfolge für Update (backup → download → install)
    $isUpdate = ($action === 'backup');
    ?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="<?php echo $bsTheme; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle; ?> — <?php echo $siteName; ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/all.min.css" rel="stylesheet">
    <link href="font-awesome/css/v4-shims.min.css" rel="stylesheet">
    <style>
        body { padding: 2rem; }
        #log-box {
            background: #0d1117;
            color: #c9d1d9;
            font-family: ui-monospace, 'SFMono-Regular', Consolas, monospace;
            font-size: .85rem;
            border-radius: .5rem;
            padding: 1rem 1.25rem;
            min-height: 180px;
            max-height: 60vh;
            overflow-y: auto;
        }
        #log-box li { padding: 2px 0; animation: fadein .2s ease-in; }
        @keyframes fadein { from { opacity:0; transform:translateX(-6px); } to { opacity:1; transform:translateX(0); } }
        .log-ok   { color: #3fb950; }
        .log-warn { color: #d29922; }
        .log-err  { color: #f85149; }
        .step-divider { border-top: 1px solid #30363d; margin: .5rem 0; }
    </style>
</head>
<body>
<div class="container" style="max-width:780px;">
    <h4 class="mb-3">
        <span class="spinner-border spinner-border-sm me-2 text-primary" id="spinner"></span>
        <span id="page-title"><?php echo $pageTitle; ?></span>
    </h4>
    <div id="log-box"><ul class="list-unstyled mb-0" id="log-list"></ul></div>
    <div id="action-area" class="mt-3"></div>
</div>

<script>
var TOKEN   = <?php echo json_encode($runToken); ?>;
var ACTION  = <?php echo json_encode($action); ?>;
var PARAMS  = <?php echo json_encode($params); ?>;
var IS_UPDATE = <?php echo $isUpdate ? 'true' : 'false'; ?>;
var currentSrc = null;

function addLog(type, msg) {
    var icons = {ok: '<i class="fa fa-check"></i>', warn: '<i class="fa fa-exclamation-triangle"></i>', err: '<i class="fa fa-times"></i>'};
    var li = document.createElement('li');
    li.className = 'log-' + type;
    li.innerHTML = (icons[type] || '·') + ' ' + msg;
    document.getElementById('log-list').appendChild(li);
    var box = document.getElementById('log-box');
    box.scrollTop = box.scrollHeight;
}

function addDivider(label) {
    var li = document.createElement('li');
    li.className = 'step-divider';
    li.innerHTML = '<small class="text-secondary">' + label + '</small>';
    document.getElementById('log-list').appendChild(li);
}

function setTitle(title) {
    document.getElementById('page-title').textContent = title;
}

function stopSpinner() {
    document.getElementById('spinner').style.display = 'none';
}

function showNextButton(label, icon, step, btnClass) {
    var area = document.getElementById('action-area');
    area.innerHTML = '<button class="btn ' + btnClass + '" id="next-btn" onclick="runStep(\'' + step + '\')">'
        + '<i class="fa ' + icon + '"></i> ' + label + '</button>'
        + ' <a href="index.php?p=update&c=reset" class="btn btn-outline-secondary ms-2">Abbrechen</a>';
}

function showDone(ok, msg) {
    stopSpinner();
    var cls = ok ? 'alert-success' : 'alert-danger';
    var btn = ok ? 'btn-primary' : 'btn-warning';
    document.getElementById('action-area').innerHTML =
        '<div class="alert ' + cls + '">' + msg + '</div>'
        + '<a href="index.php?p=update" class="btn ' + btn + '">Zurück zur Update-Seite</a>';
    if (ok) setTimeout(function(){ window.location.href = 'index.php?p=update'; }, 4000);
}

function runStep(step) {
    // Button während Lauf deaktivieren
    var btn = document.getElementById('next-btn');
    if (btn) btn.disabled = true;
    document.getElementById('action-area').innerHTML = '';
    document.getElementById('spinner').style.display = '';

    addDivider('— ' + stepLabel(step) + ' —');

    var url = 'updater_run.php?stream=' + encodeURIComponent(TOKEN) + '&step=' + encodeURIComponent(step);
    currentSrc = new EventSource(url);

    currentSrc.onmessage = function(e) {
        try {
            var data = JSON.parse(e.data);
            if (data.done !== undefined) {
                currentSrc.close();
                onStepDone(step, data.ok === true, data.msg || '', data);
            } else {
                addLog(data.type, data.msg);
            }
        } catch(ex) {}
    };

    currentSrc.onerror = function() {
        currentSrc.close();
        stopSpinner();
        showDone(false, 'Verbindung unterbrochen.');
    };
}

function stepLabel(step) {
    var labels = {backup:'Backup erstellt', download:'Download', install:'Installation', backup_only:'Backup', restore:'Wiederherstellung'};
    return labels[step] || step;
}

function onStepDone(step, ok, msg, data) {
    stopSpinner();
    if (!ok) { showDone(false, msg); return; }

    if (step === 'backup' && IS_UPDATE) {
        addLog('ok', msg);
        setTitle('Schritt 2: Update herunterladen');
        var dlBtn = '';
        if (data && data.backup_filename) {
            dlBtn = '<a href="index.php?p=update&c=backup_download&a=' + encodeURIComponent(data.backup_filename) + '" '
                  + 'class="btn btn-outline-secondary" title="Backup jetzt herunterladen">'
                  + '<i class="fa fa-download"></i> Backup herunterladen</a>';
        }
        var area = document.getElementById('action-area');
        area.innerHTML = '<div class="d-flex gap-2 flex-wrap">'
            + '<button class="btn btn-primary" id="next-btn" onclick="runStep(\'download\')">'
            + '<i class="fa fa-download"></i> Schritt 2: Update herunterladen</button>'
            + dlBtn
            + '<a href="index.php?p=update&c=reset" class="btn btn-outline-secondary">Abbrechen</a>'
            + '</div>';
    } else if (step === 'download') {
        addLog('ok', msg);
        setTitle('Schritt 3: Update installieren');
        showNextButton('Schritt 3: Update installieren', 'fa-upload', 'install', 'btn-success');
    } else {
        // backup_only, install, restore — fertig
        showDone(true, msg);
    }
}

// Ersten Schritt automatisch starten
window.addEventListener('load', function() {
    runStep(ACTION);
});
</script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
<?php
    exit();
}

// ═══════════════════════════════════════════════════════════════════════════════
// MODUS 3 — STREAM: SSE-Endpunkt für einen Schritt
// ═══════════════════════════════════════════════════════════════════════════════
if ($mode === 'stream') {
    $runToken = $_GET['stream'] ?? '';
    $step     = $_GET['step']   ?? '';

    if ($runToken !== ($_SESSION['updater_token'] ?? '')) {
        http_response_code(403);
        echo 'data: ' . json_encode(['done' => true, 'ok' => false, 'msg' => 'Ungültiger Token.']) . "\n\n";
        exit();
    }

    $params = (array)($_SESSION['updater_params'] ?? []);
    session_write_close();

    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache, no-store');
    header('X-Accel-Buffering: no');
    if (function_exists('apache_setenv')) apache_setenv('no-gzip', '1');
    ini_set('zlib.output_compression', '0');
    while (ob_get_level() > 0) ob_end_clean();

    $emit = static function(string $type, string $msg): void {
        echo 'data: ' . json_encode(['type' => $type, 'msg' => $msg]) . "\n\n";
        if (ob_get_level() > 0) ob_flush();
        flush();
    };

    $updater = new updater();
    $ok      = false;
    $doneMsg = '';

    set_time_limit(300);

    try {
        switch ($step) {

            case 'backup':
                $emit('ok', 'Wartungsmodus wird aktiviert…');
                $updater->enableMaintenance();
                $emit('ok', 'Wartungsmodus aktiv.');
                $r = $updater->createBackup($emit, true);
                if ($r['success']) {
                    $updater->setProgress([
                        'step'           => 'backup',
                        'backup_file'    => $r['file'],
                        'target_version' => $params['target_version'] ?? '',
                        'download_url'   => $params['download_url']   ?? '',
                    ]);
                    $ok             = true;
                    $doneMsg        = 'Backup erfolgreich erstellt.';
                    $extraData      = ['backup_filename' => basename($r['file'])];
                } else {
                    $updater->disableMaintenance();
                    $doneMsg = 'Backup fehlgeschlagen: ' . ($r['error'] ?? '');
                }
                break;

            case 'backup_only':
                $r = $updater->createBackup($emit);
                if ($r['success']) {
                    $updater->setProgress(['step' => 'backup_only_done', 'backup_file' => $r['file']]);
                    $ok      = true;
                    $doneMsg = 'Backup erfolgreich erstellt.';
                } else {
                    $doneMsg = 'Backup fehlgeschlagen: ' . ($r['error'] ?? '');
                }
                break;

            case 'download':
                $progress = $updater->getProgress();
                $url      = (string)($progress['download_url'] ?? $params['download_url'] ?? '');
                $r = $updater->downloadRelease($url, $emit);
                if ($r['success']) {
                    $progress['step']     = 'download';
                    $progress['zip_file'] = $r['file'];
                    $updater->setProgress($progress);
                    $ok      = true;
                    $doneMsg = 'Download abgeschlossen.';
                } else {
                    $doneMsg = 'Download fehlgeschlagen: ' . ($r['error'] ?? '');
                }
                break;

            case 'install':
                $progress = $updater->getProgress();
                $zip      = (string)($progress['zip_file'] ?? '');
                $r = $updater->installUpdate($zip, $emit);
                if ($r['success']) {
                    $emit('ok', 'Wartungsmodus wird deaktiviert…');
                    $updater->disableMaintenance();
                    $updater->setProgress(['step' => 'done']);
                    $ok      = true;
                    $doneMsg = 'Update erfolgreich installiert!';
                } else {
                    $doneMsg = 'Installation fehlgeschlagen: ' . ($r['error'] ?? '');
                }
                break;

            case 'restore':
                $filename = $params['backup_filename'] ?? '';
                $emit('ok', 'Wartungsmodus wird aktiviert…');
                $updater->enableMaintenance();
                $emit('ok', 'Wartungsmodus aktiv.');
                $r = $updater->restoreBackup($filename, $emit);
                $emit('ok', 'Wartungsmodus wird deaktiviert…');
                $updater->disableMaintenance();
                if ($r['success']) {
                    $ok      = true;
                    $doneMsg = 'Backup erfolgreich eingespielt!';
                } else {
                    $doneMsg = 'Wiederherstellung fehlgeschlagen: ' . ($r['error'] ?? '');
                }
                break;

            default:
                $doneMsg = 'Unbekannter Schritt.';
        }
    } catch (\Throwable $e) {
        $doneMsg = 'Unerwarteter Fehler: ' . htmlspecialchars($e->getMessage());
    }

    $donePayload = array_merge(['done' => true, 'ok' => $ok, 'msg' => $doneMsg], $extraData ?? []);
    echo 'data: ' . json_encode($donePayload) . "\n\n";
    flush();
    exit();
}
