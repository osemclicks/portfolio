<?php
/**
 * Category Management - Admin
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'Category Management';
$errors = [];

$db = new Database();
$conn = $db->getConnection();

// Handle delete
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $csrf_token = $_GET['token'] ?? '';
    
    if (verifyCSRFToken($csrf_token)) {
        try {
            // Check if category has portfolio items
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM portfolio WHERE category_id = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetch()['count'];
            
            if ($count > 0) {
                setFlash('error', 'Cannot delete category that has portfolio items.');
            } else {
                $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
                $stmt->execute([$id]);
                setFlash('success', 'Category deleted successfully!');
            }
        } catch (PDOException $e) {
            setFlash('error', 'Failed to delete category.');
        }
    }
    redirect(ADMIN_URL . '/categories/manage.php');
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $id = (int)($_POST['id'] ?? 0);
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        $errors[] = 'Invalid request.';
    }
    
    if (empty($name)) {
        $errors[] = 'Category name is required.';
    }
    
    if (empty($errors)) {
        $slug = strtolower(str_replace(' ', '-', $name));
        
        try {
            if ($id > 0) {
                // Update
                $stmt = $conn->prepare("UPDATE categories SET name = ?, slug = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $id]);
                setFlash('success', 'Category updated successfully!');
            } else {
                // Insert
                $stmt = $conn->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
                $stmt->execute([$name, $slug]);
                setFlash('success', 'Category added successfully!');
            }
            redirect(ADMIN_URL . '/categories/manage.php');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $errors[] = 'A category with this name already exists.';
            } else {
                $errors[] = 'Failed to save category.';
            }
        }
    }
}

// Get edit category if id provided
$editCategory = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    try {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$editId]);
        $editCategory = $stmt->fetch();
    } catch (PDOException $e) {
        // Ignore
    }
}

// Get all categories
try {
    $stmt = $conn->query("
        SELECT c.*, COUNT(p.id) as item_count 
        FROM categories c 
        LEFT JOIN portfolio p ON c.id = p.category_id 
        GROUP BY c.id 
        ORDER BY c.name
    ");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
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
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul style="margin: 0; padding-left: 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 25px;">
                    <!-- Add/Edit Form -->
                    <div class="form-card">
                        <h2>
                            <?php echo $editCategory ? '<i class="fas fa-edit"></i> Edit Category' : '<i class="fas fa-plus"></i> Add Category'; ?>
                        </h2>
                        
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="id" value="<?php echo $editCategory['id'] ?? 0; ?>">
                            
                            <div class="form-group">
                                <label for="name">Category Name *</label>
                                <input type="text" id="name" name="name" required 
                                       value="<?php echo htmlspecialchars($editCategory['name'] ?? ''); ?>"
                                       placeholder="e.g., Nature, Wildlife">
                            </div>
                            
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?php echo $editCategory ? 'Update' : 'Add'; ?>
                                </button>
                                <?php if ($editCategory): ?>
                                    <a href="manage.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Categories List -->
                    <div class="data-table">
                        <div class="table-header">
                            <h2><i class="fas fa-tags"></i> All Categories</h2>
                        </div>
                        
                        <?php if (count($categories) > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Items</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                                            <td><?php echo htmlspecialchars($category['slug']); ?></td>
                                            <td><?php echo $category['item_count']; ?></td>
                                            <td class="table-actions">
                                                <a href="?edit=<?php echo $category['id']; ?>" class="btn btn-icon btn-edit" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($category['item_count'] == 0): ?>
                                                    <a href="?delete=1&id=<?php echo $category['id']; ?>&token=<?php echo $csrf_token; ?>" 
                                                       class="btn btn-icon btn-delete" 
                                                       title="Delete"
                                                       onclick="return confirm('Are you sure?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-tags"></i>
                                <h3>No categories yet</h3>
                                <p>Add your first category to organize portfolio items</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
