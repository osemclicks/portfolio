<?php
/**
 * Global Configuration File
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Site Configuration
define('SITE_NAME', 'Osem Clicks');

// Base Directory Path - Absolute file system path to project root
define('__BASE__', dirname(__DIR__));

// Dynamic URL Detection
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Get the base path from document root
$documentRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$projectRoot = str_replace('\\', '/', __BASE__);
$projectPath = str_replace($documentRoot, '', $projectRoot);

define('SITE_URL', $protocol . $host . $projectPath);
define('ADMIN_URL', SITE_URL . '/admin');
define('BASE_URL', SITE_URL); // Alias for easier usage

// File Upload Configuration
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');
define('PORTFOLIO_UPLOAD_DIR', UPLOAD_DIR . 'portfolio/');
define('BLOG_UPLOAD_DIR', UPLOAD_DIR . 'blogs/');
define('GEAR_UPLOAD_DIR', UPLOAD_DIR . 'gears/');
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes

// Allowed image extensions
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('ITEMS_PER_PAGE', 12);
define('BLOGS_PER_PAGE', 6);

// Security
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// TinyMCE Editor
define('TINYMCE_API_KEY', 'p6go9g16a7e68v8s0ktp7bohlwfqvk7fhvc3xicp8vvi7hg9'); // TinyMCE API key

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Autoload required files
 */
require_once __DIR__ . '/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
