<?php

// ✅ Maintenance mode check (must be FIRST)
if (file_exists(__DIR__ . '/maintenance.html')) {
    include __DIR__ . '/maintenance.html';
    exit;
}

session_start();
require "./app/core/init.php";

$app = new App();
$app->loadController();
