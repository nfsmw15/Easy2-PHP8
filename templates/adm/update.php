<?php
/** @var loginsystem $loginsystem */
/** @var updater $updater */
// Zugriffsschutz: nur Admins mit mainsave-Recht
if (!$loginsystem->auditRight('mainsave')) {
    echo '<div class="container"><div class="alert alert-danger mt-4"><i class="fa fa-ban"></i> Keine Berechtigung.</div></div>';
    return;
}

$_upd_step    = $_SESSION['updater_step'] ?? '';
$_upd_backup  = $_SESSION['updater_backup_file'] ?? '';
$_upd_zip     = $_SESSION['updater_zip_file'] ?? '';
$_upd_version = $_SESSION['updater_target_version'] ?? '';

$_avail       = $updater->getAvailableVersion();
$_avail_ver   = $_avail['version'];
$_avail_url   = $_avail['url'];
$_has_update  = $_avail_ver !== '' && version_compare($_avail_ver, EASY_VERSION, '>');
$_maintenance = $updater->isMaintenanceActive();
$_backupDir   = $updater->getBackupDir();
$_backups     = $updater->listBackups();
?>
<div class="container">
    <h1 class="mt-4 mb-3">Update</h1>
    <div class="bg-body-tertiary rounded-2 px-3 py-2 mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="?">Übersicht</a></li>
            <li class="breadcrumb-item">Verwaltung</li>
            <li class="breadcrumb-item active">Update</li>
        </ol>
    </div>

    <?php echo $error ?? ''; ?>

    <?php if ($_maintenance): ?>
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
        <span><i class="fa fa-wrench"></i> <strong>Wartungsmodus ist aktiv</strong> seit <?php echo htmlspecialchars($updater->getMaintenanceStartTime()); ?> — die Seite ist für normale Besucher gesperrt.</span>
        <form method="post" action="?p=update&c=disable_maintenance" class="ms-3 mb-0">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-sm btn-warning"><i class="fa fa-power-off"></i> Wartungsmodus beenden</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Linke Spalte: Update-Prozess -->
        <div class="col-lg-8 mb-4">

            <?php if ($_upd_step === '' || $_upd_step === 'config'): ?>
            <!-- ── Schritt 1: Übersicht ── -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fa fa-refresh"></i> Update-Status</span>
                    <span>
                        Version: <strong><?php echo htmlspecialchars(EASY_VERSION); ?></strong>
                        <?php if ($_has_update): ?>
                            <span class="badge bg-warning text-dark ms-1"><i class="fa fa-arrow-up"></i> <?php echo htmlspecialchars($_avail_ver); ?> verfügbar</span>
                        <?php elseif ($_avail_ver !== ''): ?>
                            <span class="badge bg-success ms-1"><i class="fa fa-check"></i> aktuell</span>
                        <?php else: ?>
                            <span class="badge bg-secondary ms-1"><i class="fa fa-question"></i> unbekannt</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if ($_has_update): ?>
                        <p class="mb-3">Eine neue Version <strong><?php echo htmlspecialchars($_avail_ver); ?></strong> ist verfügbar. Der Update-Prozess läuft in folgenden Schritten ab:</p>
                        <ol class="mb-3">
                            <li>Backup der aktuellen Installation erstellen</li>
                            <li>Neue Version von GitHub herunterladen</li>
                            <li>Dateien installieren (ohne Config &amp; Benutzerdaten)</li>
                        </ol>
                    <?php else: ?>
                        <p class="text-muted mb-3">Es ist kein Update verfügbar. Du kannst trotzdem manuell ein Backup erstellen.</p>
                    <?php endif; ?>

                    <!-- Backup-Pfad konfigurieren -->
                    <form method="post" action="?p=update&c=save_config" class="mb-3">
                        <?php echo csrf_field(); ?>
                        <label class="form-label fw-semibold"><i class="fa fa-folder-open"></i> Backup-Verzeichnis:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-hdd-o"></i></span>
                            <input type="text" name="backup_dir" class="form-control" value="<?php echo htmlspecialchars($_backupDir); ?>" placeholder="z.B. /home/user/backups/ oder tmp/backups/">
                            <button type="submit" class="btn btn-outline-secondary">Speichern</button>
                        </div>
                        <div class="form-text">Absoluter Pfad oder relativ zum CMS-Verzeichnis. Empfohlen: außerhalb des Web-Roots.</div>
                    </form>

                    <?php if ($_has_update): ?>
                    <form method="post" action="?p=update&c=backup">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="target_version" value="<?php echo htmlspecialchars($_avail_ver); ?>">
                        <input type="hidden" name="download_url" value="<?php echo htmlspecialchars($_avail_url); ?>">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-database"></i> Schritt 1: Backup erstellen &amp; starten
                            </button>
                        </div>
                    </form>
                    <?php else: ?>
                    <form method="post" action="?p=update&c=backup_only">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fa fa-database"></i> Nur Backup erstellen
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <?php elseif ($_upd_step === 'backup'): ?>
            <!-- ── Schritt 2: Backup fertig, Download ── -->
            <div class="card mb-3">
                <div class="card-header"><i class="fa fa-check text-success"></i> Schritt 1 abgeschlossen: Backup erstellt</div>
                <div class="card-body">
                    <p><i class="fa fa-file-zip-o"></i> Backup: <code><?php echo htmlspecialchars(basename($_upd_backup)); ?></code></p>
                    <p class="text-muted">Der Wartungsmodus ist aktiviert. Bereit zum Download der Version <strong><?php echo htmlspecialchars($_upd_version); ?></strong>.</p>
                    <div class="d-flex gap-2">
                        <form method="post" action="?p=update&c=download">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-download"></i> Schritt 2: Update herunterladen
                            </button>
                        </form>
                        <a href="?p=update&c=reset" class="btn btn-outline-secondary"><i class="fa fa-undo"></i> Abbrechen</a>
                    </div>
                </div>
            </div>

            <?php elseif ($_upd_step === 'download'): ?>
            <!-- ── Schritt 3: Download fertig, Installieren ── -->
            <div class="card mb-3">
                <div class="card-header"><i class="fa fa-check text-success"></i> Schritt 2 abgeschlossen: Update heruntergeladen</div>
                <div class="card-body">
                    <p class="text-muted">Das Update-Paket für Version <strong><?php echo htmlspecialchars($_upd_version); ?></strong> ist bereit zur Installation.</p>
                    <div class="alert alert-warning mb-3">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Hinweis:</strong> Mit dem nächsten Schritt werden die Dateien überschrieben.
                        Die Konfiguration (<code>system/config.inc.php</code>), <code>avatare/</code> und eigene Anpassungen bleiben erhalten.
                    </div>
                    <div class="d-flex gap-2">
                        <form method="post" action="?p=update&c=install">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-upload"></i> Schritt 3: Update installieren
                            </button>
                        </form>
                        <a href="?p=update&c=reset" class="btn btn-outline-secondary"><i class="fa fa-undo"></i> Abbrechen</a>
                    </div>
                </div>
            </div>

            <?php elseif ($_upd_step === 'done'): ?>
            <!-- ── Schritt 4: Fertig ── -->
            <div class="card mb-3 border-success">
                <div class="card-header bg-success text-white"><i class="fa fa-check-circle"></i> Update erfolgreich abgeschlossen!</div>
                <div class="card-body">
                    <p>Das CMS wurde auf Version <strong><?php echo htmlspecialchars($_upd_version ?: EASY_VERSION); ?></strong> aktualisiert.</p>
                    <p>Der Wartungsmodus wurde automatisch deaktiviert.</p>
                    <a href="?p=update&c=reset" class="btn btn-primary"><i class="fa fa-refresh"></i> Update-Seite neu laden</a>
                </div>
            </div>

            <?php elseif ($_upd_step === 'backup_only_done'): ?>
            <div class="card mb-3 border-success">
                <div class="card-header"><i class="fa fa-check text-success"></i> Backup erstellt</div>
                <div class="card-body">
                    <p>Backup <code><?php echo htmlspecialchars(basename($_upd_backup)); ?></code> wurde erfolgreich erstellt.</p>
                    <a href="?p=update&c=reset" class="btn btn-outline-primary"><i class="fa fa-refresh"></i> Zurück</a>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Rechte Spalte: Backups -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header"><i class="fa fa-database"></i> Vorhandene Backups</div>
                <div class="card-body p-0">
                    <?php if (empty($_backups)): ?>
                        <div class="p-3 text-muted"><em>Keine Backups vorhanden.</em></div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                        <?php foreach ($_backups as $_bk): ?>
                            <li class="list-group-item">
                                <div class="fw-semibold text-break" style="font-size:.85em;"><?php echo htmlspecialchars($_bk['filename']); ?></div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted"><?php echo updater::formatBytes($_bk['size']); ?> &mdash; <?php echo date('d.m.Y H:i', $_bk['mtime']); ?></small>
                                    <form method="post" action="?p=update&c=restore" class="ms-2" onsubmit="return confirm('Backup wirklich einspielen? Die aktuellen Dateien werden überschrieben.');">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="backup_filename" value="<?php echo htmlspecialchars($_bk['filename']); ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-warning"><i class="fa fa-undo"></i> Einspielen</button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><i class="fa fa-shield"></i> Geschützte Dateien</div>
                <div class="card-body" style="font-size:.85em;">
                    <p class="text-muted mb-2">Diese Dateien werden beim Update <strong>nie</strong> überschrieben:</p>
                    <ul class="mb-0 ps-3">
                        <li><code>system/config.inc.php</code></li>
                        <li><code>system/config.user.php</code></li>
                        <li><code>system/functions.user.php</code></li>
                        <li><code>system/run.user.php</code></li>
                        <li><code>system/classes.run.user.php</code></li>
                        <li><code>avatare/</code></li>
                        <li><code>tmp/</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
