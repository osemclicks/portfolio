<?php
/**
 * Edit Blog Post - Admin
 * Note: For full TinyMCE integration, include TinyMCE CDN in head
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'Edit Blog Post';
$errors = [];
$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    setFlash('error', 'Invalid blog post.');
    redirect(ADMIN_URL . '/blogs/index.php');
}

$db = new Database();
$conn = $db->getConnection();

// Get blog post
try {
    $stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    $blog = $stmt->fetch();
    
    if (!$blog) {
        setFlash('error', 'Blog post not found.');
        redirect(ADMIN_URL . '/blogs/index.php');
    }
} catch (PDOException $e) {
    setFlash('error', 'Failed to load blog post.');
    redirect(ADMIN_URL . '/blogs/index.php');
}

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
    
    if (empty($errors)) {
        $coverImage = $blog['cover_image'];
        $oldCoverImage = $blog['cover_image'];
        
        // Check if new cover image uploaded
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = uploadImage($_FILES['cover_image'], BLOG_UPLOAD_DIR);
            
            if ($uploadResult['success']) {
                $coverImage = 'uploads/blogs/' . $uploadResult['filename'];
                
                // Delete old cover image if it's in uploads directory
                if (strpos($oldCoverImage, 'uploads/') !== false) {
                    deleteFile(dirname(dirname(__DIR__)) . '/' . $oldCoverImage);
                }
            } else {
                $errors[] = $uploadResult['message'];
            }
        }
        
        if (empty($errors)) {
            $excerpt = generateExcerpt($content);
            
            try {
                $stmt = $conn->prepare("
                    UPDATE blogs 
                    SET title = ?, cover_image = ?, content = ?, excerpt = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$title, $coverImage, $content, $excerpt, $id]);
                
                setFlash('success', 'Blog post updated successfully!');
                redirect(ADMIN_URL . '/blogs/index.php');
            } catch (PDOException $e) {
                $errors[] = 'Failed to update blog post.';
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
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/admin.css'); ?>">
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
                    <h2><i class="fas fa-edit"></i> Edit Blog Post</h2>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label for="title">Blog Title *</label>
                            <input type="text" id="title" name="title" required 
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? $blog['title']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Current Cover Image</label>
                            <img src="<?php echo asset_url($blog['cover_image']); ?>" 
                                 class="image-preview"
                                 alt="Current Cover">
                        </div>
                        
                        <div class="form-group">
                            <label for="cover_image">New Cover Image (Optional - leave empty to keep current)</label>
                            <input type="file" id="cover_image" name="cover_image" accept="image/*">
                        </div>
                        
                        <div class="form-group">
                            <label for="content">Blog Content *</label>
                            <textarea id="content" name="content" rows="15"><?php echo htmlspecialchars($_POST['content'] ?? $blog['content']); ?></textarea>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Blog Post
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
