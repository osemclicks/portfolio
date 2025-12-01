<?php
/**
 * Edit Portfolio Item - Admin
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'Edit Portfolio Item';
$errors = [];
$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    setFlash('error', 'Invalid portfolio item.');
    redirect(ADMIN_URL . '/portfolio/index.php');
}

$db = new Database();
$conn = $db->getConnection();

// Get portfolio item
try {
    $stmt = $conn->prepare("SELECT * FROM portfolio WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    
    if (!$item) {
        setFlash('error', 'Portfolio item not found.');
        redirect(ADMIN_URL . '/portfolio/index.php');
    }
} catch (PDOException $e) {
    setFlash('error', 'Failed to load portfolio item.');
    redirect(ADMIN_URL . '/portfolio/index.php');
}

// Get categories
try {
    $stmt = $conn->query("SELECT id, name FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        $errors[] = 'Invalid request.';
    }
    
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }
    
    if ($category_id === 0) {
        $errors[] = 'Please select a category.';
    }
    
    if (empty($errors)) {
        $imagePath = $item['image_path'];
        $oldImagePath = $item['image_path'];
        
        // Check if new image uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = uploadImage($_FILES['image'], PORTFOLIO_UPLOAD_DIR);
            
            if ($uploadResult['success']) {
                $imagePath = 'uploads/portfolio/' . $uploadResult['filename'];
                
                // Delete old image if it's in uploads directory
                if (strpos($oldImagePath, 'uploads/') !== false) {
                    deleteFile(dirname(dirname(__DIR__)) . '/' . $oldImagePath);
                }
            } else {
                $errors[] = $uploadResult['message'];
            }
        }
        
        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("
                    UPDATE portfolio 
                    SET title = ?, image_path = ?, category_id = ?, description = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$title, $imagePath, $category_id, $description, $id]);
                
                setFlash('success', 'Portfolio item updated successfully!');
                redirect(ADMIN_URL . '/portfolio/index.php');
            } catch (PDOException $e) {
                $errors[] = 'Failed to update portfolio item.';
            }
        }
    }
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
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul style="margin: 0; padding-left: 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="form-card">
                    <h2><i class="fas fa-edit"></i> Edit Portfolio Item</h2>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label for="title">Title *</label>
                            <input type="text" id="title" name="title" required 
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? $item['title']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Category *</label>
                            <select id="category_id" name="category_id" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                            <?php echo (isset($_POST['category_id']) ? $_POST['category_id'] : $item['category_id']) == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Current Image</label>
                            <img src="../../<?php echo htmlspecialchars($item['image_path']); ?>" 
                                 class="image-preview"
                                 alt="Current">
                        </div>
                        
                        <div class="form-group">
                            <label for="image">New Image (Optional - leave empty to keep current)</label>
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description (Optional)</label>
                            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? $item['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Portfolio Item
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
