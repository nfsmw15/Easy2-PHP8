# Upgrade-Anleitung: EASY 2.0 → PHP 8 Fork

Diese Anleitung beschreibt, wie Sie von einer bestehenden **EASY 2.0 Installation** (v0.9.6 bis v0.9.8.6) zu dieser **PHP 8 Fork** upgraden.

---

## ⚠️ Wichtig: Vor dem Upgrade

### Voraussetzungen
- **PHP 8.0+** (vorab mit Hosting-Provider prüfen oder lokal testen)
- **MySQL 5.7+** oder **MariaDB 10.2+**
- **Vollständiges Backup** der bestehenden Installation
- **SSH-/FTP-Zugang** zum Server (oder Hosting-Panel mit Dateimanager)

### Sicherung erstellen (kritisch!)
```bash
# Datenbank-Backup
mysqldump -u benutzername -p datenbankname > easy2_backup.sql

# Dateien-Backup (via SSH)
tar -czf easy2_backup.tar.gz /path/to/easy2/
```

---

## 🔴 Breaking Changes

Diese Änderungen können die Funktionalität beeinflussen:

| Change | Original EASY 2.0 | PHP 8 Fork | Migrationsbedarf |
|--------|-------------------|------------|------------------|
| **Datenbank** | mysqli | PDO | ⚠️ Core automatisch, eigene Erweiterungen prüfen |
| **Verschlüsselung** | mcrypt (deprecated) | OpenSSL AES-256-GCM | ⚠️ Existierende verschlüsselte Daten könnten problematisch sein |
| **Typisierung** | Keine | Strict Types (PHP 8) | ✅ Keine Auswirkung auf Nutzer |
| **Session Sicherheit** | Standard | HttpOnly, Secure, SameSite | ✅ Besser! |
| **Lizenz** | GPLv3 | AGPLv3 | ℹ️ Beachten Sie die neuen Bedingungen |

---

## ⚠️ Sehr alte Installationen: Neuinstallation empfohlen

Wer von EASY 2.0 **v0.9.6 oder älter** upgradet — oder wer unsicher ist, welche Daten historisch gespeichert wurden — sollte eine **Neuinstallation mit manueller Datenmigration** einer direkten In-Place-Überschreibung vorziehen.

### Warum Neuinstallation?

| Problem | Erklärung |
|---------|-----------|
| **Passwort-Hashes** | Alte MD5/SHA1-Hashes sind nicht kompatibel und gelten als unsicher — alle Benutzer müssen ihr Passwort neu setzen |
| **Verschlüsselte Daten** | Die Verschlüsselung wurde von mcrypt auf OpenSSL AES-256-GCM umgestellt — alte verschlüsselte Felder können nicht automatisch migriert werden |
| **Rang-/Rechte-Daten** | Das Rechte-Modell hat sich geändert; alte `ranks`-Einträge blind zu importieren kann zu unbeabsichtigten Berechtigungen führen |
| **Menü- und Seiten-URLs** | Historisch gespeicherte Menü-URLs können `javascript:`- oder andere Injection-Werte enthalten; nach dem Import prüfen |
| **Zusatzfelder (Benutzerdaten)** | Alte Feldwerte wurden ohne XSS-Escaping gespeichert und können gespeicherte Script-Tags enthalten — Inhalte vor dem Import prüfen |
| **Seiteninhalte / Impressum / Datenschutz** | In `ml_main` gespeicherte HTML-Felder (`impressum_info`, `impressum_content`, `privacy_policy`) sowie selbst erstellte Seiten-Templates prüfen — historisch können dort unsanitierte Inhalte stehen |

### Empfohlene Vorgehensweise bei alten Installationen

1. **Neuinstallation** der PHP 8 Fork auf sauberem Pfad
2. **Konfiguration** manuell aus der alten `config.user.php` übernehmen
3. **Nur strukturell harmlose Daten** importieren (z.B. Seiten-Inhalte nach Prüfung)
4. **Menü-Einträge** nach dem Import auf verdächtige URLs prüfen (`SELECT url FROM ml_menu WHERE url != ''`)
5. **Zusatzfeld-Inhalte** nach dem Import auf gespeicherte Script-Tags prüfen (`SELECT value FROM ml_additional_user_information WHERE value LIKE '%<script%'`)
6. **Alle Benutzer zum Passwort-Reset zwingen** — kein Benutzer sollte sich mit einem alten Hash anmelden können:
   ```sql
   -- Alle Passwörter ungültig machen (Benutzer müssen "Passwort vergessen" nutzen).
   -- Nur auf gesicherter Kopie / Neuinstallation ausführen!
   -- Tabellenpräfix ggf. anpassen (Standard: ml_)
   UPDATE ml_user SET password = '' WHERE id > 0;
   ```
