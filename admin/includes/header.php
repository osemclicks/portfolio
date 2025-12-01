<?php
/**
 * Admin Header
 */
?>
<div class="admin-header">
    <h1><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
    <div class="admin-user">
        <div class="user-info">
            <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
            <span class="user-role">Administrator</span>
        </div>
        <a href="<?php echo ADMIN_URL; ?>/logout.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>
