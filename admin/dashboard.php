<?php
/**
 * Admin Dashboard
 */
require_once '../config/config.php';
requireLogin();

$pageTitle = 'Dashboard';

// Get database connection
$db = new Database();
$conn = $db->getConnection();

// Get statistics
$stats = [];

// Total visitors
try {
    $stmt = $conn->query("SELECT COUNT(DISTINCT ip_address) as count FROM site_visitors");
    $stats['visitors'] = $stmt->fetch()['count'] ?? 0;
} catch (PDOException $e) {
    $stats['visitors'] = 0;
}

// Total portfolio items
try {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM portfolio");
    $stats['portfolio'] = $stmt->fetch()['count'] ?? 0;
} catch (PDOException $e) {
    $stats['portfolio'] = 0;
}

// Total blogs
try {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM blogs");
    $stats['blogs'] = $stmt->fetch()['count'] ?? 0;
} catch (PDOException $e) {
    $stats['blogs'] = 0;
}

// Total contact submissions
try {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM contact_submissions");
    $stats['submissions'] = $stmt->fetch()['count'] ?? 0;
} catch (PDOException $e) {
    $stats['submissions'] = 0;
}

// Get recent contact submissions
try {
    $stmt = $conn->prepare("SELECT id, name, email, message, is_read, created_at FROM contact_submissions ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $recentSubmissions = $stmt->fetchAll();
} catch (PDOException $e) {
    $recentSubmissions = [];
}

// Get recent portfolio items
try {
    $stmt = $conn->prepare("
        SELECT p.id, p.title, p.created_at, c.name as category_name 
        FROM portfolio p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recentPortfolio = $stmt->fetchAll();
} catch (PDOException $e) {
    $recentPortfolio = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?> Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <?php
                $flash = getFlash();
                if ($flash):
                ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>">
                        <?php echo htmlspecialchars($flash['message']); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Total Visitors</h3>
                            <div class="stat-value"><?php echo number_format($stats['visitors']); ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-images"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Portfolio Items</h3>
                            <div class="stat-value"><?php echo number_format($stats['portfolio']); ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-blog"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Blog Posts</h3>
                            <div class="stat-value"><?php echo number_format($stats['blogs']); ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Contact Submissions</h3>
                            <div class="stat-value"><?php echo number_format($stats['submissions']); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activities -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px;">
                    <!-- Recent Contact Submissions -->
                    <div class="data-table">
                        <div class="table-header">
                            <h2><i class="fas fa-envelope"></i> Recent Contact Submissions</h2>
                            <a href="notifications/index.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <?php if (count($recentSubmissions) > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentSubmissions as $submission): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($submission['name']); ?></td>
                                            <td><?php echo htmlspecialchars($submission['email']); ?></td>
                                            <td><?php echo formatDate($submission['created_at']); ?></td>
                                            <td>
                                                <?php if ($submission['is_read']): ?>
                                                    <span class="badge badge-success">Read</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Unread</span>
                                                <?php endif; ?>
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
                    
                    <!-- Recent Portfolio Items -->
                    <div class="data-table">
                        <div class="table-header">
                            <h2><i class="fas fa-images"></i> Recent Portfolio Items</h2>
                            <a href="portfolio/index.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <?php if (count($recentPortfolio) > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentPortfolio as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                                            <td><?php echo htmlspecialchars($item['category_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo formatDate($item['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-images"></i>
                                <h3>No portfolio items yet</h3>
                                <p>Add your first portfolio item to get started</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