7. **Admin/Webmaster-Accounts bewusst neu anlegen** statt aus der alten DB zu übernehmen

---

## 🔐 Sicherheitsänderungen (ab v1.2.6 / v1.1.8)

Wer von EASY 2.0 oder einer älteren Fork-Version upgradet, muss folgende Punkte in **eigenen/angepassten Templates** nachpflegen:

### CSRF-Tokens in Formularen

Die folgenden öffentlichen Formulare benötigen jetzt zwingend `<?php echo csrf_field(); ?>` unmittelbar nach dem öffnenden `<form>`-Tag:

| Template | Formular |
|----------|----------|
| `templates/login/regist.php` | Registrierung |
| `templates/login/pwv.php` | Passwort vergessen |
| `templates/login/pw_reset.php` | Passwort zurücksetzen |
| `templates/bootstrap/contact.php` | Kontaktformular |

Ohne dieses Feld werden POST-Anfragen mit `Ungültiger CSRF-Token!` abgewiesen.

### XSS-Escaping mit `e()`

Neue Hilfsfunktion `e()` in `functions.inc.php`:
```php
echo e($value); // htmlspecialchars mit ENT_QUOTES|ENT_SUBSTITUTE
```

Alle `$_POST`-Werte in `value=""`-Attributen und Textarea-Inhalten **müssen** mit `e()` escaped werden. Betrifft insbesondere eigene Admin- und Login-Templates.

### Menü-URL-Sanitierung

`sanitize_menu_url()` in `menu.php` blockiert automatisch `javascript:`, `data:`, `vbscript:` und unbekannte URL-Schemata. Bereits gespeicherte URLs mit diesen Schemata werden beim Rendern durch `#` ersetzt — kein DB-Eingriff nötig.

### SMTP-Passwort-Feld

Das `smtp_pass`-Eingabefeld in `templates/adm/settings.php` gibt das gespeicherte Passwort nicht mehr als `value=""` aus. Ein leeres Feld beim Speichern überschreibt das bestehende Passwort **nicht** mehr. Custom-Settings-Templates müssen entsprechend angepasst werden (leeres `value=""`, Guard in `loginsystem.php` ist serverseitig bereits aktiv).

### HTTP 404 bei Fehlerseiten

`includeSite()` in `sites.php` sendet jetzt automatisch `http_response_code(404)` wenn die konfigurierte Fehlerseite ausgeliefert wird. Kein Handlungsbedarf — aber Caches/Proxys müssen ggf. geleert werden.

---

## 📋 Schritt-für-Schritt Upgrade

### Schritt 1: Aktuelles System herunterfahren (optional)
```bash
# Wartungsmodus aktivieren - einfach umbenennen
mv index.php index.php.bak
echo "System wird aktualisiert..." > index.php
```

### Schritt 2: Dateien sichern & neue Version hochladen
```bash
# Lokales Backup der aktuellen Installation
cp -r /path/to/easy2 /path/to/easy2_backup

# Neue Dateien hochladen (via Git Clone oder manuell)
git clone https://github.com/nfsmw15/Easy2-PHP8.git /path/to/easy2_new
```

### Schritt 3: Verzeichnisse & Berechtigungen

**Folgende Verzeichnisse müssen beschreibbar sein (755 oder 775):**
```
system/        (enthält config.user.php)
avatare/       (Benutzerprofile-Bilder)
```

**Prüfen:**
```bash
ls -la system/ avatare/
```

### Schritt 4: Konfiguration übernehmen
```bash
# Alte Konfiguration kopieren:
cp easy2_backup/system/config.user.php easy2_new/system/config.user.php

# Falls custom CSS/JS vorhanden:
cp easy2_backup/css/custom.css easy2_new/css/ 2>/dev/null || true
cp easy2_backup/js/custom.js easy2_new/js/ 2>/dev/null || true
```

