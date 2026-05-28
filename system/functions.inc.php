<?php
declare(strict_types=1);
/********************************************
 *
 * System:   EASY 2.0 Loginsystem
 * File:     functions.inc.php
 * PHP8-Umbau:
 *   - mcrypt -> OpenSSL AES-256-GCM (authenticated encryption)
 *   - generate() -> random_bytes() statt mt_rand/microtime seed
 *   - each() entfernt (PHP 8 removed)
 *   - encodeRand/decodeRand durch HMAC-gesichertes Cookie-Token ersetzt
 *   - printarray/printobject entfernt (Debug-Tool, kein Produktionscode)
 * DSGVO:
 *   - errormail() gibt keine internen Stack-Infos an Frontend
 *   - IP-Adresse wird nur noch gehasht gespeichert, wenn nötig
 *
 * PHP 8 modifications: Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 *********************************************/

define('EASY_VERSION', '1.2.7');
define('EASY_BRANCH',  'bs5');

// ─── Input-Sanitierung ──────────────────────────────────────────────────────

function htmlspecialchar(string $string): string
{
    return htmlentities($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Kürzt und sanitiert einen Eingabewert.
 * $type = "html"  → htmlentities
 * $type = "none"  → nur substr
 */
function length(mixed $item, int $length = 64, int|null $start = 0, string $type = 'html'): string
{
    $item  = (string)($item ?? '');
    $start = $start ?? 0; // null → 0 (PHP 8 strict types)

    return match ($type) {
        'html' => mb_substr(htmlspecialchar($item), $start, $length),
        // 'sql' war früher real_escape_string → mit PDO nicht mehr nötig
        // Prepared Statements übernehmen die Absicherung.
        // Fallback: unverändert zurückgeben (wie 'none')
        default => mb_substr($item, $start, $length),
    };
}

// ─── Validierung ────────────────────────────────────────────────────────────

function check_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function check_filename(string $string): bool
{
    return (bool)preg_match('/^[_a-zA-Z0-9()\s\-.]*\.[a-zA-Z]{1,16}$/', $string);
}

function check_date(string $date, string $format, string $sep): bool
{
    $pos1  = strpos($format, 'd');
    $pos2  = strpos($format, 'm');
    $pos3  = strpos($format, 'Y');
    $check = explode($sep, $date);
    if (!isset($check[$pos1], $check[$pos2], $check[$pos3])) {
        return false;
    }
    return checkdate((int)$check[$pos2], (int)$check[$pos1], (int)$check[$pos3]);
}

// ─── Domain / URL ───────────────────────────────────────────────────────────

function this_domain(): string
{
    return $_SERVER['HTTP_HOST'] ?? '';
}

function getCurrentUrl(): string
{
    $scheme = is_https() ? 'https' : 'http';
    $url    = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $parts  = parse_url($url);
    return ($parts['scheme'] ?? $scheme) . '://' . ($parts['host'] ?? '') . ($parts['path'] ?? '');
}

// ─── Zufallscode (kryptografisch sicher) ────────────────────────────────────

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

function generate_hex(): string
{
    return bin2hex(random_bytes(32));
}

function isvalid(string $code, array|string|null $tables = null, array|string|null $columns = null): bool
{
    global $loginsystem;
    $tables  = is_array($tables)  ? $tables  : [$tables];
    $columns = is_array($columns) ? $columns : [$columns];

    foreach ($tables as $key => $table) {
        $cols = explode(',', $columns[$key] ?? '');
        foreach ($cols as $column) {
            if ($loginsystem->getAmount($table, trim($column), $code) > 0) {
                return false;
            }
        }
    }
    return true;
}

function getCode(int $length = 32, array|string|null $tables = null, array|string|null $columns = null): string
{
    do {
        $code = generate($length);
    } while (!isvalid($code, $tables, $columns));
    return $code;
}

// ─── Verschlüsselung (AES-256-GCM, ersetzt mcrypt) ──────────────────────────
// HINWEIS: Der Schlüssel muss aus der config.inc.php kommen, NICHT hardcoded sein.
// Die Funktionen erwarten einen 32-Byte-Schlüssel (256 Bit).

function encrypt(string $str, ?string $key = null): string
{
    $key = $key ?? (defined('ENCRYPT_KEY') ? ENCRYPT_KEY : '');
    if (strlen($key) < 32) {
        $key = str_pad($key, 32, "\0");
    } else {
        $key = substr($key, 0, 32);
    }
    $iv         = random_bytes(12); // GCM standard: 96 bit IV
    $ciphertext = openssl_encrypt($str, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    return base64_encode($iv . $tag . $ciphertext);
}

function decrypt(string $str, ?string $key = null): string|false
{
    $key = $key ?? (defined('ENCRYPT_KEY') ? ENCRYPT_KEY : '');
    if (strlen($key) < 32) {
        $key = str_pad($key, 32, "\0");
    } else {
        $key = substr($key, 0, 32);
    }
    $data       = base64_decode($str, true);
    if ($data === false || strlen($data) < 28) {
        return false;
    }
    $iv         = substr($data, 0, 12);
    $tag        = substr($data, 12, 16);
    $ciphertext = substr($data, 28);
    $result     = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    return $result;
}

// ─── Cookie-Token (ersetzt encodeRand/decodeRand) ───────────────────────────
// HMAC-signiertes Token für "Remember me"-Cookies – verhindert Fälschung

function encodeRand(string $str, ?string $secret = null): string
{
    $secret ??= (defined('COOKIE_SECRET') ? COOKIE_SECRET : ENCRYPT_KEY);
    $hmac     = hash_hmac('sha256', $str, $secret);
    return base64_encode($str . '|' . $hmac);
}

function decodeRand(string $str, ?string $secret = null): string|false
{
    $secret  ??= (defined('COOKIE_SECRET') ? COOKIE_SECRET : ENCRYPT_KEY);
    $decoded   = base64_decode($str, true);
    if ($decoded === false) {
        return false;
    }
    $parts = explode('|', $decoded, 2);
    if (count($parts) !== 2) {
        return false;
    }
    [$value, $hmac] = $parts;
    $expected = hash_hmac('sha256', $value, $secret);
    if (!hash_equals($expected, $hmac)) {
        return false; // Manipuliert!
    }
    return $value;
}

// ─── Hilfsfunktionen ────────────────────────────────────────────────────────

function checker(mixed $a, mixed $b, int $c = 0): string
{
    $match = is_array($a) ? in_array($b, $a, true) : ($a == $b);
    if ($match) {
        return $c === 1 ? 'checked' : 'selected';
    }
    return '';
}

function delTree(string $dir): bool
{
    if ($dir === './' || $dir === '') {
        return false;
    }
    if (!is_dir($dir)) {
        return false;
    }
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = "$dir/$file";
        is_dir($path) ? delTree($path) : unlink($path);
    }
    return rmdir($dir);
}

function getFilePermission(string $file): string
{
    $perms  = fileperms($file);
    $length = strlen(decoct($perms)) - 3;
    return substr(decoct($perms), $length);
}

function return_bytes(string $val): int
{
    $val  = trim($val);
    $last = strtolower($val[-1] ?? '');
    $num  = (int)$val;
    return match ($last) {
        'g' => $num * 1024 * 1024 * 1024,
        'm' => $num * 1024 * 1024,
        'k' => $num * 1024,
        default => $num,
    };
}

function count_size(int $size): string
{
    if ($size < 1024)        return $size . ' Byte';
    if ($size < 1_048_576)   return str_replace('.', ',', (string)round($size / 1024, 2)) . ' KiB';
    if ($size < 1_073_741_824) return str_replace('.', ',', (string)round($size / 1_048_576, 2)) . ' MiB';
    return str_replace('.', ',', (string)round($size / 1_073_741_824, 2)) . ' GiB';
}

// ─── Datums- / Zeitfunktionen ────────────────────────────────────────────────

function timeformer(string $dom = 'd', int $timeset = 0, string $short = 'long'): string
{
    $monthnames = [
        '01' => ['long' => 'Januar',    'short' => 'Jan'],
        '02' => ['long' => 'Februar',   'short' => 'Feb'],
        '03' => ['long' => 'März',      'short' => 'Mär'],
        '04' => ['long' => 'April',     'short' => 'Apr'],
        '05' => ['long' => 'Mai',       'short' => 'Mai'],
        '06' => ['long' => 'Juni',      'short' => 'Jun'],
        '07' => ['long' => 'Juli',      'short' => 'Jul'],
        '08' => ['long' => 'August',    'short' => 'Aug'],
        '09' => ['long' => 'September', 'short' => 'Sep'],
        '10' => ['long' => 'Oktober',   'short' => 'Okt'],
        '11' => ['long' => 'November',  'short' => 'Nov'],
        '12' => ['long' => 'Dezember',  'short' => 'Dez'],
    ];
    $weekdays = [
        0 => ['long' => 'Sonntag',    'short' => 'So'],
        1 => ['long' => 'Montag',     'short' => 'Mo'],
        2 => ['long' => 'Dienstag',   'short' => 'Di'],
        3 => ['long' => 'Mittwoch',   'short' => 'Mi'],
        4 => ['long' => 'Donnerstag', 'short' => 'Do'],
        5 => ['long' => 'Freitag',    'short' => 'Fr'],
        6 => ['long' => 'Samstag',    'short' => 'Sa'],
    ];

    if ($dom === 'd') {
        return $weekdays[(int)date('w', $timeset)][$short] ?? '';
    }
    if ($dom === 'm') {
        return $monthnames[date('m', $timeset)][$short] ?? '';
    }
    return '';
}

function timeout(int $timestamp, string $short = 'long'): string
{
    $diff = time() - $timestamp;
    if ($diff <= 59)    return 'vor ' . $diff . ' Sek';
    if ($diff <= 3599)  return 'vor ' . (int)round($diff / 60) . ' Min';
    if ($diff <= 86399) return 'vor ' . (int)round($diff / 3600) . ' Std';
    if (date('d.m.Y', $timestamp) === date('d.m.Y', time() - 86399)) return 'Gestern';
    return date('d.', $timestamp) . ' ' . timeformer('m', $timestamp, $short);
}

function timediv(int $time): string
{
    if (date('d') === date('d', $time))             return 'Heute';
    if (date('d', time() - 86400) === date('d', $time)) return 'Gestern';
    return 'am ' . date('d.m.Y', $time);
}

// ─── Auto-Cleaner ────────────────────────────────────────────────────────────

function auto_remover(): void
{
    global $pdo, $loginsystem;

    $time  = strtotime('-14 days');
    $time2 = (int)$loginsystem->getMainData('cookielifetime');
    $time2 = time() - $time2;

    $stmt = $pdo->prepare(
        "DELETE FROM `" . Prefix . "_changes` WHERE (changed = '0' AND timestamp < ?) OR (changed != '0' AND changed < ?)"
    );
    $stmt->execute([$time, $time]);

    $stmt = $pdo->prepare(
        "DELETE FROM `" . Prefix . "_sessions` WHERE (closed = '1' AND last_action < ?) OR (closed = '0' AND logout = '0' AND last_action < ?)"
    );
    $stmt->execute([$time, $time2]);

    // Rate-Limit-Dateien löschen deren Block-Zeitfenster abgelaufen ist
    $rl_expiry = time() - 3600; // älter als 1 Stunde
    foreach (glob(__DIR__ . '/../tmp/rl_*.json') ?: [] as $rl_file) {
        if (@filemtime($rl_file) < $rl_expiry) {
            @unlink($rl_file);
        }
    }
}

// ─── Kontaktformular ─────────────────────────────────────────────────────────

function contact(): string
{
    global $loginsystem;

    if (defined('DEMO_MODE') && DEMO_MODE) {
        return 'In der DEMO nicht möglich!';
    }

    $name    = length($_POST['name']    ?? '', 64);
    $phone   = length($_POST['phone']   ?? '', 16);
    $email   = length($_POST['email']   ?? '', 64);
    $message = length($_POST['message'] ?? '', 2048);
    $captcha = length($_POST['captcha'] ?? '', 4);
    $dsgvo   = !empty($_POST['dsgvo']);

    if (empty($name) || empty($email) || empty($message) || empty($captcha) || !$dsgvo) {
        return 'Es müssen alle Pflichtfelder (*) ausgefüllt werden.';
    }
    if (!check_email($email)) {
        return 'Ungültige E-Mail Adresse!';
    }

    $captchaClass = new captcha();
    if (!$captchaClass->check_captcha($captcha)) {
        return 'Fehlerhaften Sicherheitscode eingegeben! Bitte versuche es erneut!';
    }

    $data            = [];
    $data['subject'] = 'Kontaktformular';
    $data['name']    = $name;
    $data['email']   = $email;
    $data['phone']   = $phone;
    $data['message'] = nl2br(htmlspecialchar($message));

    $loginsystem->sendMail(
        'contact.html',
        null,
        $data,
        $loginsystem->getMainData('mail_receiver'),
        $email
    );
    header('Location: ?p=contact&h=contact_success');
    exit();
}

// ─── HTTPS-Erkennung (direktes HTTPS + Reverse-Proxy via X-Forwarded-Proto) ──
// Funktioniert sowohl hinter Traefik als auch bei direktem HTTPS.

function is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    if (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
        return true;
    }
    if (($_SERVER['SERVER_PORT'] ?? 80) == 443) {
        return true;
    }
    return false;
}

// ─── Error-Mail (DSGVO: keine Stack-Traces an den Browser) ──────────────────

function csrf_field(): string
{
    if (empty($_SESSION['ml_csrfToken'])) {
        $_SESSION['ml_csrfToken'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf" value="' . htmlspecialchars($_SESSION['ml_csrfToken'], ENT_QUOTES, 'UTF-8') . '">';
}

function errormail(string $message): void
{
    global $loginsystem;
    // Interne Fehlermeldung nur per E-Mail, nie an den Browser
    $from    = htmlspecialchar((string)$loginsystem->getMainData('site_title'));
    $subject = 'Fehler auf deiner Homepage';
    $header  = "From: $from <" . $loginsystem->getMainData('mail_sender') . ">\r\n";
    $header .= "Mime-Version: 1.0\r\n";
    $header .= "Content-Type: text/html; charset=utf-8\r\n";
    $header .= "Content-Transfer-Encoding: quoted-printable\r\n";
    @mail((string)$loginsystem->getMainData('administrator_mail'), $subject, $message, $header);
}
