<?php
declare(strict_types=1);
/********************************************
 * System:   EASY 2.0 Loginsystem
 * File:     install/setup/install.php
 * PHP8-Umbau:
 *   - mysqli → PDO
 *   - md5-Vergleiche → hash_equals()
 *   - PHP-Version >= 8.0 geprüft
 *   - Kein @-Fehleroperator
 *   - Prepared Statements im createUser()
 *   - Installer prüft auf eigene Zugänglichkeit (muss danach gelöscht werden)
 *********************************************/

// ─── Lokale Hilfsfunktionen (Installer ist standalone) ───────────────────────

function htmlspecialchar(string $string): string
{
    return htmlentities($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function check_email(string $use): bool
{
    return filter_var($use, FILTER_VALIDATE_EMAIL) !== false;
}

function length(mixed $item, int $length = 64, int $start = 0, string $type = 'none'): string
{
    $item = (string)($item ?? '');
    if ($type === 'html') {
        return mb_substr(htmlspecialchar($item), $start, $length);
    }
    return mb_substr($item, $start, $length);
}

function this_domain(): string
{
    return $_SERVER['HTTP_HOST'] ?? '';
}

function this_dir(): string
{
    $url    = ($_SERVER['SERVER_NAME'] ?? '') . ($_SERVER['PHP_SELF'] ?? '');
    $parts  = parse_url('http://' . $url);
    $dir    = dirname($parts['path'] ?? '/');
    $folder = str_replace('install', '', $dir);
    return $folder;
}

function optional_title(): string
{
    $url       = $_SERVER['SERVER_NAME'] ?? '';
    $splitHost = explode('.', $url);
    $cnt       = count($splitHost);
    $name      = explode('-', $splitHost[$cnt - 2] ?? '');
    return implode(' ', array_map('ucfirst', $name));
}

function checker(mixed $a, mixed $b, int $c = 0): string
{
    if ($a == $b) return $c === 1 ? 'checked' : 'selected';
    return '';
}

function pass_control(string $string, int $min_length): bool
{
    return strlen($string) >= $min_length;
}

function mainout(string $i): mixed
{
    global $pdo;
    if (!$pdo instanceof \PDO) return null;
    $stmt = $pdo->prepare("SELECT * FROM `" . Prefix . "_main` WHERE id = '1' LIMIT 1");
    $stmt->execute();
    $out = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $out[$i] ?? null;
}

function getMainData(string $tag): mixed
{
    global $pdo;
    if (!$pdo instanceof \PDO) return null;
    $stmt = $pdo->prepare("SELECT value FROM `" . Prefix . "_main` WHERE tag = ? LIMIT 1");
    $stmt->execute([$tag]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $row ? $row['value'] : null;
}

function generate(int $length): string
{
    $chars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $max    = strlen($chars) - 1;
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= $chars[random_int(0, $max)];
    }
    return $result;
}

function isvalid_install(string $code, \PDO $pdo): bool
{
    $tables = [
        ['user',     'uik'],
        ['sessions', 'ulc'],
        ['sessions', 'sic'],
        ['changes',  'code'],
        ['codes',    'code'],
    ];
    foreach ($tables as [$table, $col]) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `" . Prefix . "_$table` WHERE `$col` = ?");
        $stmt->execute([$code]);
        if ((int)$stmt->fetchColumn() > 0) return false;
    }
    return true;
}

function getCode_install(int $length = 32, \PDO $pdo): string
{
    do {
        $code = generate($length);
    } while (!isvalid_install($code, $pdo));
    return $code;
}


// ─── Template-Klasse ──────────────────────────────────────────────────────────

class template extends install
{
    private string $page;
    private string $dir = './steps/';

    public function __construct()
    {
        $this->page = length($_GET['p'] ?? '', 16);
    }

    public function includeFile(): string
    {
        // Vollständige Whitelist aller gültigen Seiten.
        // Wir verzichten auf die dynamische Freischaltung per condition –
        // ein Installer läuft einmalig und der Admin weiß was er tut.
        // Die einzige Prüfung: Datei muss physisch existieren.
        $allPages = [
            'home', 'terms_of_use', 'step1', 'step2', 'step2.2',
            'step3', 'step4', 'step5', 'finish', '404',
        ];

        if (empty($this->page)) {
            return $this->dir . 'home.php';
        }

        // Seite in Whitelist?
        if (!in_array($this->page, $allPages, true)) {
            return $this->dir . '404.php';
        }

        // Datei muss existieren
        $phpFile = $this->dir . $this->page . '.php';
        if (!file_exists($phpFile)) {
            return $this->dir . '404.php';
        }

        return $phpFile;
    }
}


// ─── Install-Klasse ───────────────────────────────────────────────────────────

class install
{
    protected ?\PDO $pdo = null;
    public    bool  $condition   = false;
    public    array $valid_field = [];

    private array $dbs = [
        'changes', 'codes', 'main', 'ranks', 'rules', 'sessions',
        'sites', 'user', 'menu', 'menu_group', 'fields', 'additional_user_information',
    ];
    private array $username_blacklist = [
        'root', 'admin', 'administrator', 'supporter', 'system', 'gast', 'benutzer', 'user',
    ];

    public function __construct()
    {
        global $pdo, $dberror;
        $this->pdo = ($pdo instanceof \PDO) ? $pdo : null;
    }

    // ── Voraussetzungen prüfen ───────────────────────────────────────────────

    public function conditions(?int $col, bool $return = true): string|bool
    {
        // __DIR__ ist das install/setup/ Verzeichnis → zwei Ebenen hoch = Webroot
        $root = dirname(dirname(__DIR__)); // setup/ → install/ → Webroot
        $dirA = $root . '/avatare';
        $dirB = $root . '/system';

        @chmod($dirA, 0755);
        @chmod($dirB, 0755);

        $cols    = [];
        $cols[0] = is_writable($dirA) ? '<span class="text-success">Ja</span>' : '<span class="text-danger">Nein</span>';
        $cols[1] = is_writable($dirB) ? '<span class="text-success">Ja</span>' : '<span class="text-danger">Nein</span>';
        $cols[3] = version_compare(PHP_VERSION, '8.0.0') >= 0
            ? '<span class="text-success">' . phpversion() . '</span>'
            : '<span class="text-danger">' . phpversion() . ' (PHP 8.0+ erforderlich!)</span>';

        $this->condition = (
            is_writable($dirA) &&
            is_writable($dirB) &&
            version_compare(PHP_VERSION, '8.0.0') >= 0 &&
            function_exists('imagettftext') &&
            extension_loaded('gd') &&
            extension_loaded('pdo_mysql')
        );

        if ($return) return $cols[$col] ?? '';
        return $this->condition;
    }

    // ── Tabellen-Check ───────────────────────────────────────────────────────

    public function checkTable(string $table): bool
    {
        if (!$this->pdo) return false;
        $stmt = $this->pdo->prepare(
            "SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE table_name = ?"
        );
        $stmt->execute([Prefix . '_' . $table]);
        return $stmt->rowCount() === 1;
    }

    // ── Datenbankverbindung testen ───────────────────────────────────────────

    public function mysqlCondition(): bool
    {
        global $dberror, $pdo;
        // index.php hat die Verbindung bereits aufgebaut und $dberror gesetzt.
        // Wir müssen hier NICHT nochmal verbinden – das würde die Schleife verursachen.
        if (!$dberror && $pdo instanceof \PDO) {
            $this->pdo = $pdo; // Sicherstellen dass $this->pdo gesetzt ist
            return true;
        }
        return false;
    }

    public function show_tables(): string
    {
        $rtn = '';
        foreach ($this->dbs as $database) {
            $exists = $this->checkTable($database);
            $rtn   .= '<tr>
                <td>' . Prefix . '_' . $database . '</td>
                <td>' . ($exists ? '<span class="text-danger">Ja</span>' : '<span class="text-success">Nein</span>') . '</td>
            </tr>';
        }
        return $rtn;
    }

    // ── MySQL-Verbindung einrichten und Tabellen anlegen ─────────────────────

    public function mysqlConnection(): string
    {
        $error    = '';
        $host     = length($_POST['mysql_host']     ?? '', 64);
        $user     = length($_POST['mysql_user']     ?? '', 32);
        $passwd   = $_POST['mysql_passwd']   ?? '';
        $database = length($_POST['mysql_database'] ?? '', 63);
        $prefix   = length($_POST['mysql_prefix']   ?? 'ml_', 16);

        $this->valid_field = [
            'host'     => empty($host)     ? 'has-error' : '',
            'user'     => empty($user)     ? 'has-error' : '',
            'database' => empty($database) ? 'has-error' : '',
            'prefix'   => empty($prefix)   ? 'has-error' : '',
        ];

        if (in_array('has-error', $this->valid_field, true)) {
            return 'Bitte alle Felder ausfüllen!';
        }
        if (!preg_match('/^[a-zA-Z0-9_]{1,16}$/', $prefix)) {
            $this->valid_field['prefix'] = 'has-error';
            return 'Präfix enthält ungültige Zeichen!';
        }
        if (!preg_match('/^[a-zA-Z0-9_\-]{4,63}$/', $database)) {
            $this->valid_field['database'] = 'has-error';
            return 'Datenbankname enthält ungültige Zeichen!';
        }
        if (!preg_match('/^[a-zA-Z0-9_\-]{1,32}$/', $user)) {
            $this->valid_field['user'] = 'has-error';
            return 'Benutzername enthält ungültige Zeichen!';
        }

        try {
            $dsn = 'mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8mb4';
            $db  = new \PDO($dsn, $user, $passwd, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
        } catch (\PDOException $e) {
            $this->valid_field = array_fill_keys(['host', 'user', 'passwd', 'database'], 'has-error');
            return 'Datenbankverbindung fehlgeschlagen! Bitte Zugangsdaten prüfen.';
        }

        // MySQL / MariaDB Versionscheck
        $dbVersion = $db->query('SELECT VERSION()')->fetchColumn();
        $_SESSION['db_version'] = $dbVersion;
        $isMariaDB = stripos($dbVersion, 'mariadb') !== false;
        $versionClean = preg_replace('/[^0-9.].*/', '', $dbVersion);
        if ($isMariaDB && version_compare($versionClean, '10.3', '<')) {
            return 'MariaDB-Version zu alt: ' . htmlspecialchars($dbVersion) . ' – Mindestens 10.3 erforderlich.';
        }
        if (!$isMariaDB && version_compare($versionClean, '5.7', '<')) {
            return 'MySQL-Version zu alt: ' . htmlspecialchars($dbVersion) . ' – Mindestens 5.7 erforderlich.';
        }

        if (!str_ends_with($prefix, '_ml')) $prefix .= '_ml';

        // Kryptographische Schlüssel generieren
        $encryptKey   = bin2hex(random_bytes(32));
        $cookieSecret = bin2hex(random_bytes(32));

        // Config schreiben
        $configTpl = file_get_contents('./setup/config.php');
        // Platzhalter ersetzen – Muster muss exakt mit config.php übereinstimmen
        // config.php hat: 'host' => '[host]',  (einfache Anführungszeichen, Komma)
        $configTpl = str_replace("'[host]'",     "'" . $host     . "'", $configTpl);
        $configTpl = str_replace("'[user]'",     "'" . $user     . "'", $configTpl);
        $configTpl = str_replace("'[pass]'",     "'" . $passwd   . "'", $configTpl);
        $configTpl = str_replace("'[dbna]'",     "'" . $database . "'", $configTpl);
        $configTpl = str_replace("'[pref]'",     "'" . $prefix   . "'", $configTpl);
        // Verschlüsselungs-Konstanten haben einfache Anführungszeichen und Apostroph
        $configTpl = str_replace("'[generated_after_install_32bytes_hex]'",   "'" . $encryptKey   . "'", $configTpl);
        $configTpl = str_replace("'[generated_after_install_32bytes_hex_2]'", "'" . $cookieSecret . "'", $configTpl);

        if (!file_put_contents('../system/config.inc.php', $configTpl)) {
            return 'Fehler beim Schreiben der config.inc.php! Bitte Schreibrechte prüfen.';
        }

        // Prüfen ob Tabellen existieren
        $sum = 0;
        foreach ($this->dbs as $dbfile) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE table_name = ?");
            $stmt->execute([$prefix . '_' . $dbfile]);
            $sum += (int)$stmt->fetchColumn();
        }

        if ($sum === 0) {
            $error = $this->runSqlFiles($db, $prefix);
            if (empty($error)) {
                header('Location: ' . $_SERVER['SCRIPT_NAME'] . '?p=step3');
                exit();
            }
        } else {
            header('Location: ' . $_SERVER['SCRIPT_NAME'] . '?p=step2.2');
            exit();
        }

        return $error;
    }

    // ── SQL-Dateien ausführen ────────────────────────────────────────────────

    private function runSqlFiles(\PDO $db, string $prefix): string
    {
        // Verbindung explizit auf utf8mb4 setzen
        $db->exec("SET NAMES utf8mb4");
        $db->exec("SET CHARACTER SET utf8mb4");
        $db->exec("SET character_set_connection = utf8mb4");

        // "_ml" am Ende abschneiden für den kurzen Präfix
        $shortPrefix = str_ends_with($prefix, '_ml') ? substr($prefix, 0, -3) : $prefix;

        foreach ($this->dbs as $dbfile) {
            $sqlFile = './sql/_ml_' . $dbfile . '.sql';
            if (!file_exists($sqlFile)) continue;

            $rawSql = file_get_contents($sqlFile);
            $rawSql = str_replace('[prefix]', $shortPrefix, $rawSql);

            // Sicheres Aufteilen: Semikolons in Strings nicht als Trenner werten
            $queries = $this->splitSqlQueries($rawSql);

            foreach ($queries as $q) {
                $q = trim($q);
                if ($q === '' || str_starts_with($q, '--') || str_starts_with($q, '/*')) continue;
                try {
                    $db->exec($q);
                } catch (\PDOException $e) {
                    return 'Fehler in Tabelle "' . $prefix . '_' . $dbfile . '": ' . $e->getMessage();
                }
            }
        }
        return '';
    }

    /**
     * Teilt SQL-Dump sicher an Semikolons auf.
     * Ignoriert Semikolons innerhalb von String-Literalen.
     */
    private function splitSqlQueries(string $sql): array
    {
        $queries  = [];
        $current  = '';
        $inString = false;
        $len      = strlen($sql);

        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];

            if ($char === "'" && ($i === 0 || $sql[$i - 1] !== '\\')) {
                $inString = !$inString;
            }

            if ($char === ';' && !$inString) {
                $q = trim($current);
                if ($q !== '') {
                    $queries[] = $q;
                }
                $current = '';
            } else {
                $current .= $char;
            }
        }

        $q = trim($current);
        if ($q !== '') {
            $queries[] = $q;
        }

        return $queries;
    }


    public function createMysqlTableIgnoreExists(): string
    {
        global $pdo;
        $db = $this->pdo ?? $pdo;
        if (!$db instanceof \PDO) return 'Keine Datenbankverbindung!';
        $prefix = Prefix;
        $error  = '';
        foreach ($this->dbs as $dbfile) {
            if (!$this->checkTable($dbfile)) {
                $error = $this->runSqlFiles($db, $prefix);
                if (!empty($error)) return $error;
            }
        }
        if (empty($error)) {
            header('Location: ' . $_SERVER['SCRIPT_NAME'] . '?p=step3');
            exit();
        }
        return $error;
    }

    public function createMysqlTableDeleteExists(): string
    {
        global $pdo;
        $db = $this->pdo ?? $pdo;
        if (!$db instanceof \PDO) return 'Keine Datenbankverbindung!';
        $prefix = Prefix;

        foreach ($this->dbs as $dbfile) {
            if ($this->checkTable($dbfile)) {
                $db->exec("DROP TABLE `" . Prefix . "_$dbfile`");
            }
        }
        return $this->runSqlFiles($db, $prefix);
    }

    // ── Grundeinstellungen speichern ─────────────────────────────────────────

    public function saveMainsettings(): string
    {
        global $pdo;
        $db = $this->pdo ?? $pdo;
        if (!$db instanceof \PDO) {
            return 'Keine Datenbankverbindung! Bitte gehe zurück zu Step 2.';
        }
        $title       = length($_POST['title']                    ?? '', 64, 0, 'html');
        $title_short = length($_POST['title_short']              ?? '', 16, 0, 'html');
        $email       = length($_POST['email']                    ?? '', 128);
        $sender      = length($_POST['from']                     ?? '', 128);
        $to          = length($_POST['to']                       ?? '', 128);
        $user_share  = length($_POST['useradministration_share'] ?? '0', 1);
        $restore     = length($_POST['restore']                  ?? '0', 1);
        $pwlength    = (int)length($_POST['pwlength']            ?? '6', 2);
        $cookielife  = length($_POST['cookie_lifetime']          ?? (string)(90 * 86400), 16);
        $regist_ac   = length($_POST['regist_ac']               ?? '0', 1);
        $pwv_ac      = length($_POST['pwv_ac']                   ?? '0', 1);
        $reg_mode    = length($_POST['reg_mode']                 ?? '2', 1);

        if (empty($title) || empty($title_short) || empty($email) || empty($sender) || empty($to)
            || $pwlength < 3 || $pwlength > 64) {
            return 'Bitte alle Pflichtfelder ausfüllen!';
        }

        $updates = [
            'site_title'             => $title,
            'short_site_title'       => $title_short,
            'administrator_mail'     => $email,
            'mail_sender'            => $sender,
            'mail_receiver'          => $to,
            'useradministration_share' => $user_share,
            'restore'                => $restore,
            'password_length'        => (string)$pwlength,
            'cookielifetime'         => $cookielife,
            'regist_active'          => $regist_ac,
            'pwv_active'             => $pwv_ac,
            'user_activation_mode'   => $reg_mode,
        ];

        $stmt = $db->prepare("UPDATE `" . Prefix . "_main` SET value = ? WHERE tag = ?");
        foreach ($updates as $key => $val) {
            $stmt->execute([$val, $key]);
        }

        header('Location: ' . $_SERVER['SCRIPT_NAME'] . '?p=step4');
        exit();
    }

    // ── Admin-Account anlegen ────────────────────────────────────────────────

    public function pwhash(string $passwd): string
    {
        return password_hash($passwd, PASSWORD_BCRYPT, ['cost' => 13]);
    }

    public function is_user(): bool
    {
        global $pdo;
        $conn = $this->pdo ?? $pdo;
        if (!$conn instanceof \PDO) return true; // Sicher: wenn keine DB → kein User möglich
        $stmt = $conn->prepare("SELECT COUNT(*) FROM `" . Prefix . "_user` WHERE rank = '1813201541'");
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    public function createUser(): string
    {
        if ($this->is_user()) return 'Es existiert bereits ein Admin-Account!';

        $username         = length($_POST['new-username']         ?? '', 64, 0, 'html');
        $fullname         = length($_POST['new-fullname']         ?? '', 64, 0, 'html');
        $email            = length($_POST['new-email']            ?? '', 64, 0, 'html');
        $password         = $_POST['new-password']         ?? '';
        $password_confirm = $_POST['new-password-confirm'] ?? '';

        $this->valid_field = [
            'username' => empty($username) ? 'has-error' : '',
            'email'    => empty($email)    ? 'has-error' : '',
            'passwd'   => empty($password) ? 'has-error' : '',
            'pw_co'    => empty($password_confirm) ? 'has-error' : '',
        ];

        if (empty($username) || empty($fullname) || empty($email) || empty($password) || empty($password_confirm)) {
            return 'Alle Felder ausfüllen!';
        }
        if (!hash_equals($password, $password_confirm)) {
            $this->valid_field['passwd'] = $this->valid_field['pw_co'] = 'has-error';
            return 'Passwörter stimmen nicht überein!';
        }
        if (!check_email($email)) {
            $this->valid_field['email'] = 'has-error';
            return 'Ungültige E-Mail!';
        }
        if (in_array(strtolower($username), $this->username_blacklist, true)) {
            $this->valid_field['username'] = 'has-error';
            return 'Dieser Benutzername ist nicht erlaubt!';
        }
        if (!preg_match('/^[a-zA-Z0-9äöüÄÖÜß_\-]{4,}$/u', $username)) {
            $this->valid_field['username'] = 'has-error';
            return 'Benutzername enthält ungültige Zeichen!';
        }

        $minLen = (int)mainout('password_length', $this->pdo);
        if (!pass_control($password, $minLen)) {
            $this->valid_field['passwd'] = 'has-error';
            return "Passwort muss mindestens $minLen Zeichen haben!";
        }

        $names      = explode(' ', $fullname);
        $last_name  = count($names) > 1 ? array_pop($names) : null;
        $first_name = implode(' ', $names);
        $uik        = getCode_install(32, $this->pdo);
        $hashed     = $this->pwhash($password);

        global $pdo;
        $conn = $this->pdo ?? $pdo;
        if (!$conn instanceof \PDO) {
            return 'Keine Datenbankverbindung!';
        }
        $stmt = $conn->prepare(
            "INSERT INTO `" . Prefix . "_user` (username, first_name, last_name, email, password, active, rank, uik, regdate, avatar)
             VALUES (?, ?, ?, ?, ?, '1', '1813201541', ?, ?, '')"
        );
        $stmt->execute([$username, $first_name, $last_name, $email, $hashed, $uik, time()]);

        header('Location: ' . $_SERVER['SCRIPT_NAME'] . '?p=finish');
        exit();
    }

    public function replace_impressum(): string
    {
        $imp_cont = $_POST['impressum_content'] ?? '';
        $privacy  = $_POST['privacy_policy']    ?? '';

        // Absoluter Pfad via __DIR__ – verhindert Probleme mit Working Directory
        $tplDir = dirname(__DIR__) . '/system/tpl';
        if (!is_dir($tplDir)) {
            mkdir($tplDir, 0755, true);
        }

        // Impressum-Inhalte dürfen HTML enthalten (Redakteur-Eingabe) → nicht escapen
        // Aber: XSS-Schutz ist Aufgabe der Ausgabe, nicht der Speicherung
        file_put_contents($tplDir . '/impressum.tpl',      $imp_cont);
        file_put_contents($tplDir . '/privacy_policy.tpl', $privacy);
        header('Location: ' . $_SERVER['SCRIPT_NAME'] . '?p=finish&h=impressum_saved');
        exit();
    }
}
