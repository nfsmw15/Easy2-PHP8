# Changelog — EASY 2.0 PHP8 Fork (Bootstrap 5)

## [1.2.4] — 2026-05-26

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

---

## [1.2.3] — 2026-05-26

### Neu
- **`EASY_BRANCH`-Konstante** in `functions.inc.php` eingeführt (Wert je Branch: `bs5`, `bs4`, `bs3`, `dashboard`)
- **Versionscheck branch-spezifisch**: `settings.php` filtert GitHub-Releases nach Branch-Suffix (`-bs5` usw.) statt immer `releases/latest` zu nehmen — kein falscher Update-Hinweis mehr
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

## [1.2.2] — 2026-05-26

### Neu
- **Dark Mode**: Native Bootstrap 5.3 `data-bs-theme`-Umschaltung; Präferenz in Cookie gespeichert (seitenübergreifend persistent)
- **Breadcrumb**: BS5.3-konformer `bg-body-tertiary`-Wrapper in allen 26 Templates ergänzt
- `contact.php`: Google Maps durch **OpenStreetMap** ersetzt (DSGVO-konform, kein Tracking, kein Consent-Banner nötig)
- `settings.php`: OSM Embed-URL im Admin-Bereich konfigurierbar (`?p=settings` → "Karte")
- `loginsystem.php`: `osm_embed_url` wird gespeichert; `html_entity_decode()` normalisiert `&amp;` → `&` vor DB-Speicherung (OSM Share-Dialog liefert HTML-kodierte URLs)
- `install/sql/_ml_main.sql`: `osm_embed_url` als Standardwert hinterlegt
- `header_navbar.php`: Dark-Mode-Toggle-Button in Navbar

### Bugfixes (CSP)
- `index.php`: Inline-Script für Dark Mode → PHP-Cookie-Auslesen server-seitig (CSP blockiert Inline-Scripts in Firefox)
- `index.php`: `frame-src https://www.openstreetmap.org` in Content-Security-Policy ergänzt (OSM-Iframe)
- `userlist.php`: 4× `onClick="window.history.back();"` → `data-history-back`-Attribut
- `contact.php`, `regist.php`, `pwv.php`, `pw_reset.php`: Captcha-`onClick` entfernt
- `app.js`: Vanilla-JS-Handler für `.captcha-img`, `[data-history-back]` und Dark-Mode-Toggle

---

## [1.2.1] — 2026-05-25

### Verbesserungen
- Font Awesome **4.5.0 → 7.2.0 Free** (webfonts-only, kein less/scss mehr)
  - `v4-shims.min.css` eingebunden — alle bestehenden `fa fa-*` Klassen bleiben kompatibel
  - `v4-compatibility.woff2` enthält die Glyphen für den Shim
- `home.php`: Outdated Features-Liste (Bootstrap v4, jQuery) durch EASY 2.0 / BS5-Beschreibung ersetzt
- `install/index.php`: Font-Awesome-Pfad auf FA 7.2.0-Struktur aktualisiert
- Portfolio-Templates: `sr-only` → `visually-hidden` (BS5-konforme Accessibility-Klasse)

### Bootstrap 5 Kompatibilität (vollständige Bereinigung)
- `btn-block` → `w-100` in allen Templates und Installer-Schritten (Klasse in BS5 entfernt)
- `input-group-addon` → `input-group-text` in `settings.php`, `additional_fields.php`, `step3.php`, `step5.php`
- `float-right` → `float-end` in `menu.php`, `sites.php`, `step2.php`, `step4.php`
- `input-group-btn`-Wrapper entfernt — Buttons liegen direkt im `input-group` (BS5-konform)
- `jumbotron` → `p-5 bg-body-tertiary rounded-3` in `404.php` (Klasse in BS5 entfernt)
- `media` / `media-body` → `d-flex` / `flex-shrink-0` / `flex-grow-1` in `blog-post.php` (Komponente in BS5 entfernt)
- `data-dismiss` → `data-bs-dismiss`, `class="close"` → `class="btn-close"` in `install/index.php`
- `data-parent` → `data-bs-parent` in `faq.php` (Accordion)

