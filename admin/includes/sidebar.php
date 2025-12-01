<?php
/**
 * Admin Sidebar Navigation
 */
?>
<div class="admin-sidebar">
    <div class="sidebar-header">
        <h2><?php echo SITE_NAME; ?></h2>
        <p>Admin Panel</p>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-item">
            <a href="<?php echo ADMIN_URL; ?>/dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <div class="nav-section-title">Content Management</div>
        
        <div class="nav-item">
            <a href="<?php echo ADMIN_URL; ?>/portfolio/index.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/portfolio/') !== false ? 'active' : ''; ?>">
                <i class="fas fa-images"></i>
                <span>Portfolio</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="<?php echo ADMIN_URL; ?>/categories/manage.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/categories/') !== false ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="<?php echo ADMIN_URL; ?>/blogs/index.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/blogs/') !== false ? 'active' : ''; ?>">
                <i class="fas fa-blog"></i>
                <span>Blogs</span>
            </a>
        </div>
        
        <div class="nav-section-title">Communications</div>
        
        <div class="nav-item">
            <a href="<?php echo ADMIN_URL; ?>/notifications/index.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/notifications/') !== false ? 'active' : ''; ?>">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
            </a>
        </div>
        
        <div class="nav-section-title">Website Content</div>
        
        <div class="nav-item">
            <a href="<?php echo ADMIN_URL; ?>/content/pages.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/content/') !== false && (strpos($_SERVER['PHP_SELF'], 'pages') !== false || strpos($_SERVER['PHP_SELF'], 'edit-section') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i>
                <span>Page Content</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="<?php echo ADMIN_URL; ?>/content/faqs.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/content/faqs') !== false ? 'active' : ''; ?>">
                <i class="fas fa-question-circle"></i>
                <span>FAQs</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="<?php echo ADMIN_URL; ?>/content/gears.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/content/gears') !== false ? 'active' : ''; ?>">
                <i class="fas fa-camera"></i>
                <span>Gears</span>
            </a>
        </div>
        
        <div class="nav-section-title">Settings</div>
        
        <div class="nav-item">
            <a href="<?php echo SITE_URL; ?>" target="_blank" class="nav-link">
                <i class="fas fa-external-link-alt"></i>
                <span>View Website</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="<?php echo ADMIN_URL; ?>/logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</div>
