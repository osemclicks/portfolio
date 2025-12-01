<?php
/**
 * Services Page
 */
require_once 'config/config.php';

$db = new Database();
$conn = $db->getConnection();

trackVisitor($conn, 'services');

// Get services content
try {
    $stmt = $conn->prepare("
        SELECT section, content 
        FROM page_content 
        WHERE page = 'services'
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    
    $services = [];
    foreach ($rows as $row) {
        $services[$row['section']] = $row['content'];
    }
} catch (PDOException $e) {
    $services = [];
}

$serviceList = [
    [
        'icon' => 'fa-calendar-alt',
        'title' => 'Event Shoots',
        'key' => 'event_shoots'
    ],
    [
        'icon' => 'fa-box',
        'title' => 'Product Shoots',
        'key' => 'product_shoots'
    ],
    [
        'icon' => 'fa-video',
        'title' => 'Content Creation Support',
        'key' => 'content_creation'
    ],
    [
        'icon' => 'fa-building',
        'title' => 'Brand & Corporate Visuals',
        'key' => 'brand_corporate'
    ],
    [
        'icon' => 'fa-sliders-h',
        'title' => 'Editing & Post-Production',
        'key' => 'editing_post'
    ],
    [
        'icon' => 'fa-film',
        'title' => 'Specialized / Cinematic Services',
        'key' => 'cinematic'
    ]
];

$pageTitle = 'Services - ' . SITE_NAME;
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
    <style>
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .service-card {
            background: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #fff;
        }
        
        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333;
        }
        
        .service-card p {
            color: #666;
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="services" style="padding-top: 120px;">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <p style="text-align: center; max-width: 700px; margin: 0 auto 40px; font-size: 1.1rem; color: #666;">
                Professional photography, videography, and post-production services tailored to your needs
            </p>
            
            <div class="services-grid">
                <?php foreach ($serviceList as $service): ?>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas <?php echo $service['icon']; ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                        <p><?php echo htmlspecialchars($services[$service['key']] ?? 'Service description coming soon.'); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="<?php echo asset_url('js/script.js'); ?>"></script>
</body>
</html>
