<?php
/**
 * About Us Page
 */
require_once 'config/config.php';

$db = new Database();
$conn = $db->getConnection();

// Track visitor
trackVisitor($conn, 'about');

// Get page content
try {
    $stmt = $conn->prepare("SELECT section, content FROM page_content WHERE page = 'about'");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    
    $content = [];
    foreach ($rows as $row) {
        $content[$row['section']] = $row['content'];
    }
} catch (PDOException $e) {
    $content = [
        'who_we_are' => 'Content not available.',
        'our_story' => 'Content not available.'
    ];
}

$pageTitle = 'About Us - ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="icon" href="<?php echo asset_url('images/Logo/osemclickslogo-black.ico'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/logo-fix.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- About Section -->
    <section id="about" class="about" style="padding-top: 120px;">
        <div class="container">
            <h2 class="section-title">About Osem Clicks</h2>
            
            <div class="about-content">
                <div class="about-image">
                    <img src="<?php echo asset_url('images/Admin/keerthan2.jpg'); ?>" alt="Keerthan B">
                </div>
                <div class="about-text">
                    <h3>Keerthan B</h3>
                    <p class="location"><i class="fas fa-map-marker-alt"></i> Kundapura, Udupi, Karnataka</p>
                    <p>Hello! I'm Keerthan, a passionate photographer based in the beautiful coastal town of Kundapura.
                        With my camera, I strive to capture the essence of moments, emotions, and stories that unfold
                        around us.</p>
                    <p>My journey in photography began with a simple curiosity that evolved into a profound passion. I
                        specialize in various photography styles, from capturing the raw emotions in portraits to the
                        serene beauty of nature.</p>
                    <p>Every photograph tells a story, and I'm here to tell yours.</p>
                    <div class="social-links">
                        <a href="https://www.instagram.com/keerthan___poojary" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.facebook.com/profile.php?id=100077017638867" target="_blank"><i class="fab fa-facebook"></i></a>
                        <a href="https://youtube.com/@keerthanpoojary_vlogs?si=0UImfSDFr1tQLKyL" target="_blank"><i class="fa-brands fa-youtube"></i></a>
                        <a href="https://x.com/keerthan__05?s=21" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                        <a href="http://wa.me/6364620304" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 80px;">
                <h3 style="font-size: 2rem; margin-bottom: 30px; text-align: center;">Who We Are</h3>
                <div style="max-width: 900px; margin: 0 auto; line-height: 1.8; white-space: pre-line;">
                    <?php echo htmlspecialchars($content['who_we_are'] ?? ''); ?>
                </div>
            </div>
            
            <div style="margin-top: 60px;">
                <h3 style="font-size: 2rem; margin-bottom: 30px; text-align: center;">Our Story</h3>
                <div style="max-width: 900px; margin: 0 auto; line-height: 1.8; white-space: pre-line;">
                    <?php echo htmlspecialchars($content['our_story'] ?? ''); ?>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="<?php echo asset_url('js/script.js'); ?>"></script>
</body>
</html>
