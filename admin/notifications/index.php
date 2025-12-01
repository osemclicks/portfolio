<?php
/**
 * Notifications List - Admin
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'Notifications';

$db = new Database();
$conn = $db->getConnection();

// Handle delete
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $csrf_token = $_GET['token'] ?? '';
    
    if (verifyCSRFToken($csrf_token)) {
        try {
            $stmt = $conn->prepare("DELETE FROM contact_submissions WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'Notification deleted!');
        } catch (PDOException $e) {
            setFlash('error', 'Failed to delete notification.');
        }
    }
    redirect(ADMIN_URL . '/notifications/index.php');
}

// Get all submissions
try {
    $stmt = $conn->query("SELECT * FROM contact_submissions ORDER BY created_at DESC");
    $submissions = $stmt->fetchAll();
} catch (PDOException $e) {
    $submissions = [];
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
                <?php
                $flash = getFlash();
                if ($flash):
                ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>">
                        <?php echo htmlspecialchars($flash['message']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="data-table">
                    <div class="table-header">
                        <h2><i class="fas fa-bell"></i> Contact Form Submissions</h2>
                    </div>
                    
                    <?php if (count($submissions) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Message Preview</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($submissions as $submission): ?>
                                    <tr style="<?php echo !$submission['is_read'] ? 'font-weight: 600; background-color: #f9f9f9;' : ''; ?>">
                                        <td><?php echo htmlspecialchars($submission['name']); ?></td>
                                        <td><?php echo htmlspecialchars($submission['email']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($submission['message'], 0, 50)) . (strlen($submission['message']) > 50 ? '...' : ''); ?></td>
                                        <td><?php echo formatDate($submission['created_at'], 'M d, Y H:i'); ?></td>
                                        <td>
                                            <?php if ($submission['is_read']): ?>
                                                <span class="badge badge-success">Read</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Unread</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="table-actions">
                                            <a href="view.php?id=<?php echo $submission['id']; ?>" class="btn btn-icon btn-view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="?delete=1&id=<?php echo $submission['id']; ?>&token=<?php echo $csrf_token; ?>" 
                                               class="btn btn-icon btn-delete" 
                                               title="Delete"
                                               onclick="return confirm('Are you sure you want to delete this submission?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>No submissions yet</h3>
                            <p>Contact form submissions will appear here</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
