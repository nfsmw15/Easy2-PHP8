# Changelog – Branch `loginsystem-php8`

Basis: **EASY 2.0 Loginsystem 0.9.8.6 – Dashboard**  
PHP 8 Port von [nfsmw15](https://github.com/nfsmw15)

---

## [1.2.0] – 2026-05-30 – Sicherheits-Patches

### Sicherheit
- **`check_filename()` gehärtet**: Dotfiles, mehrfache Extensions, Pfad-Bestandteile blockiert
- **Kryptografisch sichere Zufallsfunktionen**: `generatePassword()` und Captcha nutzen `random_int()` + Fisher-Yates
- **Captcha**: Klartext in Session, `hash_equals()`, einmalige Verwendung
- **Sites / Additional Fields**: zustandsändernde Aktionen nur noch per POST+CSRF
- **`pw_reset` Token-Verbrauch**: Token nach Reset sofort gelöscht; Formular nur bei gültigem Token; Sessions nach Reset geschlossen
- **Lock/Unlock**: `is_locked()` PDO-Typenvergleich fix; `csrf_field()` im Unlock-Formular; `unlock` POST-only
- **`errormail()` nutzt PHPMailer** statt direktem `@mail()`

### Behoben
- `site_add_successfully_upl_error` ohne Upload (PHP null == 0 Typenvergleich)
- `downloadSite()` MIME-Type: `filetype()` → `finfo(FILEINFO_MIME_TYPE)`
- `pw_reset` für Gäste: Site-ID 20 in Gast-Rang ergänzt
- Captcha `random_int()` ValueError bei min > max

### Abhängigkeiten
- **PHPMailer 6.9.3 → 7.1.1**

---

## [1.1.8] – 2026-05-27 – Sicherheits-Patches

### Sicherheit
- **XSS-Escaping (`e()`)**: Neue Hilfsfunktion `e()` in `functions.inc.php` (`htmlspecialchars` mit `ENT_QUOTES|ENT_SUBSTITUTE`) — alle Benutzer- und DB-Werte in Admin-Templates (`settings.php`, `additional_fields.php`), Login-Templates und Zusatzfelder-Ausgabe über `showFields()` konsequent escaped
- **CSRF-Schutz für Gäste**: `csrf_field()` generiert Token nun lazy (auch ohne bestehende Session); `run.inc.php` prüft CSRF auch für öffentliche Formulare (`regist`, `pwv`, `pw_reset`, `contact`) — Token in `regist.php`, `pwv.php`, `pw_reset.php` ergänzt
- **Menü-URL-Sanitierung**: Neue Methode `sanitize_menu_url()` in `menu.php` — blockiert `javascript:`, `data:`, `vbscript:`, `file:` und unbekannte Schemata; relative URLs und `http/https/mailto` erlaubt; alle drei URL-Render-Stellen in `getMenu()`/`getUnderMenu()` abgesichert
- **HTTP 404 bei Fehlerseiten**: `includeSite()` in `sites.php` setzt `http_response_code(404)` wenn Error-/Fallback-Seite ausgeliefert wird
- **SMTP-Passwort-Guard**: Leeres `smtp_pass`-Feld in Einstellungen überschreibt das gespeicherte Passwort nicht mehr; Passwort-Feld zeigt `value=""` mit Hinweis-Placeholder
- **PWV-Fehlermeldung präzisiert**: "Bitte gebe eine gültige E-Mail-Adresse ein!" statt allgemeiner Meldung

---

## [1.1.7] – 2026-05-27 – Sicherheits-Patches

### Sicherheit
- **Webmaster-Bestätigung vor dem Speichern (Pre-Save)**: `createUser()` und `editUser()` unterbrechen die Speicheraktion mit Rückgabe `__webmaster_confirm__` wenn Webmaster-Rang gewählt und bereits ein Webmaster existiert — Formular wird neu gerendert mit Warnbox sowie "Trotzdem anlegen/speichern"- und "Abbrechen"-Buttons; erst nach Bestätigung via `confirm_webmaster`-Feld wird der Account gespeichert (zuvor wurde der Account sofort angelegt)
- **Passwort und Checkbox-State beim Bestätigungsschritt erhalten**: Passwörter werden als versteckte Felder durchgereicht (sichtbare Felder `disabled`), "Anmeldedaten versenden"-Haken bleibt gesetzt

### Behoben
- Tippfehler: "erfolgreichgel&ouml;scht" → "erfolgreich gel&ouml;scht"

---

## [1.1.6] – 2026-05-26 – Sicherheits-Patches

### Sicherheit
- **Operator-Precedence-Bug in `encrypt()`/`decrypt()` behoben**: `$key ?? defined('ENCRYPT_KEY') ? ENCRYPT_KEY : ''` wurde als `($key ?? defined('ENCRYPT_KEY')) ? ENCRYPT_KEY : ''` ausgewertet (PHP-Priorität: `??` > `?:`) — bei übergebenem Key wurde stattdessen immer `ENCRYPT_KEY` verwendet; Klammern ergänzt
- **Gleiches Problem in `encodeRand()`/`decodeRand()`** für `$secret ??= ...` behoben
- **HTTPS-Erkennung hinter Reverse-Proxy gehärtet**:
  - Neue Funktion `is_https()` in `functions.inc.php`: prüft `$_SERVER['HTTPS']`, `$_SERVER['HTTP_X_FORWARDED_PROTO']` (Traefik) und Port 443 — funktioniert sowohl hinter Traefik als auch bei direktem HTTPS
  - `index.php`: Session-Cookie `Secure`-Flag berücksichtigt jetzt `X-Forwarded-Proto: https` (war zuvor immer `0` hinter Traefik)
  - `loginsystem.php`: Remember-Me-Cookies nutzen `is_https()` statt nackter `$_SERVER['HTTPS']`-Prüfung
  - `loginsystem.php` `logout()`: Cookie-Löschung von 7-Argument-Signatur auf Array-Syntax umgestellt (konsistent mit Setzen; `Secure`+`SameSite` korrekt übergeben)
  - `getCurrentUrl()`: nutzt `is_https()` statt `$_SERVER['HTTPS']` — Passwort-Reset-/Kontaktlinks in E-Mails haben jetzt auch hinter Traefik korrekte `https://`-Scheme
  - `loginsystem.php` `session_data()`: hardcoded `http://` durch `is_https()`-Erkennung ersetzt — Redirect-URL wird korrekt mit `https://` gebaut
  - `decodeRand()`: fehlende Klammern ergänzt (war letzter verbliebener `??=`-Precedence-Bug)
- **Privilege-Escalation in Benutzerverwaltung behoben**: `editUser()`, `resetUserPasswd()`, `activateUser()`, `rmUserAvatar()` prüfen jetzt `checkRank()` des Zielusers — ein Administrator konnte bisher Benutzer mit höherem Rang (z.B. Webmaster) bearbeiten, Passwort zurücksetzen und Avatar entfernen
- **Edit-Button in Userliste**: fehlender `checkRank()`-Check ergänzt — Bearbeiten-Icon wird für höherrangige Benutzer nicht mehr angezeigt
- **Gast-Rang: `adm/`-Seiten gesperrt** (`sites.php`): `allowSite()` blockiert jetzt alle Seiten mit `dir='adm/'` ohne aktiven Login, unabhängig von DB-Einstellungen
- **Selbstlöschung im Admin-Panel gesperrt**: Löschen-Icon und Lösch-Formular werden für den eigenen Account nicht mehr angezeigt (`listAllUsers()`, `userlist.php`) — Backend-Schutz war bereits vorhanden
- **Webmaster-Selbstlöschung über Profil gesperrt**: `removeUserSelf()` prüft `pos=0`-Rang und blockiert; Konto-löschen-Button und `remove_self`-Formular in `profil.php` via `isTopRank()` ausgeblendet
- **Warnung bei mehreren Webmaster-Accounts**: `newUser()` und `editUser()` erkennen wenn mehr als ein Webmaster-Account existiert und leiten zu `h=multiple_webmaster_warning` um — gelber Alert mit Liste der Risiken (gegenseitiges Löschen/Aussperren, neue Webmaster anlegen) und "Ich habe verstanden"-Button

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
- **Mitglied-Rang: Berechtigungen korrigiert** — Site-IDs 18 (`?p=menu`) und 19 (`?p=additional_fields`) entfernt; Menu-IDs `m5` (Mein Profil), `m6` (Sperren), `m7` (Abmelden) ergänzt; Migration für bestehende Installs:
  ```sql
  UPDATE `[prefix]_ml_ranks`
  SET `sites` = CONCAT(REPLACE(REPLACE(`sites`, ',18', ''), ',19', ''), ',m5,m6,m7')
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
- **CSP gehärtet**: `unsafe-eval` aus `script-src` entfernt — kein Code nutzte `eval()` oder `new Function()`; `unsafe-inline` in `style-src` bleibt (statische `style=""`-Attribute in Templates und Summernote-Editor)
- **CSRF auf alle POST-Formulare ausgeweitet**:
  - `functions.inc.php`: neue Helper-Funktion `csrf_field()` — gibt `<input type="hidden" name="csrf">` aus (leer wenn nicht eingeloggt)
  - `run.inc.php`: `cookielogin()` läuft jetzt vor dem Guard (Session vollständig aufgebaut); Guard prüft `$_POST['csrf']` via `hash_equals()`; öffentliche Aktionen (`login`, `regist`, `pwv/send`, `pw_reset/reset`) explizit whitelisted
  - 28 `csrf_field()`-Aufrufe in 9 Templates: `settings.php`, `ranks.php`, `rules.php`, `sites.php`, `menu.php`, `additional_fields.php`, `userlist.php`, `profil.php`, `contact.php`
- **Default-Avatar-Upload gehärtet** (`mainSettings()`): gleicher Codepfad wie `setUserAvatar()` — `finfo` MIME-Check, GD-Re-Encoding, Whitelist `jpeg/png/gif/webp`; altes Bild wird bei Typ-Wechsel gelöscht

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