---

## [1.2.0] — 2026-05-25

### Bootstrap 5 Migration (main-bs5)
- Bootstrap 4.6.2 → **Bootstrap 5.3.8** (CSS + JS Bundle, kein separates Popper.js nötig)
- jQuery entfernt — Bootstrap 5 ist Vanilla JS; jQuery bleibt nur für Summernote geladen (nur auf settings/news_add-Seiten)
- Summernote BS4-Build → **Summernote 0.9.1 BS5-Build**
- `modern-business.css` entfernt — durch BS5-Utilities und `mlsystems.css` ersetzt
- `mlsystems.css` bereinigt: BS3-Dropdown-Overrides und btn-xs-Hacks entfernt (BS5 hat das nativ)
- Navbar: `data-toggle` → `data-bs-toggle`, `data-target` → `data-bs-target`, `ml-auto` → `ms-auto`
- Templates: BS4-Klassen migriert (`ml-*` → `ms-*`, `mr-*` → `me-*`, `float-right` → `float-end` etc.)
- `js/app.js`: Carousel-Initialisierung von jQuery auf Vanilla JS (`bootstrap.Carousel`) umgestellt
- `BS_VERSION`-Konstante auf `5` gesetzt
- IE-Kompatibilitäts-Meta (`X-UA-Compatible`) entfernt (BS5 unterstützt kein IE)

---

## [1.1.3] — 2026-05-24

### Neu
- `functions.user.php` → Plugin-Loader: lädt automatisch alle `system/plugins/*/functions.php`
- `classes.run.user.php` → Plugin-Loader: lädt automatisch alle `system/plugins/*/classes.php`
- `EASY_WEBROOT`-Konstante nach `functions.user.php` verschoben — in allen Loader-Stufen verfügbar

---

## [1.1.2] — 2026-05-24

### Neu
- Plugin-Loader: `system/run.user.php` lädt automatisch alle `system/plugins/*/run.php`
- `system/classes.run.user.php` geleert — Plugin-Initialisierung liegt in der jeweiligen `run.php`
- `system/plugins/` Verzeichnis angelegt
- Konstante `EASY_WEBROOT` definiert (Pfad zum Webroot, nutzbar in Plugins)
- Fehler in Plugins werden per `\Throwable`-catch isoliert — kein Plugin-Fehler zerstört die Seite
- `js/app.js` — Inline-Carousel-Script aus `index.php` ausgelagert (CSP-Compliance)

### Bugfixes
- `system/.htaccess`: `placeholder.php` explizit vom `deny from all` ausgenommen
- Gast-Rang: Site 18 (Menüverwaltung) aus den Standard-Berechtigungen entfernt (`install/sql/_ml_ranks.sql`)

---

## [1.1.1] — 2026-04-19

### Neu
- `BS_VERSION`-Konstante in `config.inc.php` definiert (Wert: `4`)
- Summernote-Editor wird jetzt auch auf der `news_add`-Seite geladen

### Bugfixes
- Summernote BS4-Build (`summernote-bs4.min.js`) statt BS3-Build verwendet
- jQuery 1.11.1 → **3.7.1** (Pflicht für Bootstrap 4 und Summernote BS4)
- Bootstrap 4.6.2 **Bundle** (inkl. Popper.js) ersetzt standalone-Version
- CSP-Header um `unsafe-eval` ergänzt (Summernote wurde blockiert)
- Doppelte Summernote-Einbindung in `settings.php` entfernt

---

## [1.1.0] — 2026-04-19

