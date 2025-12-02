<?php
/**
 * Contact / Know More Page
 */
require_once 'config/config.php';

$db = new Database();
$conn = $db->getConnection();

trackVisitor($conn, 'contact');

// Get gears
try {
    $stmt = $conn->query("SELECT * FROM gears ORDER BY display_order, name");
    $gears = $stmt->fetchAll();
} catch (PDOException $e) {
    $gears = [];
}

$csrf_token = generateCSRFToken();
$pageTitle = 'Contact Us - ' . SITE_NAME;
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
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Contact Section -->
    <section id="contact" class="contact" style="padding-top: 120px;">
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
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
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
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="<?php echo asset_url('js/script.js'); ?>"></script>
    <script src="<?php echo asset_url('js/contact.js'); ?>"></script>
</body>
</html>
