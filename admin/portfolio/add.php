<?php
/**
 * Add Portfolio Item - Admin
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'Add Portfolio Item';
$errors = [];
$success = '';

// Get categories
$db = new Database();
$conn = $db->getConnection();

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
    
    // Validation
    if (!verifyCSRFToken($csrf_token)) {
        $errors[] = 'Invalid request. Please try again.';
    }
    
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }
    
    if ($category_id === 0) {
        $errors[] = 'Please select a category.';
    }
    
    if (!isset($_FILES['project_images']) || empty($_FILES['project_images']['name'][0])) {
        $errors[] = 'At least one image is required.';
    }
    
    // If no errors, process upload and insert
    if (empty($errors)) {
        try {
            $conn->beginTransaction();

            // 1. Upload all images
            $uploadedImages = [];
            $files = $_FILES['project_images'];
            $fileCount = count($files['name']);
            
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
                        $uploadedImages[] = 'uploads/portfolio/' . $uploadResult['filename'];
                    } else {
                        throw new Exception("Error uploading file {$files['name'][$i]}: " . $uploadResult['message']);
                    }
                }
            }
            
            if (empty($uploadedImages)) {
                throw new Exception("No images were successfully uploaded.");
            }

            // 2. Insert Portfolio Item (using first image as cover)
            $coverImage = $uploadedImages[0];
            
            $stmt = $conn->prepare("
                INSERT INTO portfolio (title, image_path, category_id, description) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$title, $coverImage, $category_id, $description]);
            $portfolioId = $conn->lastInsertId();
            
            // 3. Insert into portfolio_images table
            $showOnHomepage = isset($_POST['show_on_homepage']) ? 1 : 0;
            
            $stmtImage = $conn->prepare("
                INSERT INTO portfolio_images (portfolio_id, image_path, display_order, show_on_homepage) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($uploadedImages as $index => $imagePath) {
                $stmtImage->execute([$portfolioId, $imagePath, $index, $showOnHomepage]);
            }
            
            $conn->commit();
            setFlash('success', 'Portfolio item added successfully!');
            redirect(ADMIN_URL . '/portfolio/index.php');
            
        } catch (Exception $e) {
            $conn->rollBack();
            $errors[] = $e->getMessage();
            
            // Cleanup uploaded files
            foreach ($uploadedImages as $img) {
                deleteFile('../' . $img); // Adjust path as needed based on where deleteFile looks
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
                    <h2><i class="fas fa-plus"></i> Add New Portfolio Item</h2>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label for="title">Title *</label>
                            <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Category *</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                            <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Images * (Select multiple, Max 5MB each)</label>
                            <input type="file" id="image" name="project_images[]" accept="image/*" multiple required>
                            <small class="form-text text-muted">The first selected image will be the cover image.</small>
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="checkbox-container">
                                <input type="checkbox" name="show_on_homepage" value="1" checked>
                                <span class="checkmark"></span>
                                Show uploaded photos on homepage "Photographs" section
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description (Optional)</label>
                            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Portfolio Item
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
