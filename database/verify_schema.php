<?php
require_once '../config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->prepare("DESCRIBE portfolio_images");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (in_array('show_on_homepage', $columns)) {
        echo "VERIFICATION SUCCESS: 'show_on_homepage' column exists.\n";
    } else {
        echo "VERIFICATION FAILED: 'show_on_homepage' column MISSING.\n";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
