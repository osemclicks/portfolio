<?php
/**
 * Blogs Management - Admin
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'Blog Management';

$db = new Database();
$conn = $db->getConnection();

// Handle delete
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $csrf_token = $_GET['token'] ?? '';
    
    if (verifyCSRFToken($csrf_token)) {
        try {
            // Get cover image path
            $stmt = $conn->prepare("SELECT cover_image FROM blogs WHERE id = ?");
            $stmt->execute([$id]);
            $blog = $stmt->fetch();
            
            if ($blog) {
                // Delete from database
                $stmt = $conn->prepare("DELETE FROM blogs WHERE id = ?");
                $stmt->execute([$id]);
                
                // Delete cover image if in uploads
                if (strpos($blog['cover_image'], 'uploads/') !== false) {
                    deleteFile(dirname(dirname(__DIR__)) . '/' . $blog['cover_image']);
                }
                
                setFlash('success', 'Blog post deleted!');
            }
        } catch (PDOException $e) {
            setFlash('error', 'Failed to delete blog post.');
        }
    }
    redirect(ADMIN_URL . '/blogs/index.php');
}

// Get all blogs
try {
    $stmt = $conn->query("SELECT * FROM blogs ORDER BY created_at DESC");
    $blogs = $stmt->fetchAll();
} catch (PDOException $e) {
    $blogs = [];
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
                        <h2><i class="fas fa-blog"></i> All Blog Posts</h2>
                        <a href="add.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Post
                        </a>
                    </div>
                    
                    <?php if (count($blogs) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Cover</th>
                                    <th>Title</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($blogs as $blog): ?>
                                    <tr>
                                        <td>
                                            <img src="../../<?php echo htmlspecialchars($blog['cover_image']); ?>" 
                                                 alt="Cover"
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                                        </td>
                                        <td><?php echo htmlspecialchars($blog['title']); ?></td>
                                        <td><?php echo formatDate($blog['created_at']); ?></td>
                                        <td class="table-actions">
                                            <a href="edit.php?id=<?php echo $blog['id']; ?>" class="btn btn-icon btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?delete=1&id=<?php echo $blog['id']; ?>&token=<?php echo $csrf_token; ?>" 
                                               class="btn btn-icon btn-delete" 
                                               title="Delete"
                                               onclick="return confirm('Are you sure?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-feather-alt" style="color: #6c63ff; font-size: 5rem; margin-bottom: 20px;"></i>
                            <h3>No blog posts yet</h3>
                            <p style="margin-bottom: 25px;">Add your first blog post to get started</p>
                            <!-- <a href="add.php" class="btn btn-primary" style="padding: 10px 20px; font-size: 0.9rem;">
                                <i class="fas fa-plus"></i> Add Blog Post
                            </a> -->
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
