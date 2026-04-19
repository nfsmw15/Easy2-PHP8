# Easy2-PHP8 — Bootstrap 4 Branch

PHP 8 fork of [EASY 2.0 Loginsystem](https://github.com/Marlight/Easy2-Modern_Business) by Marius Rasche / Marlight Systems.

> **Branches:**
> - `main-bs3` — Bootstrap 3.3.7
> - `main-bs4` — Bootstrap 4.6.2 (this branch)

## Features

- Lightweight PHP login and CMS system
- **Bootstrap 4.6.2** + Start Bootstrap Modern Business BS4 template
- **PHPMailer 6.9.3** SMTP mail dispatch (replaces PHP `mail()`)
- Summernote WYSIWYG editor for Impressum and Datenschutz
- Admin panel: users, ranks, pages, settings, SMTP configuration

## Changes vs. original EASY 2.0

- `mysqli` → PDO with Exceptions
- `mcrypt` → OpenSSL AES-256-GCM encryption
- Session hardening: HttpOnly, Secure, SameSite=Strict
- Security HTTP headers + CSP in `index.php`
- `declare(strict_types=1)` in all core files
- PHP `mail()` → PHPMailer 6.9.3 with SMTP authentication
- Bootstrap 3 → Bootstrap 4.6.2 (navbar, cards, breadcrumbs migrated)
- jQuery 1.11.1 → jQuery 3.7.1
- Bootstrap bundle with Popper.js included
- `BS_VERSION` and `EASY_VERSION` constants in `config.inc.php`

## Requirements

- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.3+
- Apache or nginx

## Installation

1. Copy files to your web root
2. Run the installer at `./install/`
3. Configure SMTP in the admin panel under Settings

## Updating an existing installation

```bash
git pull origin main-bs4
```

If updating from before v1.1.0, run in your database:

```sql
INSERT INTO `PREFIX_ml_main` (`id`, `tag`, `value`) VALUES
(19, 'smtp_host', ''), (20, 'smtp_port', '587'),
(21, 'smtp_user', ''), (22, 'smtp_pass', ''),
(23, 'smtp_encryption', 'tls');
```

And add to `system/config.inc.php`:

```php
define('BS_VERSION', 4);
```

## License

Original EASY 2.0: GNU General Public License v3.0
Copyright (C) 2018 Marius Rasche – Marlight Systems

PHP 8 fork: GNU Affero General Public License v3.0
Copyright (C) 2026 nfsmw15 <https://nfsmw15.de>
