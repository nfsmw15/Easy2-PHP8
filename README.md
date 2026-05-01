# EASY 2.0 Loginsystem – Dashboard (PHP 8 Port)

> **Branch:** `loginsystem-php8`  
> **Basis:** EASY 2.0 Loginsystem 0.9.8.6 – Dashboard von [Marlight Systems](https://www.marlight-systems.de)  
> **PHP 8 Port:** [nfsmw15](https://github.com/nfsmw15)

---

## Über dieses Projekt

Dieses Repository enthält den PHP 8.x-kompatiblen Port des **EASY 2.0 Loginsystems** in der Dashboard-Variante (SB Admin Template, Bootstrap 4).

Das Original wurde von Marius Rasche (Marlight Systems) entwickelt und war auf PHP 7.x ausgelegt. Dieser Fork macht das System kompatibel mit **PHP 8.0 – 8.4** und behebt dabei alle veralteten oder entfernten Funktionen.

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

### Entfernte / ersetzte Funktionen

| Original (PHP 7) | Ersatz (PHP 8) | Datei |
|---|---|---|
| `mcrypt_encrypt()` / `mcrypt_decrypt()` | `openssl_encrypt()` / `openssl_decrypt()` (AES-256-GCM) | `system/functions.inc.php` |
| `each()` | `foreach` | `system/functions.inc.php` |
| `(double)microtime()` + `mt_srand` | `random_int()` | `system/functions.inc.php` |
| `encodeRand()` / `decodeRand()` (mt_rand XOR) | HMAC-SHA256 signiertes Base64-Token | `system/functions.inc.php` |
| `return_bytes()` Switch-Fallthrough | `match`-Ausdruck | `system/functions.inc.php` |
| `mysql_*` Funktionen | PDO mit Prepared Statements | `system/classes/database.php` |
| `create_function()` | Anonyme Funktionen | diverses |

### Sicherheitsverbesserungen

- `declare(strict_types=1)` in allen PHP-Dateien
- Security-HTTP-Header: `X-Content-Type-Options`, `X-Frame-Options`, `Content-Security-Policy`, `Referrer-Policy`, `Permissions-Policy`
- Session-Härtung: `cookie_httponly`, `cookie_samesite=Strict`, `use_strict_mode`, `cookie_secure` (HTTPS-aware)
- `display_errors=0` in Produktion
- CSRF-Token korrekt via `htmlspecialchars()` maskiert

### Asset-Änderungen

Das Original verwendete ein `vendor/`-Verzeichnis (nicht im Repository enthalten). Dieser Port nutzt:

- Bootstrap 4, jQuery, Font Awesome → **lokal** in `css/` und `js/`
- Chart.js → **CDN** (`cdnjs.cloudflare.com`)
- DataTables → **CDN** (`cdnjs.cloudflare.com`)
- SB Admin CSS/JS (`sb-admin.css`, `sb-admin.min.js`) → **lokal** aus Original übernommen

### Sonstige Fixes

- Alle `placehold.it` / `unsplash.it` Bild-URLs (externe Dienste offline) → inline SVG-Platzhalter
- Installer: MySQL-Benutzername-Limit von 16 auf 32 Zeichen erhöht
- Menü-Templates auf SB-Admin Accordion-Stil umgestellt (kein Bootstrap-Dropdown mehr)
- `content-wrapper` in allen Templates ergänzt (war im Original korrekt, ging beim Port verloren)

---

## Lizenz

Das ursprüngliche EASY 2.0 Loginsystem steht unter der Lizenz von Marlight Systems – siehe `LICENSE_EASY2.md`.

Die PHP 8 Modifikationen in diesem Fork stehen unter **AGPL-3.0-or-later** – siehe `LICENSE.md`.

---

## Credits

- **Original:** Marius Rasche (Marlight Systems) – [marlight-systems.de](https://www.marlight-systems.de)
- **PHP 8 Port:** [nfsmw15](https://github.com/nfsmw15)
- **SB Admin Template:** [Start Bootstrap](https://startbootstrap.com) (MIT)
- **Bootstrap 4:** [getbootstrap.com](https://getbootstrap.com) (MIT)
