# easy2-php8

PHP 8 fork of [EASY 2.0 Loginsystem](https://marlight-systems.de) by Marius Rasche.

## About

EASY 2.0 is a lightweight PHP login and CMS system. This fork rewrites the codebase for PHP 8 compatibility.

## Changes vs. original

- mysqli → PDO with Exceptions (`system/classes/database.php`)
- mcrypt → OpenSSL AES-256-GCM (`system/functions.inc.php`)
- `each()` removed (PHP 8 removed)
- Session hardening: HttpOnly, Secure, SameSite
- Security HTTP headers in `index.php`
- `declare(strict_types=1)` in core files

## Requirements

- PHP 8.0+
- MySQL / MariaDB
- Apache or nginx (or PHP built-in server for development)

## Installation

1. Copy files to your web root
2. Run the installer at `./install/`
3. Configure `system/config.inc.php`

## License

Original EASY 2.0: GNU General Public License v3.0
Copyright (C) 2018 Marius Rasche – Marlight Systems

PHP 8 modifications: GNU Affero General Public License v3.0
Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
