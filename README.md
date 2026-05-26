# EASY 2.0 PHP 8 Fork

> **Fork von:** [EASY 2.0 Loginsystem 0.9.8.6](https://github.com/Marlight/Easy2-Modern_Business) von [Marlight Systems](https://www.marlight-systems.de)  
> **PHP 8 Port & Weiterentwicklung:** [nfsmw15](https://github.com/nfsmw15)  
> **Lizenz:** AGPL-3.0-or-later (Modifikationen) · Original: GPLv3

---

## Branches

| Branch | Bootstrap | Version | Besonderheiten |
|---|---|---|---|
| **`main-bs5`** *(default)* | 5.3.8 | 1.2.4 | Dark Mode, Vanilla JS, kein jQuery (außer Summernote) |
| `main-bs4` | 4.6.2 | 1.1.6 | jQuery 3.7.1 |
| `main-bs3` | 3.3.7 | 1.1.6 | jQuery 3.7.1 |
| `main-dashboard` | 4 / SB Admin | 1.1.6 | Admin-Dashboard-Layout |

---

## Features

- **Login-System** mit Registrierung, Passwort-zurücksetzen, Benutzer- und Rangverwaltung
- **Admin-Panel** mit Einstellungen, Menüverwaltung, Benutzerverwaltung, Newssystem
- **Dark Mode** (BS5): natives Bootstrap 5.3 `data-bs-theme`, Cookie-persistiert
- **OpenStreetMap** auf Kontaktseite (DSGVO-konform, kein Consent-Banner nötig)
- **PHPMailer 6.9.3** mit SMTP-Authentifizierung (STARTTLS/SSL, z.B. Mailcow)
- **Plugin-Loader**: `system/plugins/*/run.php`, `functions.php`, `classes.php` werden automatisch geladen
- **CSP-konform**: keine Inline-Event-Handler, `frame-src` für OSM-Iframe
- **Summernote WYSIWYG** für Impressum, Datenschutz und News

---

## Voraussetzungen

| Komponente | Mindestversion |
|---|---|
| PHP | 8.0 |
| MySQL / MariaDB | 5.7 / 10.3 |
| Webserver | Apache 2.4 / Nginx |

Benötigte PHP-Extensions: `pdo_mysql`, `gd`, `openssl`, `mbstring`, `session`

---

## Installation

1. Dateien auf den Webserver hochladen
2. `http://deine-domain.de/install/` aufrufen
3. Den Installer durchlaufen (Datenbankverbindung, Admin-Account)
4. Das `install/`-Verzeichnis wird automatisch nach der Installation gelöscht

---

## PHP 8 Änderungen gegenüber Original 0.9.8.6

| Original (PHP 7) | Ersatz (PHP 8) |
|---|---|
| `mcrypt_encrypt()` / `mcrypt_decrypt()` | `openssl_encrypt()` / `openssl_decrypt()` (AES-256-GCM) |
| `each()` | `foreach` |
| `mt_rand` XOR-Token | HMAC-SHA256 signiertes Base64-Token |
| `mysql_*` Funktionen | PDO mit Prepared Statements & Exceptions |
| `create_function()` | Anonyme Funktionen |

### Sicherheit

- `declare(strict_types=1)` in allen PHP-Dateien
- Security-HTTP-Header: `X-Content-Type-Options`, `X-Frame-Options`, `Content-Security-Policy`, `Referrer-Policy`, `Permissions-Policy`
- Session-Härtung: `cookie_httponly`, `cookie_samesite=Strict`, `use_strict_mode`, `cookie_secure` (HTTPS-aware)
- `display_errors=0` in Produktion

---

## Lizenz

Die PHP 8 Modifikationen in diesem Fork stehen unter **AGPL-3.0-or-later** – siehe [`LICENSE.md`](LICENSE.md).  
Das ursprüngliche EASY 2.0 Loginsystem steht unter GPLv3 – siehe [`LICENSE_EASY2.md`](LICENSE_EASY2.md).

---

## Credits

- **Original:** Marius Rasche (Marlight Systems) – [marlight-systems.de](https://www.marlight-systems.de)
- **PHP 8 Port:** [nfsmw15](https://github.com/nfsmw15)
- **Bootstrap 5:** [getbootstrap.com](https://getbootstrap.com) (MIT)
- **Font Awesome 7 Free:** [fontawesome.com](https://fontawesome.com) (Icons: CC BY 4.0, Fonts: SIL OFL 1.1)
- **PHPMailer:** [github.com/PHPMailer/PHPMailer](https://github.com/PHPMailer/PHPMailer) (LGPL-2.1)
- **Summernote:** [summernote.org](https://summernote.org) (MIT)
