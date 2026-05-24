<?php
declare(strict_types=1);
define('EASY_WEBROOT', dirname(__DIR__));
foreach (glob(__DIR__ . '/plugins/*/run.php') as $plugin) {
    include $plugin;
}
