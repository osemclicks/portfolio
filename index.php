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

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <h2 class="section-title">About Me</h2>
            <div class="about-content">
                <div class="about-image">
                    <img src="<?php echo asset_url('images/Admin/keerthan2.jpg'); ?>" alt="Keerthan B" id="profile-img">
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
                        <a href=" https://www.instagram.com/keerthan___poojary" target="_blank"><i
                                class="fab fa-instagram"></i></a>
                        <a href="https://www.facebook.com/profile.php?id=100077017638867" target="_blank"><i
                                class="fab fa-facebook"></i></a>
                        <a href="https://youtube.com/@keerthanpoojary_vlogs?si=0UImfSDFr1tQLKyL
" target="_blank"><i class="fa-brands fa-youtube"></i></a>
                        <a href=" https://x.com/keerthan__05?s=21
" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                        <a href="http://wa.me/6364620304
" target="_blank"><i class="fa-brands fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section - MADE DYNAMIC -->
    <section id="portfolio" class="portfolio">
        <div class="container">
            <h2 class="section-title">Portfolio</h2>
            
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
                <?php if (count($portfolioItems) > 0): ?>
                    <?php foreach ($portfolioItems as $item): ?>
                        <div class="portfolio-item <?php echo htmlspecialchars($item['category_slug']); ?>">
                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <div class="overlay">
                                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                <p><?php echo htmlspecialchars($item['category_slug']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #999;">
                        <i class="fas fa-images" style="font-size: 3rem; margin-bottom: 15px;"></i>
                        <p>Portfolio items coming soon!</p>
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

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title">Get in Touch</h2>
            <div class="contact-content">
                <div class="contact-info">
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p>Studio: <a href="mailto:osemclicks@gmail.com">osemclicks@gmail.com</a></p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Phone</h3>
                            <p><a href="tel:+916364620304">+91 63646 20304</a></p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Location</h3>
                            <p>Osem Clicks Studio</p>
                            <p>Kundapura, Udupi, Karnataka</p>
                        </div>
                    </div>
                </div>
                <div class="contact-form">
                    <form id="contactForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" required>
                            <span class="error-message" id="name-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                            <span class="error-message" id="email-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone (Optional)</label>
                            <input type="tel" id="phone" name="phone">
                            <span class="error-message" id="phone-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                            <span class="error-message" id="message-error"></span>
                        </div>
                        <button type="submit" class="btn">Send Message</button>
                        <div id="form-success" class="success-message"></div>
                        <div id="form-error" class="error-message"></div>
                    </form>
                </div>
            </div>
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
    <script src="<?php echo asset_url('js/contact.js'); ?>"></script>
</body>

</html>
