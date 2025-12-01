<?php
/**
 * Portfolio List - Admin
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'Portfolio Management';

// Handle delete action
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $csrf_token = $_GET['token'] ?? '';
    
    if (verifyCSRFToken($csrf_token)) {
        $db = new Database();
        $conn = $db->getConnection();
        
        try {
            // Get image path before deleting
            $stmt = $conn->prepare("SELECT image_path FROM portfolio WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            
            if ($item) {
                // Delete from database
                $stmt = $conn->prepare("DELETE FROM portfolio WHERE id = ?");
                $stmt->execute([$id]);
                
                // Delete image file if it's in uploads directory
                if (strpos($item['image_path'], 'uploads/') !== false) {
                    $filepath = dirname(dirname(__DIR__)) . '/' . $item['image_path'];
                    deleteFile($filepath);
                }
                
                setFlash('success', 'Portfolio item deleted successfully!');
            }
        } catch (PDOException $e) {
            setFlash('error', 'Failed to delete portfolio item.');
        }
    }
    
    redirect(ADMIN_URL . '/portfolio/index.php');
}

// Get all portfolio items
$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare("
        SELECT p.*, c.name as category_name 
        FROM portfolio p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $portfolioItems = $stmt->fetchAll();
} catch (PDOException $e) {
    $portfolioItems = [];
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?> Admin</title>
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/admin.css'); ?>">
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
                        <h2><i class="fas fa-images"></i> All Portfolio Items</h2>
                        <a href="add.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New
                        </a>
                    </div>
                    
                    <?php if (count($portfolioItems) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($portfolioItems as $item): ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo asset_url($item['image_path']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['title']); ?>"
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                                        </td>
                                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                                        <td><?php echo htmlspecialchars($item['category_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo formatDate($item['created_at']); ?></td>
                                        <td class="table-actions">
                                            <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-icon btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?delete=1&id=<?php echo $item['id']; ?>&token=<?php echo $csrf_token; ?>" 
                                               class="btn btn-icon btn-delete" 
                                               title="Delete"
                                               onclick="return confirm('Are you sure you want to delete this item?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-images"></i>
                            <h3>No portfolio items yet</h3>
                            <p>Add your first portfolio item to get started</p>
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Portfolio Item
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
