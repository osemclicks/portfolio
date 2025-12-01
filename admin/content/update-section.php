<?php
/**
 * Update Page Content Section - API Handler
 */
require_once '../../config/config.php';
requireLogin();

$db = new Database();
$conn = $db->getConnection();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('error', 'Invalid request method.');
    redirect(ADMIN_URL . '/content/pages.php');
}

// Validate CSRF token
$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($csrf_token)) {
    setFlash('error', 'Invalid security token. Please try again.');
    redirect(ADMIN_URL . '/content/pages.php');
}

// Get and validate input
$sectionId = isset($_POST['section_id']) ? intval($_POST['section_id']) : 0;
$content = $_POST['content'] ?? '';

if ($sectionId <= 0) {
    setFlash('error', 'Invalid section ID.');
    redirect(ADMIN_URL . '/content/pages.php');
}

if (empty(trim($content))) {
    setFlash('error', 'Content cannot be empty.');
    redirect(ADMIN_URL . '/content/edit-section.php?id=' . $sectionId);
}

// Update content in database
try {
    $stmt = $conn->prepare("
        UPDATE page_content 
        SET content = ?, updated_at = CURRENT_TIMESTAMP 
        WHERE id = ?
    ");
    $stmt->execute([$content, $sectionId]);
    
    if ($stmt->rowCount() > 0) {
        setFlash('success', 'Content updated successfully!');
    } else {
        setFlash('info', 'No changes were made to the content.');
    }
    
    redirect(ADMIN_URL . '/content/pages.php');
} catch (PDOException $e) {
    setFlash('error', 'Failed to update content. Please try again.');
    redirect(ADMIN_URL . '/content/edit-section.php?id=' . $sectionId);
}
