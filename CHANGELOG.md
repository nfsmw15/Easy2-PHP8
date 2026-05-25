# Changelog – Branch `loginsystem-php8`

Basis: **EASY 2.0 Loginsystem 0.9.8.6 – Dashboard**  
PHP 8 Port von [nfsmw15](https://github.com/nfsmw15)

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
- `regist.php`: Captcha-`onClick` entfernt (CSP-Verletzung behoben)

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
