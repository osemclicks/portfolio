<?php
/**
 * Portfolio Detail Page
 */
require_once 'config/config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    header('Location: portfolio.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

trackVisitor($conn, 'portfolio-detail');

// Get portfolio item
$stmt = $conn->prepare("
    SELECT p.*, c.name as category_name, c.id as category_id 
    FROM portfolio p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ?
");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    header('Location: portfolio.php');
    exit;
}

// Get related items from same category
$stmtRelated = $conn->prepare("
    SELECT * FROM portfolio 
    WHERE category_id = ? AND id != ? 
    ORDER BY RAND() 
    LIMIT 3
");
$stmtRelated->execute([$item['category_id'], $id]);
$relatedItems = $stmtRelated->fetchAll();

$pageTitle = htmlspecialchars($item['title']) . ' - Portfolio - ' . SITE_NAME;
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
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section style="padding: 120px 0;">
        <div class="container">
            <div style="max-width: 1000px; margin: 0 auto;">
                <a href="portfolio.php" style="display: inline-flex; align-items: center; margin-bottom: 30px; color: #6c63ff; text-decoration: none;">
                    <i class="fas fa-arrow-left" style="margin-right: 10px;"></i> Back to Portfolio
                </a>
                
                <h1 style="font-size: 2.5rem; margin-bottom: 15px;"><?php echo htmlspecialchars($item['title']); ?></h1>
                <p style="color: #666; margin-bottom: 30px;">
                    <i class="fas fa-tag" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($item['category_name']); ?>
                    <span style="margin: 0 10px;">•</span>
                    <i class="fas fa-calendar" style="margin-right: 5px;"></i> <?php echo formatDate($item['created_at']); ?>
                </p>
                
                <div style="margin-bottom: 30px; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <img src="<?php echo asset_url($item['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                         style="width: 100%; height: auto; display: block;">
                </div>
                
                <?php if (!empty($item['description'])): ?>
                    <div style="background: #f8f9fa; padding: 25px; border-radius: 10px; margin-bottom: 40px;">
                        <h3 style="margin-bottom: 15px;">About this Project</h3>
                        <p style="line-height: 1.8; color: #555;"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                    </div>
                <?php endif; ?>

                <?php 
                // Get additional images
                $stmtImages = $conn->prepare("SELECT * FROM portfolio_images WHERE portfolio_id = ? ORDER BY display_order ASC");
                $stmtImages->execute([$id]);
                $additionalImages = $stmtImages->fetchAll();
                ?>

                <?php if (count($additionalImages) > 0): ?>
                    <h3 style="margin-bottom: 20px;">Project Gallery</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 50px;">
                        <?php foreach ($additionalImages as $img): ?>
                            <div style="border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                                <img src="<?php echo asset_url($img['image_path']); ?>" 
                                     alt="Project Image"
                                     style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s ease;"
                                     onmouseover="this.style.transform='scale(1.05)'"
                                     onmouseout="this.style.transform='scale(1)'">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (count($relatedItems) > 0): ?>
                    <h3 style="margin-top: 60px; margin-bottom: 30px; font-size: 1.8rem;">Related Work</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                        <?php foreach ($relatedItems as $related): ?>
                            <a href="portfolio-detail.php?id=<?php echo $related['id']; ?>" class="portfolio-item">
                                <img src="<?php echo asset_url($related['image_path']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                <div class="overlay">
                                    <h3><?php echo htmlspecialchars($related['title']); ?></h3>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="<?php echo asset_url('js/script.js'); ?>"></script>
</body>
</html>
