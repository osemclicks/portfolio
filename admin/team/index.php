<?php
/**
 * Team Management - List
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'Manage Team';
$db = new Database();
$conn = $db->getConnection();

// Handle Delete
if (isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (verifyCSRFToken($csrf_token)) {
        try {
            // Get image path to delete file
            $stmt = $conn->prepare("SELECT image_path FROM team_members WHERE id = ?");
            $stmt->execute([$deleteId]);
            $member = $stmt->fetch();
            
            if ($member) {
                if (file_exists('../../' . $member['image_path'])) {
                    unlink('../../' . $member['image_path']);
                }
                
                $stmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
                $stmt->execute([$deleteId]);
                setFlash('success', 'Team member deleted successfully.');
            }
        } catch (PDOException $e) {
            setFlash('error', 'Failed to delete team member.');
        }
    } else {
        setFlash('error', 'Invalid request.');
    }
    redirect(ADMIN_URL . '/team/index.php');
}

// Get Team Members
try {
    $stmt = $conn->query("SELECT * FROM team_members ORDER BY display_order ASC, created_at DESC");
    $teamMembers = $stmt->fetchAll();
} catch (PDOException $e) {
    $teamMembers = [];
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
                <div class="page-header">
                    <h2><i class="fas fa-users"></i> Manage Team</h2>
                    <a href="add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Member</a>
                </div>
                
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['flash_type']; ?>">
                        <?php echo $_SESSION['flash_message']; ?>
                        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($teamMembers)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No team members found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($teamMembers as $member): ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo asset_url($member['image_path']); ?>" 
                                                 alt="<?php echo htmlspecialchars($member['name']); ?>"
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                        </td>
                                        <td><?php echo htmlspecialchars($member['name']); ?></td>
                                        <td><?php echo htmlspecialchars($member['role']); ?></td>
                                        <td><?php echo $member['display_order']; ?></td>
                                        <td class="actions">
                                            <a href="edit.php?id=<?php echo $member['id']; ?>" class="btn-icon btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this member?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                <input type="hidden" name="delete_id" value="<?php echo $member['id']; ?>">
                                                <button type="submit" class="btn-icon btn-delete" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
