<?php
require_once '../config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Create portfolio_images table
    $sql = "CREATE TABLE IF NOT EXISTS `portfolio_images` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `portfolio_id` INT NOT NULL,
      `image_path` VARCHAR(500) NOT NULL,
      `display_order` INT DEFAULT 0,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`portfolio_id`) REFERENCES `portfolio`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql);
    echo "Table 'portfolio_images' created successfully.";
    
} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
