<?php

// Detect local development
$isLocal = in_array($_SERVER['SERVER_NAME'], [
    'localhost',
    '127.0.0.1',
    '::1'
]);

define('DirROOT', dirname(dirname(__DIR__)));  

if ($isLocal) {
    define('ROOT', 'http://localhost:4002');   // ✅ Local URL
} else {
    define('ROOT', 'https://' . $_SERVER['HTTP_HOST']);  // ✅ InfinityFree URL auto-detected
}

// Debug mode
define('DEBUG', $isLocal);

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Security
define('LOCK_KEY', "ProBid");