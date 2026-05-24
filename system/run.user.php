<?php
declare(strict_types=1);
foreach (glob(__DIR__ . '/plugins/*/run.php') as $plugin) {
    try {
        include $plugin;
    } catch (\Throwable $e) {
        error_log('Plugin error in ' . $plugin . ': ' . $e->getMessage());
    }
}
