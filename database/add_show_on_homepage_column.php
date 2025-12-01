<?php
require_once '../config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Check if column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `portfolio_images` LIKE 'show_on_homepage'");
    $stmt->execute();
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $sql = "ALTER TABLE `portfolio_images` ADD COLUMN `show_on_homepage` TINYINT(1) DEFAULT 0 AFTER `display_order`";
        $pdo->exec($sql);
        echo "Column 'show_on_homepage' added successfully to 'portfolio_images' table.\n";
    } else {
        echo "Column 'show_on_homepage' already exists.\n";
    }
    
} catch(PDOException $e) {
    echo "Error updating table: " . $e->getMessage() . "\n";
}
?>
