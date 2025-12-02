<?php
/**
 * FAQs Page
 */
require_once 'config/config.php';

$db = new Database();
$conn = $db->getConnection();

trackVisitor($conn, 'faqs');

// Get FAQs
try {
    $stmt = $conn->query("SELECT * FROM faqs WHERE is_active = 1 ORDER BY display_order, id");
    $faqs = $stmt->fetchAll();
} catch (PDOException $e) {
    $faqs = [];
}

// Get Gears
try {
    $stmt = $conn->query("SELECT * FROM gears ORDER BY display_order, name");
    $gears = $stmt->fetchAll();
} catch (PDOException $e) {
    $gears = [];
}

$pageTitle = 'FAQs - ' . SITE_NAME;
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
        .faq-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .faq-item {
            background: #fff;
            margin-bottom: 15px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .faq-question {
            padding: 20px 25px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: #333;
            transition: background-color 0.3s ease;
        }
        
        .faq-question:hover {
            background-color: #f8f9fa;
        }
        
        .faq-question i {
            color: #6c63ff;
            transition: transform 0.3s ease;
        }
        
        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }
        
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding: 0 25px;
        }
        
        .faq-item.active .faq-answer {
            max-height: 500px;
            padding: 0 25px 20px 25px;
        }
        
        .faq-answer p {
            color: #666;
            line-height: 1.8;
            margin: 0;
        }
        
        /* Gears Section */
        .gears-section {
            padding: 80px 0 60px;
            background: #f8f9fa;
        }
        
        .gears-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }
        
        .gear-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .gear-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .gear-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f0f0f0;
        }
        
        .gear-content {
            padding: 25px;
            text-align: center;
        }
        
        .gear-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        
        .gear-btn {
            display: inline-block;
            padding: 10px 24px;
            background: #6c63ff;
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        
        .gear-btn:hover {
            background: #5a52d5;
            transform: scale(1.05);
        }
        
        .gear-btn i {
            margin-left: 5px;
        }
        
        @media (max-width: 768px) {
            .gears-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="faqs" style="padding-top: 120px;">
        <div class="container">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p style="text-align: center; max-width: 700px; margin: 0 auto 50px; color: #666; font-size: 1.1rem;">
                Find answers to common questions about our services
            </p>
            
            <div class="faq-container">
                <?php if (count($faqs) > 0): ?>
                    <?php foreach ($faqs as $faq): ?>
                        <div class="faq-item">
                            <div class="faq-question">
                                <span><?php echo htmlspecialchars($faq['question']); ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p><?php echo nl2br(htmlspecialchars($faq['answer'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px 20px; color: #999;">
                        <i class="fas fa-question-circle" style="font-size: 3rem; margin-bottom: 20px;"></i>
                        <p>No FAQs available at the moment. Please check back later.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Gears Section -->
    <?php if (count($gears) > 0): ?>
    <section class="gears-section">
        <div class="container">
            <h2 class="section-title">Gears We Use</h2>
            <p style="text-align: center; max-width: 700px; margin: 0 auto; color: #666; font-size: 1.1rem;">
                Check out the photography equipment and tools we use
            </p>
            
            <div class="gears-grid">
                <?php foreach ($gears as $gear): ?>
                    <div class="gear-card">
                        <?php if (!empty($gear['image_url'])): ?>
                            <img src="<?php echo asset_url($gear['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($gear['name']); ?>" 
                                 class="gear-image">
                        <?php else: ?>
                            <div class="gear-image" style="display: flex; align-items: center; justify-content: center; background: #f0f0f0;">
                                <i class="fas fa-camera" style="font-size: 3rem; color: #ccc;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="gear-content">
                            <h3 class="gear-name"><?php echo htmlspecialchars($gear['name']); ?></h3>
                            <?php if (!empty($gear['affiliate_link'])): ?>
                                <a href="<?php echo htmlspecialchars($gear['affiliate_link']); ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="gear-btn">
                                    VIEW PRODUCT <i class="fas fa-external-link-alt"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="<?php echo asset_url('js/script.js'); ?>"></script>
    <script>
        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', function() {
                const faqItem = this.parentElement;
                const isActive = faqItem.classList.contains('active');
                
                // Close all FAQs
                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Open clicked FAQ if it wasn't active
                if (!isActive) {
                    faqItem.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