### Neu: Bootstrap 4 Branch (main-bs4)
- Neuer Branch `main-bs4` mit Bootstrap 4.6.2 und Start Bootstrap Modern Business BS4
- Bootstrap 3.3.7 durch Bootstrap 4.6.2 ersetzt (CSS + JS)
- Navbar auf BS4-Struktur umgestellt (`navbar-dark bg-dark fixed-top`, `navbar-toggler`)
- BS3-Klassen migriert: `pull-right/left` → `float-right/left`, `btn-xs` → `btn-sm`, `btn-md` entfernt, `panel` → `card`
- Glyphicons durch Font Awesome ersetzt
- Installer-Breadcrumb auf volle Breite umgestellt und `breadcrumb-item` ergänzt
- Cookie-Hinweis bei "Eingeloggt bleiben" Checkbox hinzugefügt

### SMTP-Mailversand via PHPMailer
- **PHPMailer 6.9.3** eingebunden (ersetzt PHP `mail()`)
- SMTP-Einstellungen im Admin-Panel: Host, Port, Benutzer, Passwort, Verschlüsselung (STARTTLS/SSL)
- Neue Datenbankfelder: `smtp_host`, `smtp_port`, `smtp_user`, `smtp_pass`, `smtp_encryption`
- `sendMail()` vollständig auf PHPMailer/SMTP umgestellt
- Kompatibel mit Mailcow und anderen externen Mailservern

### Bugfixes
- `system/fonts/Captureit.ttf` fehlte — Captcha-Bild wird jetzt korrekt generiert
- `emailtpl/`-Verzeichnis fehlte im BS4-Branch — Kontaktformular und System-Mails funktionieren jetzt
- `ini_set()` Session-Warnungen behoben: Guard `session_status() === PHP_SESSION_NONE` in `config.inc.php`
- MySQL/MariaDB Versionscheck in Installer `mysqlConnection()` ergänzt — zu alte Versionen werden abgelehnt
- DB-Version wird nach erfolgreichem Connect in Step 2.2 angezeigt

## [1.0.0] — 2026-04-17

### Fork-Basis
- Fork von **EASY 2.0 Loginsystem** (GPLv3) von Marius Rasche / Marlight Systems
- Lizenz geändert zu **GNU Affero General Public License v3.0 (AGPLv3)**

### PHP 8 Migration
- `mysqli` vollständig durch **PDO mit Exceptions** ersetzt
- Alle Klassen auf PHP 8 Typisierung umgestellt (`declare(strict_types=1)`)
- `short_open_tag`-Abhängigkeit entfernt
- Deprecated-Funktionen ersetzt

### Sicherheit
- Session-Härtung: `HttpOnly`, `Secure` (HTTPS-aware), `SameSite=Strict`, `use_strict_mode`
- Sicherheitsheader: `X-Content-Type-Options`, `X-Frame-Options: DENY`, `Cache-Control: no-store`
- CSP-Header hinzugefügt
- AES-256-GCM Verschlüsselung (ersetzt alte Methode)
- CSRF-Token-Schutz verbessert
- Passwort-Hashing mit `password_hash()` / `password_verify()`
- Path-Traversal-Schutz in Datei-Operationen

### Installer
- `dirname()`-Bug behoben (Schreibrechte-Check zeigte falschen Pfad)
- Step 4 invertierte Bedingung (`is_user()`) behoben
- PHP-Versionscheck auf 8.0 aktualisiert
- `short_open_tag`-Zeile entfernt
- `pdo_mysql`-Extension-Check hinzugefügt
- MySQL/MariaDB-Version wird live aus PDO angezeigt
- Marlight-Nutzungsbedingungen durch **AGPLv3-Lizenztext** ersetzt
- Marlight-Supportlinks entfernt, GitHub-Link hinzugefügt
- Tote Menüeinträge entfernt (Tables, Charts, Cards — Seiten existierten nie)

### Templates & UI
- Neue Seiten: **Impressum**, **Datenschutz**
- Header aller Templates vereinheitlicht (`mt-4 mb-3`, Font Awesome Icons, Breadcrumb)
- `contact.php` überarbeitet
- `404.php` überarbeitet
- **Summernote WYSIWYG Editor** für Impressum und Datenschutzerklärung in Admin-Einstellungen integriert
- Cookie-Notice auf Login-Seite hinzugefügt
- Font-Dateien für Summernote und Bootstrap Glyphicons optimiert (`css/font/`)

