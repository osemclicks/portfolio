<?php
/**
 * Blogs Listing Page
 */
require_once 'config/config.php';

$db = new Database();
$conn = $db->getConnection();

trackVisitor($conn, 'blogs');

// Pagination
$currentPage = max(1, (int)($_GET['page'] ?? 1));

// Get total blog count
$stmtCount = $conn->query("SELECT COUNT(*) FROM blogs");
$totalItems = $stmtCount->fetchColumn();

$pagination = getPagination($totalItems, BLOGS_PER_PAGE, $currentPage);

// Get blog posts
$stmt = $conn->prepare("
    SELECT id, title, cover_image, excerpt, created_at 
    FROM blogs 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?
");
$stmt->execute([BLOGS_PER_PAGE, $pagination['offset']]);
$blogs = $stmt->fetchAll();

$pageTitle = 'Blogs - ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="icon" href="<?php echo asset_url('images/Logo/osemclickslogo-black.ico'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/mobile.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .blog-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .blog-card-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .blog-card-content {
            padding: 25px;
        }
        
        .blog-card h3 {
            font-size: 1.4rem;
            margin-bottom: 10px;
            color: #333;
        }
        
        .blog-card-date {
            color: #999;
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: block;
        }
        
        .blog-card-excerpt {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .blog-card .btn {
            background: #6c63ff;
            color: #fff;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="blogs" style="padding-top: 120px;">
        <div class="container">
            <h2 class="section-title">Our Blog</h2>
            <p style="text-align: center; max-width: 700px; margin: 0 auto 40px; color: #666; font-size: 1.1rem;">
                Photography tips, behind-the-scenes, and stories from our journey
            </p>
            
            <?php if (count($blogs) > 0): ?>
                <div class="blog-grid">
                    <?php foreach ($blogs as $blog): ?>
                        <div class="blog-card">
                            <img src="<?php echo asset_url($blog['cover_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                                 class="blog-card-image">
                            <div class="blog-card-content">
                                <span class="blog-card-date">
                                    <i class="fas fa-calendar"></i> <?php echo formatDate($blog['created_at']); ?>
                                </span>
                                <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
                                <p class="blog-card-excerpt">
                                    <?php echo htmlspecialchars($blog['excerpt'] ?? generateExcerpt($blog['content'] ?? '')); ?>
                                </p>
                                <a href="blog-detail.php?id=<?php echo $blog['id']; ?>" class="btn">
                                    Read More <i class="fas fa-arrow-right" style="margin-left: 5px; font-size: 0.8rem;"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="pagination">
                        <?php if ($pagination['has_prev']): ?>
                            <a href="?page=<?php echo $currentPage - 1; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <?php if ($i === $currentPage): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['has_next']): ?>
                            <a href="?page=<?php echo $currentPage + 1; ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 80px 20px; color: #999;">
                    <i class="fas fa-blog" style="font-size: 4rem; margin-bottom: 20px;"></i>
                    <h3>No blog posts yet</h3>
                    <p>Check back soon for photography tips and stories!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="<?php echo asset_url('js/script.js'); ?>"></script>
</body>
</html>
