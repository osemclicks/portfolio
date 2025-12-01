<?php
/**
 * Add Blog Post - Admin
 * Note: For full TinyMCE integration, include TinyMCE CDN in head
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'Add Blog Post';
$errors = [];

$db = new Database();
$conn = $db->getConnection();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $content = $_POST['content'] ?? ''; // Don't sanitize HTML content
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        $errors[] = 'Invalid request.';
    }
    
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }
    
    if (empty($content)) {
        $errors[] = 'Content is required.';
    }
    
    if (!isset($_FILES['cover_image']) || $_FILES['cover_image']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Cover image is required.';
    }
    
    if (empty($errors)) {
        $uploadResult = uploadImage($_FILES['cover_image'], BLOG_UPLOAD_DIR);
        
        if ($uploadResult['success']) {
            $coverImage = 'uploads/blogs/' . $uploadResult['filename'];
            $excerpt = generateExcerpt($content);
            
            try {
                $stmt = $conn->prepare("
                    INSERT INTO blogs (title, cover_image, content, excerpt) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$title, $coverImage, $content, $excerpt]);
                
                setFlash('success', 'Blog post published successfully!');
                redirect(ADMIN_URL . '/blogs/index.php');
            } catch (PDOException $e) {
                $errors[] = 'Failed to save blog post.';
                deleteFile(BLOG_UPLOAD_DIR . $uploadResult['filename']);
            }
        } else {
            $errors[] = $uploadResult['message'];
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
    <script src="https://cdn.tiny.cloud/1/<?php echo TINYMCE_API_KEY; ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
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
                
                <div class="form-card" style="max-width: 1000px;">
                    <h2><i class="fas fa-plus"></i> Add New Blog Post</h2>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label for="title">Blog Title *</label>
                            <input type="text" id="title" name="title" required 
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="cover_image">Cover Image * (Max 5MB)</label>
                            <input type="file" id="cover_image" name="cover_image" accept="image/*" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="content">Blog Content *</label>
                            <textarea id="content" name="content" rows="15"><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Publish Blog Post
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
    
    <script>
        // Initialize TinyMCE rich text editor
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
            content_style: 'body { font-family: Poppins, sans-serif; font-size: 16px; }'
        });
    </script>
</body>
</html>
