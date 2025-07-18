<?php
// Start session for the entire application
session_start();

// Google OAuth Configuration
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID_HERE');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET_HERE');
define('GOOGLE_REDIRECT_URI', 'http://localhost:8000/my-daily-quote/login.php');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'daily_quotes');
define('DB_USER', 'root');
define('DB_PASS', '');

// App Settings
define('APP_NAME', 'My Daily Quote');
define('APP_URL', 'http://localhost:8000/my-daily-quote/');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Admin credentials (hardcoded for simplicity)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123');

// Timezone
date_default_timezone_set('UTC');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
