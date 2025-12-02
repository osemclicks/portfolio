<?php
require_once '../config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS `team_members` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `role` VARCHAR(255) NOT NULL,
        `image_path` VARCHAR(500) NOT NULL,
        `bio` TEXT,
        `instagram` VARCHAR(255),
        `facebook` VARCHAR(255),
        `twitter` VARCHAR(255),
        `linkedin` VARCHAR(255),
        `display_order` INT DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql);
    echo "Table 'team_members' created successfully.\n";
    
} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>
