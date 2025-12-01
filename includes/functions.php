<?php
/**
 * Utility Functions
 * Common helper functions used across the application
 */

/**
 * Sanitize input data
 * @param string $data
 * @return string
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone number
 * @param string $phone
 * @return bool
 */
function validatePhone($phone) {
    return preg_match('/^[+]?[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/', $phone);
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Upload image file
 * @param array $file - $_FILES array element
 * @param string $uploadDir - Target directory
 * @return array - ['success' => bool, 'message' => string, 'filename' => string]
 */
function uploadImage($file, $uploadDir) {
    $result = ['success' => false, 'message' => '', 'filename' => ''];
    
    // Check if file was uploaded
    if (!isset($file['error']) || is_array($file['error'])) {
        $result['message'] = 'Invalid file upload';
        return $result;
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = 'Upload failed with error code: ' . $file['error'];
        return $result;
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        $result['message'] = 'File size exceeds maximum allowed size of 5MB';
        return $result;
    }
    
    // Verify file extension
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, ALLOWED_IMAGE_EXTENSIONS)) {
        $result['message'] = 'Invalid file type. Allowed types: ' . implode(', ', ALLOWED_IMAGE_EXTENSIONS);
        return $result;
    }
    
    // Verify MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($mimeType, $allowedMimeTypes)) {
        $result['message'] = 'Invalid file type';
        return $result;
    }
    
    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $newFilename = uniqid() . '_' . time() . '.' . $fileExtension;
    $targetPath = $uploadDir . $newFilename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $result['success'] = true;
        $result['message'] = 'File uploaded successfully';
        $result['filename'] = $newFilename;
    } else {
        $result['message'] = 'Failed to move uploaded file';
    }
    
    return $result;
}

/**
 * Delete file
 * @param string $filepath
 * @return bool
 */
function deleteFile($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Format date
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Generate excerpt from HTML content
 * @param string $content
 * @param int $length
 * @return string
 */
function generateExcerpt($content, $length = 150) {
    $content = strip_tags($content);
    if (strlen($content) > $length) {
        $content = substr($content, 0, $length);
        $content = substr($content, 0, strrpos($content, ' ')) . '...';
    }
    return $content;
}

/**
 * Redirect to URL
 * @param string $url
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Check if user is logged in (admin)
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_email']);
}

/**
 * Require admin login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect(ADMIN_URL . '/login.php');
    }
    
    // Check session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        redirect(ADMIN_URL . '/login.php?timeout=1');
    }
    
    $_SESSION['last_activity'] = time();
}

/**
 * Get pagination data
 * @param int $totalItems
 * @param int $itemsPerPage
 * @param int $currentPage
 * @return array
 */
function getPagination($totalItems, $itemsPerPage, $currentPage) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    return [
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'items_per_page' => $itemsPerPage,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

/**
 * Track visitor
 * @param PDO $conn
 * @param string $page
 */
function trackVisitor($conn, $page = '') {
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Only track once per session
    if (!isset($_SESSION['visitor_tracked'])) {
        try {
            $stmt = $conn->prepare("INSERT INTO site_visitors (ip_address, page) VALUES (?, ?)");
            $stmt->execute([$ip, $page]);
            $_SESSION['visitor_tracked'] = true;
        } catch (PDOException $e) {
            // Silently fail - tracking is not critical
        }
    }
}

/**
 * Set flash message
 * @param string $type - 'success', 'error', 'warning', 'info'
 * @param string $message
 */
function setFlash($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

/**
 * Get and clear flash message
 * @return array|null
 */
function getFlash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type' => $_SESSION['flash_type'],
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Get relative path for images
 * @param string $fullPath
 * @return string
 */
function getRelativePath($fullPath) {
    $baseDir = dirname(__DIR__);
    return str_replace($baseDir . DIRECTORY_SEPARATOR, '', $fullPath);
}

/**
 * Generate URL for assets (CSS, JS, images, etc)
 * @param string $path - relative path from project root (e.g., 'css/style.css' or '/css/style.css')
 * @return string - full URL to the asset
 */
function asset_url($path) {
    // Remove leading slash if present
    $path = ltrim($path, '/');
    return BASE_URL . '/' . $path;
}

/**
 * Generate URL for pages
 * @param string $path - relative path from project root (e.g., 'about.php' or '/about.php')
 * @return string - full URL to the page
 */
function url($path) {
    // Remove leading slash if present
    $path = ltrim($path, '/');
    return BASE_URL . '/' . $path;
}

/**
 * Get base file system path
 * @param string $path - relative path from project root (optional)
 * @return string - absolute file system path
 */
function base_path($path = '') {
    if (empty($path)) {
        return __BASE__;
    }
    // Remove leading slash if present
    $path = ltrim($path, '/\\');
    return __BASE__ . DIRECTORY_SEPARATOR . $path;
}
