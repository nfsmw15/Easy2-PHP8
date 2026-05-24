# Changelog — EASY 2.0 PHP8 Fork

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
- `system/.htaccess` angelegt: `deny from all` + Ausnahme für `placeholder.php`
- Gast-Rang: Site 18 (Menüverwaltung) aus den Standard-Berechtigungen entfernt (`install/sql/_ml_ranks.sql`)

---

## [1.1.1] — 2026-04-19

### Neu
- `BS_VERSION`-Konstante in `config.inc.php` definiert (Wert: `3`)
- Summernote-Editor wird jetzt auch auf der `news_add`-Seite geladen

### Bugfixes
- CSP-Header um `unsafe-eval` ergänzt (Summernote-Editor wurde blockiert)
- Doppelte Summernote-Script-Einbindung in `settings.php` entfernt

---

## [1.1.0] — 2026-04-19

### Neu
- **PHPMailer 6.9.3** ersetzt PHP `mail()` — SMTP-Authentifizierung mit STARTTLS/SSL (z.B. Mailcow)
- SMTP-Einstellungen im Admin-Panel konfigurierbar (Host, Port, Verschlüsselung, Benutzer, Passwort)
- E-Mail-Templates (`emailtpl/`) hinzugefügt

### Bugfixes
- Captcha-Schriftart `system/fonts/Captureit.ttf` fehlte — jetzt enthalten
- Session-`ini_set()`-Warnungen im Installer behoben (`session_status()` Guard)

### Bootstrap 4-Branch (`main-bs4`)
- Neuer Branch mit Bootstrap 4.6.2
- Navbar, Breadcrumbs, Cards, Buttons auf BS4-Klassen migriert

### Konfiguration
- `EASY_VERSION` auf `1.1.0` gesetzt

---

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