### Admin-Panel
- Bearbeitbare Felder für Impressum und Datenschutzerklärung mit Rich-Text-Editor
- HTML-Export möglich für beide Seiten

### Konfiguration
- `session.cookie_secure` wird nur gesetzt wenn noch keine Session aktiv (`session_status()` Check)
- `EASY_VERSION` auf `1.0.0` gesetzt
- `config.example.inc.php` als Vorlage mit Platzhaltern hinzugefügt

### Externe Links
- GitHub-URLs durch **nfsmw15.de** ersetzt

---

# Ursprüngliches EASY 2.0 Loginsystem — Versionshistorie bis 0.9.8.6

*Diese Fork basiert auf EASY 2.0 von Marius Rasche / Marlight Systems (ursprüngliche Lizenz: GPLv3). Die folgende Versionshistorie dokumentiert die Entwicklung des ursprünglichen Projekts bis Version 0.9.8.6.*

## [0.9.8.6]

- **Gefixt**: In der Dashboard Version haben die Berechtigungen für normale Mitglieder nach der Installation nicht gestimmt
- **Gefixt**: Die DSGVO E-Mail-Adresse wurde als default Wert entfernt bei der Installation

## [0.9.8.5]

- **Verbessert**: Die Höhe der Multiselects wurde erhöht für bessere Bedienbarkeit
- **Gefixt**: Mit PHP8 sind ein paar alte Features von PHP deprecated gesetzt worden
- **Gefixt**: Ein Fehler der erst mit PHP8 auftritt, verhindert das nutzen der Rangverwaltung
- ⚠️ Für dieses Update mussten folgende Dateien angepasst werden: `loginsystem.php`, `functions.inc.php`, `ranks.php`, `mlsystems.css`

## [0.9.8.4]

- **Gefixt**: Es kam ein Hinweis, wenn die Fehlerausgabe aktiviert ist, wenn überprüft wurde ob der Benutzer gerade die Oberfläche gesperrt hat
- **Gefixt**: Registrierung funktionierte nicht mehr bei Nutzung eines MySQL Servers (nicht bei MariaDB)
- ⚠️ Update erforderte Änderung in `loginsystem.php`

## [0.9.8.3]

- **Gefixt**: Bei der PHP-Version 7.2 und höher konnte es dazu kommen, dass das Captcha nicht generiert wurde
- **Gefixt**: Bei der Bootstrap 3 Version, fehlte der Haken für die Akzeptierung der Datenschutzbestimmungen
- ⚠️ Update erforderte Änderung in `captcha.php` und `regist.php`

## [0.9.8.2]

- **Neuerung**: Die Funktion `getValue()` kann jetzt auch mehrere Spalten zurückgeben
- **Gefixt**: Die Funktion `getValue()` gab keine Werte zurück, wenn die Parameter 2-4 als NULL angegeben waren
- ⚠️ Update erforderte Änderung in `database.php`

## [0.9.8.1] — HOTFIX

- **Gefixt**: Benutzer mit einem niedrigerem Rang konnten keine Benutzer mit einem höheren Rang mehr deaktivieren oder löschen
- **Gefixt**: Wenn der Benutzer keine Berechtigung aufgrund eines zu niedrigen Rangs hat, sieht er auch nur noch die entsprechenden Möglichkeiten
- ⚠️ Update erforderte Änderung in `userlist.php`

## [0.9.8]

- **Neuerung**: Neue Funktion `rankPosition` zur Klasse `loginsystem` hinzugefügt
- **Gefixt**: Ab PHP 7.3 funktionierte die Funktion `check_filename` nicht mehr
- **Gefixt**: Einstellung für "Neu registrierte Benutzer" wurde nicht übernommen
- **Gefixt**: Bug in der Funktion `timediv`
- **Änderung**: Wer das Recht erhält, Ränge vergeben zu dürfen, kann nur noch Ränge gleich oder unter seinem Rang vergeben
- **Änderung**: Autoload für Klassen gegen SPL Autoload ausgetauscht

