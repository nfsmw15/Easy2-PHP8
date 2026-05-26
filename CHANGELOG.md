# Changelog – Branch `loginsystem-php8`

Basis: **EASY 2.0 Loginsystem 0.9.8.6 – Dashboard**  
PHP 8 Port von [nfsmw15](https://github.com/nfsmw15)

---

## [1.1.5] – 2026-05-26 – Sicherheits-Patches

### Neu
- **`EASY_BRANCH`-Konstante** (`'dashboard'`) in `functions.inc.php` eingeführt
- **Versionscheck branch-spezifisch**: `settings.php` filtert GitHub-Releases nach `-dashboard`-Suffix — kein falscher Update-Hinweis mehr durch BS5-Releases
- Pre-Releases (`prerelease: true`) werden übersprungen
- `per_page` 20 → 50 (robuster bei vielen Releases)
- `zipball_url` des Releases in `$_SESSION` gecacht (Vorbereitung Updater)
- `$error ?? ''` in `settings.php`: kein PHP-Warning mehr wenn kein Fehler vorliegt

### Sicherheit
- **XSS in Templates behoben**: `$_POST`-Werte in `value=""`-Attributen ohne HTML-Escaping in `regist.php`, `pwv.php`, `profil.php`, `menu.php`, `sites.php`, `rules.php`, `ranks.php` — alle Stellen durch `htmlspecialchar()` gesichert
- **E-Mail-Enumeration in `password_forget()` behoben**: Immer Redirect auf Success-Seite, unabhängig ob E-Mail-Adresse existiert — kein Informationsleck mehr
- **Mail-Template-Injection behoben**: `sendMail()` escaped alle `$data`-Werte per `htmlspecialchars()` bevor sie in HTML-Templates eingesetzt werden; Ausnahme: `message` (kommt bereits escaped aus `contact()`)
- **Avatar-Upload vollständig gehärtet** (`setUserAvatar()`):
  - MIME-Check via `finfo_file()` statt browser-geliefertem `$_FILES['type']` (war spoofbar)
  - Whitelist: nur `image/jpeg`, `image/png`, `image/gif`, `image/webp` erlaubt
  - GD-Re-Encoding: Bild wird neu gerendert → eingebetteter Code wird vernichtet
  - Altes Bild wird erst nach erfolgreichem Speichern gelöscht
  - `avatare/.htaccess`: PHP-Ausführung im Upload-Verzeichnis blockiert (`php_flag engine off`, `SetHandler default-handler`)
- **Mitglied-Rang: Logout/Profil-Menü fehlte** — Menu-IDs `m5` (Mein Profil), `m6` (Sperren), `m7` (Abmelden) in `_ml_ranks.sql` (Mitglied) ergänzt; Migration für bestehende Installs:
  ```sql
  UPDATE `[prefix]_ml_ranks`
  SET `sites` = CONCAT(`sites`, ',m5,m6,m7')
  WHERE `id` = 1813201540 AND `sites` NOT LIKE '%m7%';
  ```
- **CSRF auf GET-Aktionen behoben**: `activateUser()`, `deactivateUser()`, `moveRank()`, `setSpecialRank()` prüfen jetzt `$_GET['csrf']` gegen `$_SESSION['ml_csrfToken']` (gleicher Mechanismus wie `lock()`); Templates `userlist.php` und `getRankList()` hängen Token an betroffene Links an
- **SQL-Injection vollständig behoben**: Alle `$this->mysql->query()` in `loginsystem.php`, `sites.php`, `menu.php`, `additional_fields.php` und `rules.php` auf `$this->pq()` (Prepared Statements) umgestellt
  - `database.php`: neue geschützte Methode `pq(string $sql, array $params)` als sicherer Drop-in-Ersatz
  - `getUser()`: Whitelist für `$search_column` (nur erlaubte Spalten)
  - `restore()`: Whitelist für `$row['coloum']` (verhindert Column-Injection aus DB-Werten)
  - `autoPosition()` in `menu.php` und `additional_fields.php`: dynamische Query-Konstruktion durch Prepared Statements ersetzt
- **CSRF-Token**: `uniqid()` → `bin2hex(random_bytes(32))` (kryptografisch sicher); `lock()` CSRF-Länge 16 → 64 (konsistent mit Session)
- **Session-Härtung**: `session_regenerate_id(true)` nach erfolgreichem Login
- **Remember-Me-Cookies**: Cookie-Optionen auf `secure`, `httponly`, `samesite=Strict` gehärtet (Array-Syntax)
- **Passwort-Vergleiche**: `md5($a) == md5($b)` → `$a === $b` (kein Hash-Vergleich mehr, kein Typ-Juggling)
- **Mitglied-Rang: `?p=menu` und `?p=additional_fields` nicht mehr zugänglich** — Site-IDs 18 und 19 aus `_ml_ranks.sql` (Mitglied) entfernt; Migration für bestehende Installs:
  ```sql
  UPDATE `[prefix]_ml_ranks`
  SET `sites` = REPLACE(REPLACE(`sites`, ',18', ''), ',19', '')
  WHERE `id` = 1813201540;
  ```

---

## [1.1.4] – 2026-05-26 – OpenStreetMap & CSP-Fixes

### Neu
- `contact.php`: Google Maps durch **OpenStreetMap** ersetzt (DSGVO-konform, kein Tracking, kein Consent-Banner nötig)
- `settings.php`: OSM Embed-URL im Admin-Bereich konfigurierbar (`?p=settings` → "Karte")
- `loginsystem.php`: `osm_embed_url` wird gespeichert; `html_entity_decode()` normalisiert `&amp;` → `&` vor DB-Speicherung
- `install/sql/_ml_main.sql`: `osm_embed_url` als Standardwert hinterlegt
- `index.php`: `frame-src https://www.openstreetmap.org` in Content-Security-Policy ergänzt
- `js/app.js`: neu erstellt — jQuery-Handler für `.captcha-img` (Refresh) und `[data-history-back]` (Browser-Back)
- `index.php`: `app.js` nach `sb-admin.min.js` eingebunden

### Bugfixes (CSP)
- `userlist.php`: 4× `onClick="window.history.back();"` → `data-history-back`-Attribut (CSP-Verletzung behoben)
- `regist.php`, `pw_reset.php`, `pwv.php`: Captcha-`onClick` entfernt (CSP-Verletzung behoben)

---

## [1.1.3] – 2026-05-24 – Plugin-Loader Erweiterung

### Neu
- `functions.user.php` → Plugin-Loader: lädt automatisch alle `system/plugins/*/functions.php`
- `classes.run.user.php` → Plugin-Loader: lädt automatisch alle `system/plugins/*/classes.php`
- `EASY_WEBROOT`-Konstante nach `functions.user.php` verschoben — in allen Loader-Stufen verfügbar

---

## [1.1.2] – 2026-05-24 – Bugfixes

### Bugfixes
- Gast-Rang: Site 18 (Menüverwaltung) aus den Standard-Berechtigungen entfernt (`install/sql/_ml_ranks.sql`)

---

## [1.1.1] – 2026-05 – Plugin-Loader

### Plugin-System (`system/`)
- `system/run.user.php` → Plugin-Loader: lädt automatisch alle `system/plugins/*/run.php`
- `system/classes.run.user.php` geleert — Plugin-Initialisierung liegt jetzt in der jeweiligen `run.php`
- `system/plugins/` Verzeichnis angelegt (via `.gitkeep`)
- Konstante `EASY_WEBROOT` definiert (Pfad zum Webroot, nutzbar in Plugins)
- Fehler in Plugins werden per `\Throwable`-catch isoliert — kein Plugin-Fehler zerstört die Seite

---

## [1.1.0] – 2026-05 – Asset-Lokalisierung & Bugfixes

### Assets (`index.php`, `css/`, `js/`)
- Chart.js 3.9.1 (CDN) → Chart.js 2.9.4 (lokal) — behebt `Chart.defaults.global`-Fehler in `sb-admin-charts.min.js`
- DataTables 1.10.21 + Bootstrap4-Plugin + CSS → lokal
- Cloudflare CDN komplett entfernt; CSP-Header angepasst

### Inline-Script (`js/app.js`)
- `$('.carousel').carousel({interval:5000})` aus inline `<script>` → `js/app.js` (CSP-Compliance)

### Sicherheit (`system/.htaccess`)
- `system/.htaccess`: `deny from all` + Ausnahme für `placeholder.php`

### Platzhalter-Bilder (`system/placeholder.php`)
- Lokaler PHP/GD-Bildgenerator ersetzt offline-Dienste `placehold.it` / `unsplash.it`

---

## [1.0.0] – 2026-05 – Initiale PHP 8 Portierung

### PHP 8 Kompatibilität (`system/functions.inc.php`)
- `mcrypt_encrypt/decrypt` → `openssl_encrypt/decrypt` (AES-256-GCM)
- `each()` → `foreach` (in PHP 8 entfernt)
- `generate()`: `(double)microtime()` + `mt_srand` → `random_int()` (kryptografisch sicher)
- `encodeRand/decodeRand`: unsicheres mt_rand-XOR → HMAC-SHA256 Base64-Token
- `return_bytes()`: Switch-Fallthrough → `match`-Ausdruck
- `printarray/printobject` entfernt (Debug-Werkzeuge ohne Produktionswert)
- `declare(strict_types=1)` ergänzt

### Sicherheit (`index.php`)
- Security-HTTP-Header: `X-Content-Type-Options`, `X-Frame-Options`, `X-XSS-Protection`, `Referrer-Policy`, `Content-Security-Policy`, `Permissions-Policy`
- Session-Härtung: `cookie_httponly=1`, `cookie_samesite=Strict`, `use_strict_mode=1`, `use_only_cookies=1`, `cookie_secure` dynamisch (HTTPS-aware)
- `display_errors=0` (Produktionsstandard)
- CSRF-Token in Logout-Link korrekt via `htmlspecialchars()` maskiert

### Assets (`index.php`, `css/`, `js/`)
- `vendor/`-Abhängigkeit aufgelöst (war nicht im Repository enthalten)
- Bootstrap 4, jQuery, Font Awesome → lokal
- SB Admin CSS/JS aus Original übernommen → lokal
- Chart.js → CDN (`cdnjs.cloudflare.com/Chart.js/3.9.1`)
- DataTables → CDN (`cdnjs.cloudflare.com/datatables/1.10.21`)
- CSP-Header erlaubt `cdnjs.cloudflare.com`

### Templates (`templates/`)
- `content-wrapper` in allen Templates ergänzt (Layout-Fix für SB Admin Sidebar)
- Menü-Templates (`system/tpl/menu/default/`) auf SB-Admin Accordion-Stil umgestellt:
  - `menu_dropdown_point.tpl`: Bootstrap-Dropdown → `sidenav-second-level collapse`
  - `menu_point.tpl`: `[title]` in `<span class="nav-link-text">` gewrapped (Sidebar-Toggle)
- Alle `placehold.it` und `unsplash.it` URLs (externe Dienste offline) → dimensionsgenaue inline SVG-Platzhalter
- `bootstrap/contact.php`: englische Demo-Kontaktdaten → deutsche Platzhalter

### CSS (`css/mlsystems.css`)
- Fallback-Hintergrundfarben für Carousel-Slides (ohne externe Bild-URLs)
- `scroll-to-top` Button: `top: auto` fix
- SB Admin Accordion Untermenü: Textfarbe, Hintergrund, Padding explizit gesetzt
- `nav-link-collapse:after` Pfeil-Icon via FontAwesome

### Installer (`install/`)
- MySQL-Benutzername: Limit von `{4,16}` auf `{1,32}` erhöht (MySQL/MariaDB Standard)
- HTML `maxlength="16"` → `maxlength="32"` im Formularfeld
