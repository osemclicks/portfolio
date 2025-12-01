<?php
/**
 * Blog Detail Page  
 */
require_once 'config/config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    header('Location: blogs.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

trackVisitor($conn, 'blog-detail');

// Get blog post
$stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->execute([$id]);
$blog = $stmt->fetch();

if (!$blog) {
    header('Location: blogs.php');
    exit;
}

$pageTitle = htmlspecialchars($blog['title']) . ' - Blog - ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="icon" href="<?php echo asset_url('images/Logo/osemclickslogo-black.ico'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .blog-content {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .blog-content img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .blog-content h2, .blog-content h3 {
            margin: 30px 0 15px;
        }
        
        .blog-content p {
            line-height: 1.8;
            margin-bottom: 20px;
            color: #444;
        }
        
        .blog-content ul, .blog-content ol {
            margin: 20px 0;
            padding-left: 30px;
        }
        
        .blog-content li {
            margin-bottom: 10px;
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <article style="padding: 120px 0;">
        <div class="container">
            <div class="blog-content">
                <a href="blogs.php" style="display: inline-flex; align-items: center; margin-bottom: 30px; color: #6c63ff; text-decoration: none;">
                    <i class="fas fa-arrow-left" style="margin-right: 10px;"></i> Back to Blogs
                </a>
                
                <h1 style="font-size: 2.5rem; margin-bottom: 15px;"><?php echo htmlspecialchars($blog['title']); ?></h1>
                <p style="color: #999; margin-bottom: 30px;">
                    <i class="fas fa-calendar"></i> <?php echo formatDate($blog['created_at'], 'F d, Y'); ?>
                </p>
                
                <div style="margin-bottom: 40px; border-radius: 15px; overflow: hidden;">
                    <img src="<?php echo asset_url($blog['cover_image']); ?>" 
                         alt="<?php echo htmlspecialchars($blog['title']); ?>"
                         style="width: 100%; height: auto; display: block;">
                </div>
                
                <div class="blog-content">
                    <?php echo $blog['content']; // This is already HTML from rich text editor ?>
                </div>
            </div>
        </div>
    </article>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="<?php echo asset_url('js/script.js'); ?>"></script>
</body>
</html>
