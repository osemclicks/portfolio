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
    
    // Handle image deletion
    if (isset($_POST['delete_image_id'])) {
        $deleteId = (int)$_POST['delete_image_id'];
        try {
            // Get image path
            $stmt = $conn->prepare("SELECT image_path FROM portfolio_images WHERE id = ? AND portfolio_id = ?");
            $stmt->execute([$deleteId, $id]);
            $imgToDelete = $stmt->fetch();
            
            if ($imgToDelete) {
                // Delete file
                if (file_exists('../../' . $imgToDelete['image_path'])) {
                    unlink('../../' . $imgToDelete['image_path']);
                }
                
                // Delete DB record
                $stmt = $conn->prepare("DELETE FROM portfolio_images WHERE id = ?");
                $stmt->execute([$deleteId]);
                
                setFlash('success', 'Image deleted successfully.');
            }
        } catch (PDOException $e) {
            setFlash('error', 'Failed to delete image.');
        }
        // Redirect to prevent form resubmission
        redirect(ADMIN_URL . '/portfolio/edit.php?id=' . $id);
    }

    if (empty($errors)) {
        $imagePath = $item['image_path'];
        $oldImagePath = $item['image_path'];
        
        try {
            $conn->beginTransaction();

            // 1. Handle Cover Image Update
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadResult = uploadImage($_FILES['image'], PORTFOLIO_UPLOAD_DIR);
                
                if ($uploadResult['success']) {
                    $imagePath = 'uploads/portfolio/' . $uploadResult['filename'];
                    
                    // Delete old cover image if it's in uploads directory
                    if (strpos($oldImagePath, 'uploads/') !== false && file_exists('../../' . $oldImagePath)) {
                        unlink('../../' . $oldImagePath);
                    }
                } else {
                    $errors[] = $uploadResult['message'];
                }
            }
            
            // 2. Handle Additional Images Upload
            if (empty($errors) && isset($_FILES['project_images']) && !empty($_FILES['project_images']['name'][0])) {
                $files = $_FILES['project_images'];
                $fileCount = count($files['name']);
                
                $stmtImage = $conn->prepare("
                    INSERT INTO portfolio_images (portfolio_id, image_path, display_order) 
                    VALUES (?, ?, ?)
                ");
                
                for ($i = 0; $i < $fileCount; $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $fileData = [
                            'name' => $files['name'][$i],
                            'type' => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error' => $files['error'][$i],
                            'size' => $files['size'][$i]
                        ];
                        
                        $uploadResult = uploadImage($fileData, PORTFOLIO_UPLOAD_DIR);
                        if ($uploadResult['success']) {
                            $newImagePath = 'uploads/portfolio/' . $uploadResult['filename'];
                            $stmtImage->execute([$id, $newImagePath, 0]);
                        }
                    }
                }
            }
            
            if (empty($errors)) {
                $stmt = $conn->prepare("
                    UPDATE portfolio 
                    SET title = ?, image_path = ?, category_id = ?, description = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$title, $imagePath, $category_id, $description, $id]);
                
                // Update show_on_homepage flags
                // First reset all for this portfolio
                $conn->prepare("UPDATE portfolio_images SET show_on_homepage = 0 WHERE portfolio_id = ?")->execute([$id]);
                
                if (isset($_POST['show_on_homepage']) && is_array($_POST['show_on_homepage'])) {
                    $updateStmt = $conn->prepare("UPDATE portfolio_images SET show_on_homepage = 1 WHERE id = ? AND portfolio_id = ?");
                    foreach ($_POST['show_on_homepage'] as $imgId => $val) {
                        $updateStmt->execute([$imgId, $id]);
                    }
                }
                
                $conn->commit();
                setFlash('success', 'Portfolio item updated successfully!');
                redirect(ADMIN_URL . '/portfolio/index.php');
            } else {
                $conn->rollBack();
            }
            
        } catch (Exception $e) {
            $conn->rollBack();
            $errors[] = 'Failed to update portfolio item: ' . $e->getMessage();
        }
    }
}

// Get additional images
try {
    $stmt = $conn->prepare("SELECT * FROM portfolio_images WHERE portfolio_id = ? ORDER BY display_order ASC, id ASC");
    $stmt->execute([$id]);
    $additionalImages = $stmt->fetchAll();
} catch (PDOException $e) {
    $additionalImages = [];
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
                            <label>Current Cover Image</label>
                            <img src="../../<?php echo htmlspecialchars($item['image_path']); ?>" 
                                 class="image-preview"
                                 alt="Current Cover">
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Change Cover Image (Optional)</label>
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>

                        <?php if (!empty($additionalImages)): ?>
                        <div class="form-group">
                            <label>Project Gallery</label>
                            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                                <?php foreach ($additionalImages as $img): ?>
                                    <div style="position: relative; width: 100px; height: 100px;">
                                        <img src="../../<?php echo htmlspecialchars($img['image_path']); ?>" 
                                             style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                                        <button type="submit" name="delete_image_id" value="<?php echo $img['id']; ?>"
                                                onclick="return confirm('Delete this image?');"
                                                style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; z-index: 10;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); padding: 2px; text-align: center;">
                                            <label style="color: white; font-size: 10px; cursor: pointer;">
                                                <input type="checkbox" name="show_on_homepage[<?php echo $img['id']; ?>]" value="1" <?php echo $img['show_on_homepage'] ? 'checked' : ''; ?>> Show
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="project_images">Add More Images (Select multiple)</label>
                            <input type="file" id="project_images" name="project_images[]" accept="image/*" multiple>
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
