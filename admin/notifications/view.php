<?php
/**
 * View Notification Details - Admin
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'View Notification';
$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    setFlash('error', 'Invalid notification.');
    redirect(ADMIN_URL . '/notifications/index.php');
}

$db = new Database();
$conn = $db->getConnection();

// Get submission
try {
    $stmt = $conn->prepare("SELECT * FROM contact_submissions WHERE id = ?");
    $stmt->execute([$id]);
    $submission = $stmt->fetch();
    
    if (!$submission) {
        setFlash('error', 'Notification not found.');
        redirect(ADMIN_URL . '/notifications/index.php');
    }
    
    // Mark as read
    if (!$submission['is_read']) {
        $stmt = $conn->prepare("UPDATE contact_submissions SET is_read = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }
} catch (PDOException $e) {
    setFlash('error', 'Failed to load notification.');
    redirect(ADMIN_URL . '/notifications/index.php');
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?> Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include '../includes/header.php'; ?>
            
            <div class="admin-content">
                <div class="form-card" style="max-width: 800px;">
                    <h2><i class="fas fa-envelope-open"></i> Contact Submission Details</h2>
                    
                    <div style="margin: 25px 0; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                        <div style="margin-bottom: 20px;">
                            <strong>From:</strong> <?php echo htmlspecialchars($submission['name']); ?>
                        </div>
                        <div style="margin-bottom: 20px;">
                            <strong>Email:</strong> 
                            <a href="mailto:<?php echo htmlspecialchars($submission['email']); ?>">
                                <?php echo htmlspecialchars($submission['email']); ?>
                            </a>
                        </div>
                        <?php if (!empty($submission['phone'])): ?>
                            <div style="margin-bottom: 20px;">
                                <strong>Phone:</strong> 
                                <a href="tel:<?php echo htmlspecialchars($submission['phone']); ?>">
                                    <?php echo htmlspecialchars($submission['phone']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div style="margin-bottom: 20px;">
                            <strong>Received:</strong> <?php echo formatDate($submission['created_at'], 'F d, Y \a\t g:i A'); ?>
                        </div>
                        <div style="margin-bottom: 0;">
                            <strong>Status:</strong>
                            <span class="badge badge-success">Read</span>
                        </div>
                    </div>
                    
                    <div style="margin: 25px 0;">
                        <strong>Message:</strong>
                        <div style="margin-top: 10px; padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 8px; white-space: pre-wrap;">
<?php echo htmlspecialchars($submission['message']); ?>
                        </div>
                    </div>
                    
                    <div class="btn-group" style="margin-top: 30px;">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Notifications
                        </a>
                        <a href="?delete=1&id=<?php echo $submission['id']; ?>&token=<?php echo $csrf_token; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this submission?');">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