### Schritt 5: Datenbank-Migration
**Die Kernstruktur ist weitgehend kompatibel**, aber neue Fork-Versionen bringen zusätzliche Felder und Einstellungen in `ml_main` mit, und die Sicherheitslogik setzt bestimmte DB-Werte voraus. Bei sehr alten Installationen wird eine Neuinstallation mit selektiver Migration empfohlen — siehe Abschnitt [Sehr alte Installationen](#️-sehr-alte-installationen-neuinstallation-empfohlen) weiter oben.

Wenn Sie in der alten Version verschlüsselte Felder (z.B. Passwörter, API-Keys) haben:
```sql
-- Prüfen welche Spalten encrypted sind:
SELECT COLUMN_NAME, COLUMN_COMMENT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME='ml_user' AND COLUMN_SCHEMA='datenbankname';
```

⚠️ **Warnung**: Passwörter verwenden in der PHP 8 Fork `password_hash()` / `password_verify()` (bcrypt). Alte MD5/SHA1-Hashes müssen manuell migriert werden.

### Schritt 6: System online bringen
```bash
# Wartungsmodus deaktivieren
mv index.php.bak index.php
```

### Schritt 7: Installation/Überprüfung
```
Öffnen Sie im Browser: https://ihre-domain.de/install/
```

Die Installation sollte erkennen, dass die Datenbank bereits existiert und die Verbindung konfigurieren.

---

## 🔐 Passwort-Migration

**Alte Passwörter funktionieren NICHT mehr automatisch!**

### Option A: Benutzer müssen Passwort zurücksetzen
Benutzer erhalten eine E-Mail zum Zurücksetzen des Passworts:
```
https://ihre-domain.de/?p=pwv
```

### Option B: Passwörter-Batch-Migration (nur Administrator)
Falls Sie Benutzer-Passwörter manuell setzen möchten:

```php
// In admin_users.php oder per Datenbankabfrage:
$password = 'neupasswort123';
$hash = password_hash($password, PASSWORD_BCRYPT);

UPDATE ml_user SET password='$hash' WHERE id=123;
```

---

## 📁 Verzeichnisstruktur Unterschiede

### Neue Dateien/Verzeichnisse:
```
system/config.example.inc.php    ← Beispiel-Konfiguration
css/font/                        ← Summernote & Glyphicon-Fonts
js/summernote-init.js            ← WYSIWYG Editor für Impressum/Datenschutz
```

### Entfernte/Umgestellte Dateien:
```
fonts/glyphicons  ← Umgezogen nach css/font/
```

---

## 🔧 Häufige Probleme beim Upgrade

### Problem 1: "Fatal error: Uncaught PDOException"
**Ursache**: Datenbankverbindung fehlgeschlagen

**Lösung**:
```bash
# Check: config.user.php ist korrekt
cat system/config.user.php | grep DB_
```

**Alternativ**: Installer neu ausführen → `https://ihre-domain.de/install/`

---

### Problem 2: "Class 'mysqli' not found"
**Ursache**: Alte PHP-Code nutzt noch `mysqli`

**Lösung**: Alle Custom-Plugins/Erweiterungen überprüfen und zu PDO migrieren.

Kontaktieren Sie den Plugin-Autor für Updates.

---

### Problem 3: Benutzer können sich nicht anmelden
**Ursache**: Passwort-Hash-Format hat sich geändert

**Lösung**: 
1. Benutzer lassen Sie "Passwort vergessen" klicken
2. Oder als Admin neu setzen (siehe Passwort-Migration)

---

### Problem 4: CSS/Icons funktionieren nicht
**Ursache**: Font-Pfade stimmen nicht

**Lösung**:
```bash
# Prüfen ob Fonts existieren:
ls -la css/font/

# Wenn leer: Fonts aus dem original-Bundle kopieren
cp font-awesome/fonts/* css/font/ 2>/dev/null || true
```

---

## 📝 Konfigurationsänderungen

### Neue Einstellungen in `config.user.php`:

```php
// Session Sicherheit (automatisch gesetzt):
ini_set('session.http_only', 1);
ini_set('session.secure', 1);           // Nur HTTPS!
ini_set('session.use_strict_mode', 1);

// Cache-Control (verhindert Session-Caching)
header('Cache-Control: no-store');
```

Diese sind **automatisch aktiviert** und erhöhen die Sicherheit.

---

## 🔗 Lizenzänderung: GPLv3 → AGPLv3

**Was bedeutet das für Sie?**

| Punkt | Auswirkung |
|-------|-----------|
| **Weitergabe von Code** | Sie müssen den geänderten Code unter AGPLv3 veröffentlichen |
| **Kommerzielle Nutzung** | ✅ Erlaubt (mit Lizenz-Einhaltung) |
| **Eigene Anpassungen** | Offenlegungspflicht gilt auch wenn Sie das System **öffentlich als Netzwerkdienst betreiben** — nicht nur bei Weitergabe |
| **Datenschutz** | AGPLv3 schließt die "ASP-Lücke" der GPLv3: Bereitstellen als Webservice zählt als Verbreitung |

**Details**: Siehe [LICENSE.md](LICENSE.md)

---

## 🛡️ Sicherheits-Härtung nach der Migration

Diese Punkte gelten unabhängig davon, ob In-Place-Upgrade oder Neuinstallation:

### Sessions & Cookies

- **Alle aktiven Sessions ungültig machen**: Alte Session-Dateien enthalten keinen CSRF-Token und entsprechen nicht dem neuen Sicherheitsmodell. Entweder den Session-Speicher leeren (`session.save_path` leeren) oder die Session-Tabelle in der DB trunkieren — je nach Hosting-Setup.
- **Remember-Me-Cookies nicht übernehmen**: Die Cookie-Signatur hat sich geändert. Alte "Eingeloggt bleiben"-Cookies werden nicht akzeptiert und müssen vom Browser gelöscht werden. Bestehende Tokens in der DB können manuell geleert werden:
  ```sql
  UPDATE ml_user SET auto_login_code = '', auto_login_time = '' WHERE auto_login_code != '';
  ```

### Admin-Bereich

- **SMTP-Passwort neu eintragen**: Das Passwort-Feld zeigt nach dem Upgrade keinen gespeicherten Wert mehr an. Einstellungen → SMTP-Passwort neu eingeben und speichern.
- **Admin/Webmaster-Accounts prüfen**: Sicherstellen, dass nur bekannte Accounts den Webmaster-Rang haben. Mehrere Webmaster gleichzeitig sind ein Sicherheitsrisiko (gegenseitiges Löschen möglich).
- **Testbenutzer entfernen**: Vor dem Produktiveinsatz alle Testaccounts aus `?p=userlist` löschen.

### Server-Bereinigung

- **Install-Verzeichnis löschen**: Das `install/`-Verzeichnis **muss** nach der Installation entfernt werden — es enthält Setup-Skripte mit direktem DB-Zugriff.
  ```bash
  rm -rf /path/to/easy2/install/
  ```
- **Security-Header prüfen**: Mit [securityheaders.com](https://securityheaders.com) oder dem Browser-DevTools-Netzwerk-Tab prüfen, ob `HttpOnly`, `Secure`, `SameSite` auf Session-Cookies gesetzt sind und ob HTTPS korrekt erzwungen wird.
- **Reverse-Proxy**: Falls hinter Nginx/Traefik betrieben, sicherstellen dass `X-Forwarded-Proto: https` korrekt weitergeleitet wird — sonst werden Cookies ohne `Secure`-Flag gesetzt (siehe `is_https()` in `functions.inc.php`).

---

## ✅ Nach dem Upgrade: Checkliste

- [ ] Installation erfolgreich
- [ ] Admin-Panel erreichbar
- [ ] Benutzer können sich anmelden
- [ ] CSS/Icons korrekt angezeigt
- [ ] Datenbank-Backup vorhanden
- [ ] Test mit verschiedenen Browsern
- [ ] HTTPS aktiviert (wird empfohlen)
- [ ] Alte Installation gelöscht (nach Testverlauf)
- [ ] Custom-Templates: `<?php echo csrf_field(); ?>` in allen öffentlichen Formularen ergänzt
- [ ] Custom-Templates: `$_POST`-Werte mit `e()` escaped
- [ ] SMTP-Passwort in den Einstellungen neu gesetzt (wird nicht mehr aus DB vorausgefüllt)
- [ ] Alte Sessions geleert / Session-Speicher bereinigt
- [ ] Remember-Me-Tokens in DB geleert (`auto_login_code`)
- [ ] Alle Benutzer zum Passwort-Reset gezwungen (bei Migration aus EASY 2.0)
- [ ] Admin/Webmaster-Accounts geprüft — nur bekannte Accounts
- [ ] Testbenutzer entfernt
- [ ] `install/`-Verzeichnis gelöscht
- [ ] Security-Header und Cookie-Flags geprüft (HttpOnly, Secure, SameSite)

---

## 🆘 Probleme? Support & Hilfe

Falls es Probleme gibt:

1. **Fehler-Logs prüfen**:
   ```bash
   tail -f /var/log/apache2/error.log   # Apache
   tail -f /var/log/nginx/error.log     # Nginx
   ```

2. **PHP-Version prüfen**:
   ```bash
   php -v
   ```
   Muss **PHP 8.0+** sein!

3. **Datenbank-Verbindung testen**:
   ```bash
   php install/setup/config.php
   ```

4. **GitHub Issues**: https://github.com/nfsmw15/Easy2-PHP8/issues

---

## 📚 Weitere Ressourcen

- [README.md](README.md) — Übersicht der Fork
- [CHANGELOG.md](CHANGELOG.md) — Komplette Versionshistorie
- [LICENSE.md](LICENSE.md) — Lizenzinformationen

---

**Viel Erfolg beim Upgrade! 🚀**
