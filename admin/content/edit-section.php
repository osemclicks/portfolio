<?php
/**
 * Edit Page Content Section - Admin CMS
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'Edit Content Section';
$errors = [];

$db = new Database();
$conn = $db->getConnection();

// Get section ID
$sectionId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($sectionId <= 0) {
    setFlash('error', 'Invalid section ID.');
    redirect(ADMIN_URL . '/content/pages.php');
}

// Fetch section data
try {
    $stmt = $conn->prepare("SELECT * FROM page_content WHERE id = ?");
    $stmt->execute([$sectionId]);
    $section = $stmt->fetch();
    
    if (!$section) {
        setFlash('error', 'Content section not found.');
        redirect(ADMIN_URL . '/content/pages.php');
    }
} catch (PDOException $e) {
    setFlash('error', 'Failed to load content section.');
    redirect(ADMIN_URL . '/content/pages.php');
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
                <!-- Breadcrumb Navigation -->
                <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px; color: #666; font-size: 14px;">
                        <a href="pages.php" style="color: #6c63ff; text-decoration: none; display: flex; align-items: center;">
                            <i class="fas fa-file-alt"></i>
                            <span style="margin-left: 5px;">Page Content</span>
                        </a>
                        <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                        <span style="text-transform: capitalize; font-weight: 600;">
                            <?php echo htmlspecialchars($section['page']); ?> Page
                        </span>
                        <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                        <span style="text-transform: capitalize;">
                            <?php echo htmlspecialchars(str_replace('_', ' ', $section['section'])); ?>
                        </span>
                    </div>
                </div>
                
                <div class="form-card" style="max-width: 1000px;">
                    <h2>
                        <i class="fas fa-edit"></i> 
                        Edit: <?php echo htmlspecialchars(str_replace('_', ' ', $section['section'])); ?>
                    </h2>
                    
                    <p style="color: #666; margin-bottom: 30px;">
                        <strong>Page:</strong> <?php echo htmlspecialchars(ucfirst($section['page'])); ?> | 
                        <strong>Last Updated:</strong> <?php echo formatDate($section['updated_at']); ?>
                    </p>
                    
                    <form method="POST" action="update-section.php">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="section_id" value="<?php echo $section['id']; ?>">
                        
                        <div class="form-group">
                            <label for="content">
                                Section Content *
                                <small style="display: block; margin-top: 5px; color: #666; font-weight: normal;">
                                    Use the editor to format your content with headings, lists, links, and more.
                                </small>
                            </label>
                            <textarea id="content" name="content" rows="15" required><?php echo htmlspecialchars($section['content']); ?></textarea>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="pages.php" class="btn btn-secondary">
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
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | removeformat | code',
            content_style: 'body { font-family: Poppins, sans-serif; font-size: 16px; line-height: 1.8; }',
            branding: false,
            promotion: false
        });
    </script>
</body>
</html>
