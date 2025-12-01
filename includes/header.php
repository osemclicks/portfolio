<?php
/**
 * Shared Header Component
 */
?>
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
                <li><a href="<?php echo url('index.php'); ?>">Home</a></li>
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
