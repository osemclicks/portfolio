<?php
/**
 * PHP Seed Script
 * Generates properly hashed passwords and seeds admin accounts
 * Run this file once to seed the admin accounts with hashed passwords
 * Visit: http://localhost/projects/portfolio/database/seed_admins.php
 */

require_once '../config/database.php';

// Admin credentials
$admins = [
    [
        'email' => 'osemclicks@gmail.com',
        'password' => 'heroes.verse57',
        'name' => 'Keerthan Poojary'
    ],
    [
        'email' => 'karthikbillava1107@gmail.com',
        'password' => 'heroes.verse58',
        'name' => 'Karthik Billava'
    ]
];

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    die("Database connection failed!");
}

echo "<h2>Seeding Admin Accounts</h2>";

foreach ($admins as $admin) {
    // Hash the password
    $hashedPassword = password_hash($admin['password'], PASSWORD_BCRYPT);
    
    try {
        // Check if admin already exists
        $checkStmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
        $checkStmt->execute([$admin['email']]);
        
        if ($checkStmt->rowCount() > 0) {
            // Update existing admin
            $stmt = $conn->prepare("UPDATE admins SET password = ?, name = ? WHERE email = ?");
            $stmt->execute([$hashedPassword, $admin['name'], $admin['email']]);
            echo "<p>✓ Updated admin: " . htmlspecialchars($admin['email']) . "</p>";
        } else {
            // Insert new admin
            $stmt = $conn->prepare("INSERT INTO admins (email, password, name) VALUES (?, ?, ?)");
            $stmt->execute([$admin['email'], $hashedPassword, $admin['name']]);
            echo "<p>✓ Created admin: " . htmlspecialchars($admin['email']) . "</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>✗ Error for " . htmlspecialchars($admin['email']) . ": " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<p><strong>Seeding complete!</strong></p>";
echo "<p>Admin 1: osemclicks@gmail.com / heroes.verse57</p>";
echo "<p>Admin 2: karthikbillava1107@gmail.com / heroes.verse58</p>";
echo "<hr>";
echo "<p><a href='../admin/login.php'>Go to Admin Login</a></p>";
