<?php

// Debug settings
define('DEBUG', true);
define('ROOT', 'http://localhost:4002'); // Base URL for local development

// Upload settings
define('UPLOAD_DIR', __DIR__ . '/uploads/'); // Use absolute path
define('MAX_FILE_SIZE', 100 * 1024 * 1024); // 100MB in bytes

// Timezone
date_default_timezone_set('Asia/Kolkata'); // Change as per your location
