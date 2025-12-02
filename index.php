<?php
/**
 * Homepage - Osem Clicks Photography Portfolio
 * Dynamic version with database-driven portfolio
 */
require_once 'config/config.php';

$db = new Database();
$conn = $db->getConnection();

// Track visitor
trackVisitor($conn, 'home');

// Get categories for filter buttons
try {
    $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

// Get all portfolio items with category info
try {
    $stmt = $conn->prepare("
        SELECT p.*, c.slug as category_slug 
        FROM portfolio p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $portfolioItems = $stmt->fetchAll();
} catch (PDOException $e) {
    $portfolioItems = [];
}

// Get latest 3 blog posts for homepage
try {
    $stmt = $conn->query("SELECT * FROM blogs ORDER BY created_at DESC LIMIT 3");
    $latestBlogs = $stmt->fetchAll();
} catch (PDOException $e) {
    $latestBlogs = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Osem Clicks - Keerthan B Photography</title>
    <link rel="icon" href="<?php echo asset_url('images/Logo/osemclickslogo-black.ico'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/logo-fix.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/mobile.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            overflow-x: hidden;
        }

        #cover {
            background-color: rgb(36, 147, 221);
        }

        #cover:hover {
            background-color: rgb(143, 190, 222);
        }

        /* Style for the logo image */
        .logo {
            display: flex;
            align-items: center;
        }

        /* Stats Banner */
        .stats-banner {
            background-color: #fff;
            color: #333;
            padding: 80px 0;
            text-align: center;
        }
        
        .stats-subtitle {
            font-size: 1.2rem;
            margin-bottom: 50px;
            opacity: 0.9;
            color: #666;
        }
        
        .stats-title {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .stats-subtitle {
            font-size: 1.2rem;
            margin-bottom: 50px;
            opacity: 0.9;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        
        .stat-item {
            padding: 20px;
        }
        
        .stat-number {
            font-size: 3.5rem;
            font-weight: 700;
            color: #6c63ff;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stats-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        
        /* Services Section */
        .services-section {
            padding: 80px 0;
            background: #fff;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .service-card {
            background: #f8f9fa;
            padding: 40px 30px;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #eee;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            background: #fff;
            border-color: transparent;
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            background: #e0e0ff;
            color: #6c63ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 25px;
            transition: all 0.3s ease;
        }
        
        .service-card:hover .service-icon {
            background: #6c63ff;
            color: #fff;
            transform: scale(1.1);
        }
        
        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333;
        }
        
        .service-card p {
            color: #666;
            line-height: 1.6;
        }
        
        /* Latest Blogs Section */
        .latest-blogs-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .blogs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .blog-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .blog-image {
            height: 200px;
            overflow: hidden;
        }
        
        .blog-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .blog-card:hover .blog-image img {
            transform: scale(1.1);
        }
        
        .blog-content {
            padding: 25px;
        }
        
        .blog-content h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .blog-excerpt {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .blog-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #eee;
            color: #888;
            font-size: 0.9rem;
        }
        
        .read-more {
            color: #6c63ff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .read-more:hover {
            color: #5a52d5;
        }
        
        /* Final CTA */
        .final-cta {
            padding: 100px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            text-align: center;
        }
        
        .final-cta h2 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .final-cta p {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .btn-lg {
            padding: 15px 40px;
            font-size: 1.1rem;
            background: #fff;
            color: #6c63ff;
        }
        
        .btn-lg:hover {
            background: #f0f0f0;
            color: #5a52d5;
            transform: translateY(-3px);
        }
        
        @media (max-width: 768px) {
            .stats-title { font-size: 2rem; }
            .stat-number { font-size: 2.5rem; }
            .stats-actions { flex-direction: column; }
            .final-cta h2 { font-size: 2rem; }
        }

        .logo img {
            height: 64px;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <!-- Header & Navigation -->
    <header>
        <div class="container">
            <div class="logo">
                <img src="<?php echo asset_url('images/Logo/osemclickslogo-black.svg'); ?>" alt="Osem Clicks Logo">
                <h1>Osem Clicks</h1>
            </div>
            <nav>
                <div class="hamburger">
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
                <ul class="nav-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="<?php echo url('about.php'); ?>">About</a></li>
                    <li><a href="<?php echo url('services.php'); ?>">Services</a></li>
                    <li><a href="<?php echo url('portfolio.php'); ?>">Portfolio</a></li>
                    <li><a href="<?php echo url('blogs.php'); ?>">Blogs</a></li>
                    <li><a href="<?php echo url('contact.php'); ?>">Contact</a></li>
                    <li><a href="<?php echo url('faqs.php'); ?>">FAQs</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-background">
            <video class="hero-video desktop-video" autoplay muted loop playsinline>
                <source src="<?php echo asset_url('images/cover/cover-landscape.mp4'); ?>" type="video/mp4">
            </video>
            <video class="hero-video mobile-video" autoplay muted loop playsinline>
                <source src="<?php echo asset_url('images/cover/cover-vertical.mp4'); ?>" type="video/mp4">
            </video>
        </div>
        <div class="container">
            <div class="hero-content">
                <h1>Excellence In Every Frame</h1>
                <p>Photography, Videography and editing services by Osem Clicks</p>
                <a href="#portfolio" class="btn" id="cover">View Portfolio</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <p style="text-align: center; max-width: 700px; margin: 0 auto 50px; color: #666; font-size: 1.1rem;">
                Professional photography and videography services tailored to your needs
            </p>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <h3>Photography</h3>
                    <p>Professional photo shoots for portraits, events, products, and more</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>Videography</h3>
                    <p>Cinematic videos for weddings, events, and commercial projects</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h3>Photo Editing</h3>
                    <p>Expert retouching and enhancement to make your photos shine</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Event Coverage</h3>
                    <p>Complete coverage of your special moments and celebrations</p>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="<?php echo url('services.php'); ?>" class="btn btn-primary">
                    <i class="fas fa-info-circle"></i> View All Services
                </a>
            </div>
        </div>
    </section>

    <!-- Portfolio Section - MADE DYNAMIC -->
    <section id="portfolio" class="portfolio">
        <div class="container">
            <h2 class="section-title">Photographs</h2>
            <p style="text-align: center; max-width: 700px; margin: 0 auto 30px; color: #666; font-size: 1rem;">
                These photographs are free to use for any non-commercial purpose
            </p>
            
            <!-- Dynamic Category Filter Buttons -->
            <div class="portfolio-filter">
                <button class="filter-btn active" data-filter="all">All</button>
                <?php foreach ($categories as $category): ?>
                    <button class="filter-btn" data-filter="<?php echo htmlspecialchars($category['slug']); ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Dynamic Portfolio Grid -->
            <div class="portfolio-grid">
                <?php 
                // Fetch individual images marked for homepage
                try {
                    $stmt = $conn->prepare("
                        SELECT pi.*, p.title as project_title, c.slug as category_slug 
                        FROM portfolio_images pi
                        JOIN portfolio p ON pi.portfolio_id = p.id
                        LEFT JOIN categories c ON p.category_id = c.id
                        WHERE pi.show_on_homepage = 1
                        ORDER BY pi.created_at DESC
                    ");
                    $stmt->execute();
                    $homepageImages = $stmt->fetchAll();
                } catch (PDOException $e) {
                    $homepageImages = [];
                }
                
                if (count($homepageImages) > 0): ?>
                    <?php foreach ($homepageImages as $img): ?>
                        <div class="portfolio-item <?php echo htmlspecialchars($img['category_slug']); ?>" 
                             data-images='<?php echo htmlspecialchars(json_encode([$img['image_path']])); ?>'
                             data-title="<?php echo htmlspecialchars($img['project_title']); ?>"
                             data-description="">
                            <img src="<?php echo htmlspecialchars($img['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($img['project_title']); ?>">
                            <div class="overlay">
                                <h3><?php echo htmlspecialchars($img['project_title']); ?></h3>
                                <p><?php echo htmlspecialchars($img['category_slug']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #999;">
                        <i class="fas fa-images" style="font-size: 3rem; margin-bottom: 15px;"></i>
                        <p>No images selected for homepage display.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (count($portfolioItems) > 12): ?>
                <div style="text-align: center; margin-top: 40px;">
                    <a href="portfolio.php" class="btn">
                        <i class="fas fa-images"></i> View Full Portfolio
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Lightbox -->
    <div id="lightbox" class="lightbox">
        <span class="close">&times;</span>
        <img class="lightbox-content" id="lightbox-img">
        <div id="lightbox-caption"></div>
    </div>

    <!-- Support Section -->
    <section id="support" class="support">
        <div class="container">
            <h2 class="section-title">Support My Work</h2>
            <p>If you appreciate my work and would like to support me, consider buying me a coffee!</p>
            <a href="http://buymeacoffee.com/osemclicks" target="_blank" class="coffee-btn">
                <i class="fas fa-mug-hot"></i> Buy Me a Coffee
            </a>
        </div>
    </section>

    <!-- Stats Banner Section -->
    <section class="stats-banner">
        <div class="container">
            <div class="stats-content">
                <h2 class="stats-title">Capturing Life's Precious Moments</h2>
                <p class="stats-subtitle">Professional Photography & Videography Services in Kundapura</p>
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number" data-target="5">0</div>
                        <div class="stat-label">Years Experience</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" data-target="500">0</div>
                        <div class="stat-label">Happy Clients</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" data-target="1000">0</div>
                        <div class="stat-label">Projects Done</div>
                    </div>
                </div>
                
                <div class="stats-actions">
                    <a href="<?php echo url('portfolio.php'); ?>" class="btn btn-primary">View Portfolio</a>
                    <a href="<?php echo url('contact.php'); ?>" class="btn btn-secondary">Get in Touch</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Latest Blogs Section -->
    <?php if (count($latestBlogs) > 0): ?>
    <section class="latest-blogs-section">
        <div class="container">
            <h2 class="section-title">Latest from Our Blog</h2>
            <p style="text-align: center; max-width: 700px; margin: 0 auto 50px; color: #666; font-size: 1.1rem;">
                Tips, tutorials, and stories from our photography journey
            </p>
            
            <div class="blogs-grid">
                <?php foreach ($latestBlogs as $blog): ?>
                    <div class="blog-card">
                        <div class="blog-image">
                            <img src="<?php echo asset_url($blog['cover_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($blog['title']); ?>">
                        </div>
                        <div class="blog-content">
                            <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
                            <p class="blog-excerpt"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                            <div class="blog-meta">
                                <span><i class="far fa-calendar"></i> <?php echo formatDate($blog['created_at']); ?></span>
                                <a href="blog-detail.php?id=<?php echo $blog['id']; ?>" class="read-more">
                                    Read More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="<?php echo url('blogs.php'); ?>" class="btn btn-primary">
                    <i class="fas fa-blog"></i> View All Blogs
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Final CTA Section -->
    <section class="final-cta">
        <div class="container">
            <h2>Ready to Capture Your Moments?</h2>
            <p>Let's create something beautiful together</p>
            <a href="<?php echo url('contact.php'); ?>" class="btn btn-lg">
                <i class="fas fa-envelope"></i> Get in Touch
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h3>Osem Clicks</h3>
                    <p>© 2025 Osem Clicks. All Rights Reserved.</p>
                </div>
                <div class="footer-social">
                    <a href="https://www.instagram.com/osemclicks" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://whatsapp.com/channel/0029VabxCHrIN9iuL77FOL18" target="_blank"><i
                            class="fa-brands fa-whatsapp"></i>
                    </a>
                    <a href="https://youtube.com/@osemclicks" target="_blank"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </footer>



    <script src="<?php echo asset_url('js/script.js'); ?>"></script>

    <script>
        // Stats Counter Animation
        const statsSection = document.querySelector('.stats-banner');
        const stats = document.querySelectorAll('.stat-number');
        let started = false;

        const startCounting = (entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !started) {
                    stats.forEach(stat => {
                        const target = +stat.getAttribute('data-target');
                        const duration = 2000; // 2 seconds
                        const increment = target / (duration / 16); // 60fps
                        
                        let current = 0;
                        const updateCount = () => {
                            current += increment;
                            if (current < target) {
                                stat.innerText = Math.ceil(current) + "+";
                                requestAnimationFrame(updateCount);
                            } else {
                                stat.innerText = target + "+";
                            }
                        };
                        updateCount();
                    });
                    started = true;
                }
            });
        };

        const observer = new IntersectionObserver(startCounting, {
            root: null,
            threshold: 0.5
        });

        if (statsSection) {
            observer.observe(statsSection);
        }
    </script>
</body>

</html>
