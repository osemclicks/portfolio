<?php
/**
 * Edit Team Member - Admin
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'Edit Team Member';
$errors = [];
$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    setFlash('error', 'Invalid team member.');
    redirect(ADMIN_URL . '/team/index.php');
}

$db = new Database();
$conn = $db->getConnection();

// Get member
try {
    $stmt = $conn->prepare("SELECT * FROM team_members WHERE id = ?");
    $stmt->execute([$id]);
    $member = $stmt->fetch();
    
    if (!$member) {
        setFlash('error', 'Team member not found.');
        redirect(ADMIN_URL . '/team/index.php');
    }
} catch (PDOException $e) {
    setFlash('error', 'Database error.');
    redirect(ADMIN_URL . '/team/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $role = sanitize($_POST['role'] ?? '');
    $bio = sanitize($_POST['bio'] ?? '');
    $instagram = sanitize($_POST['instagram'] ?? '');
    $facebook = sanitize($_POST['facebook'] ?? '');
    $twitter = sanitize($_POST['twitter'] ?? '');
    $linkedin = sanitize($_POST['linkedin'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        $errors[] = 'Invalid request.';
    }
    
    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    
    if (empty($role)) {
        $errors[] = 'Role is required.';
    }
    
    if (empty($errors)) {
        $imagePath = $member['image_path'];
        
        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = uploadImage($_FILES['image'], '../../uploads/team/');
            
            if ($uploadResult['success']) {
                $imagePath = 'uploads/team/' . $uploadResult['filename'];
                
                // Delete old image
                if (file_exists('../../' . $member['image_path'])) {
                    unlink('../../' . $member['image_path']);
                }
            } else {
                $errors[] = $uploadResult['message'];
            }
        }
        
        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("
                    UPDATE team_members 
                    SET name = ?, role = ?, image_path = ?, bio = ?, instagram = ?, facebook = ?, twitter = ?, linkedin = ?, display_order = ?
                    WHERE id = ?
                ");
                $stmt->execute([$name, $role, $imagePath, $bio, $instagram, $facebook, $twitter, $linkedin, $display_order, $id]);
                
                setFlash('success', 'Team member updated successfully!');
                redirect(ADMIN_URL . '/team/index.php');
            } catch (PDOException $e) {
                $errors[] = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?> Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include '../includes/header.php'; ?>
            
            <div class="admin-content">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul style="margin: 0; padding-left: 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="form-card">
                    <h2><i class="fas fa-edit"></i> Edit Team Member</h2>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? $member['name']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Role *</label>
                            <input type="text" id="role" name="role" required value="<?php echo htmlspecialchars($_POST['role'] ?? $member['role']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Current Image</label>
                            <img src="../../<?php echo htmlspecialchars($member['image_path']); ?>" alt="Current" style="max-width: 150px; border-radius: 5px;">
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Change Image (Optional)</label>
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                        
                        <div class="form-group">
                            <label for="bio">Bio (Optional)</label>
                            <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($_POST['bio'] ?? $member['bio']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="display_order">Display Order</label>
                            <input type="number" id="display_order" name="display_order" value="<?php echo htmlspecialchars($_POST['display_order'] ?? $member['display_order']); ?>">
                        </div>
                        
                        <h3 style="margin-top: 20px; margin-bottom: 10px; font-size: 1.1rem;">Social Media Links (Optional)</h3>
                        
                        <div class="form-group">
                            <label for="instagram"><i class="fab fa-instagram"></i> Instagram URL</label>
                            <input type="url" id="instagram" name="instagram" value="<?php echo htmlspecialchars($_POST['instagram'] ?? $member['instagram']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="facebook"><i class="fab fa-facebook"></i> Facebook URL</label>
                            <input type="url" id="facebook" name="facebook" value="<?php echo htmlspecialchars($_POST['facebook'] ?? $member['facebook']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="twitter"><i class="fab fa-twitter"></i> Twitter/X URL</label>
                            <input type="url" id="twitter" name="twitter" value="<?php echo htmlspecialchars($_POST['twitter'] ?? $member['twitter']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="linkedin"><i class="fab fa-linkedin"></i> LinkedIn URL</label>
                            <input type="url" id="linkedin" name="linkedin" value="<?php echo htmlspecialchars($_POST['linkedin'] ?? $member['linkedin']); ?>">
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Member
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
