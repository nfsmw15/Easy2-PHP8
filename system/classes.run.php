<?php
declare(strict_types=1);
/********************************************
 * System:   EASY 2.0 Loginsystem
 * File:     classes.run.php
 * PHP8-Umbau: static fn statt static function,
 *             DEMO_MODE als Konstante
 *
 * PHP 8 modifications: Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 *********************************************/

define('DEMO_MODE', false);

// Autoloader
spl_autoload_register(static function (string $class): void {
    $dir  = str_replace('\\', '/', $class);
    if (str_ends_with($dir, 'I')) {
        $parts       = explode('/', $dir);
        $lastElement = array_pop($parts);
        $dir         = implode('/', $parts) . '/Interfaces/' . $lastElement;
    }
    $file = __DIR__ . '/classes/' . $dir . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Loginsystem initialisieren
$loginsystem = new loginsystem();

// Captcha (nur wenn angefordert)
if (isset($_GET['captcha']) && $_GET['captcha'] === 'img') {
    $captcha = new captcha();
    $captcha->generateCaptcha();
}

// Sites, Menu, zusätzliche Felder
$rules             = (isset($_GET['p']) && $_GET['p'] === 'rules') ? new rules() : null;
$sites             = new sites();
$menu              = new menu();
$additional_fields = new additional_fields();