## [0.9.7] — DSGVO Update

- **Neuerung**: Checkbox dem Kontaktformular hinzugefügt (bzgl. DSGVO)
- **Neuerung**: Checkbox zur Bestätigung der Datenschutzerklärung bei der Registrierung hinzugefügt
- **Neuerung**: Seite für die Datenschutzerklärung hinzugefügt
- **Neuerung**: Impressum und Datenschutz kann in den Einstellungen eingetragen werden
- **Verbessert**: User Löschfunktion DSGVO konform erweitert
- **Gefixt**: Problem bei fehlender DirectoryIndex Einstellung behoben
- **Gefixt**: Fehler bei entfernen des Benutzerbildes (Administrator seitens)
- **Gefixt**: Bei Zusatzfeldern vom Typ "Select" wurde nicht die richtige Option ausgewählt
- **Gefixt**: Rechtschreibfehler behoben
- **Gefixt**: Mögliche Probleme mit `getUser()` bei Eingabe von "0" behoben
- **Gefixt**: Bei `timeformer()` Funktion, Aufruf von nicht vorhandenen Funktionen behoben
- ⚠️ Ab der nächsten Version sollte `timeformer()` und `timediv()` durch neue Funktionen ersetzt werden

## [0.9.6.1]

- **Verbessert**: Autoload Funktion unterstützt nun auch namespaces (sofern diese die gleiche Struktur wie die Ordner haben)
- **Gefixt**: Keine MySQL-Fehler-Ausgabe bei der Installation, Step Benutzer anlegen
- **Gefixt**: Auf manchen Server gab es Probleme mit den Schreibrechten auf die config.php in der Installation
- **Gefixt**: Alle Tabellen haben Standardwerte erhalten
- **Gefixt**: Fehler beim Rang erstellen behoben (MySQL-Fehler wurde nicht ausgegeben)
- **Gefixt**: Fehler beim Löschen einer Seite mit Datei behoben (falls die Datei nicht existierte wurde ein PHP-Fehler erzeugt)

## [0.9.6]

- **Neuerung**: Blacklist für Benutzernamen hinzugefügt (Statisch in der loginsystem Klasse)
- **Neuerung**: htaccess Datei im "templates" Verzeichnis hinzugefügt, zum Schutz vor äußeren Aufrufen
- **Verbessert**: `getCode()` Funktion prüft jetzt nur noch für die angegeben Tabellen/Spalten ob der Code schon verwendet wird
- **Änderung**: Verwendung einer anderen Kodierung des E-Mail Betreffs
- **Änderung**: Rechtschreibfehler behoben
- **Änderung**: Installation Links hinzugefügt
- **Änderung**: Installation (Step 5) - Beschreibung für Vor-/Nachname geändert
- **Gefixt**: Passwort zurücksetzen Seite nicht erreichbar
- **Gefixt**: Änderung der minimalen Passwortlänge hatten keine Auswirkung
- **Gefixt**: Bei der Installation muss kein DB-Passwort mehr eingegeben werden
- **Gefixt**: Bei der Installation sind mehr Zeichen im Datenbankname sowie Benutzername erlaubt
- **Gefixt**: Bei der Installation (Step 5) werden jetzt Zeilenumbrüche bei dem Adressfeld hinzugefügt
- **Gefixt**: War bei Zusatzfeldern ein Regex angegeben, war es automatisch ein Pflichtfeld
- **Gefixt**: `getUser()` Funktion hat bei Übergabe von "fullname" und nur einen angegeben Vornamen nichts zurückgegeben
- **Entfernt**: Version aus dem Footer
- **Entfernt**: Nicht benötigte Spalten aus der MySQL-Benutzer-Tabelle entfernt
