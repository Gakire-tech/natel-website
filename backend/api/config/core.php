<?php
// backend/api/config/core.php

// Show error reporting
error_reporting(E_ALL);

// Set default timezone
date_default_timezone_set('UTC');

// Database credentials - these should be moved to environment variables in production
define('DB_HOST', 'localhost');
define('DB_NAME', 'infinity_enterprise');
define('DB_USER', 'root');
define('DB_PASS', '');

// JWT Settings
define('JWT_SECRET_KEY', 'infinity_enterprise_secret_key_2026'); // Should be moved to environment variable in production
define('JWT_ALGO', 'HS256');

// Upload settings
define('UPLOAD_PATH', __DIR__ . '/../../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_TYPES', 'jpg,jpeg,png,gif,pdf,doc,docx');