<?php
/**
 * Gears Management - Admin
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'Gears Management';

$db = new Database();
$conn = $db->getConnection();

// Handle delete action
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $csrf_token = $_GET['token'] ?? '';
    
    if (verifyCSRFToken($csrf_token)) {
        try {
            $stmt = $conn->prepare("DELETE FROM gears WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'Gear deleted successfully!');
        } catch (PDOException $e) {
            setFlash('error', 'Failed to delete gear.');
        }
    }
    
    redirect(ADMIN_URL . '/content/gears.php');
}

// Handle add/edit gear
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $affiliate_link = sanitize($_POST['affiliate_link'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $csrf_token = $_POST['csrf_token'] ?? '';
    $errors = [];
    
    if (!verifyCSRFToken($csrf_token)) {
        $errors[] = 'Invalid request.';
    }
    
    if (empty($name)) {
        $errors[] = 'Gear name is required.';
    }
    
    if (empty($errors)) {
        $imageUrl = '';
        $oldImageUrl = '';
        
        // Get existing gear data if updating
        if ($id > 0) {
            try {
                $stmt = $conn->prepare("SELECT image_url FROM gears WHERE id = ?");
                $stmt->execute([$id]);
                $existingGear = $stmt->fetch();
                $oldImageUrl = $existingGear['image_url'] ?? '';
                $imageUrl = $oldImageUrl; // Keep existing image by default
            } catch (PDOException $e) {
                $errors[] = 'Failed to load existing gear data.';
            }
        }
        
        // Handle image upload if file provided
        if (empty($errors) && isset($_FILES['gear_image']) && $_FILES['gear_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = uploadImage($_FILES['gear_image'], GEAR_UPLOAD_DIR);
            
            if ($uploadResult['success']) {
                $imageUrl = 'uploads/gears/' . $uploadResult['filename'];
                
                // Delete old image if updating and old image exists
                if ($id > 0 && !empty($oldImageUrl) && strpos($oldImageUrl, 'uploads/') !== false) {
                    deleteFile(dirname(dirname(__DIR__)) . '/' . $oldImageUrl);
                }
            } else {
                $errors[] = $uploadResult['message'];
            }
        }
        
        if (empty($errors)) {
            try {
                if ($id > 0) {
                    // Update existing gear
                    $stmt = $conn->prepare("
                        UPDATE gears 
                        SET name = ?, image_url = ?, affiliate_link = ?, display_order = ? 
                        WHERE id = ?
                    ");
                    $stmt->execute([$name, $imageUrl, $affiliate_link, $display_order, $id]);
                    setFlash('success', 'Gear updated successfully!');
                } else {
                    // Add new gear
                    $stmt = $conn->prepare("
                        INSERT INTO gears (name, image_url, affiliate_link, display_order) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([$name, $imageUrl, $affiliate_link, $display_order]);
                    setFlash('success', 'Gear added successfully!');
                }
            } catch (PDOException $e) {
                $errors[] = 'Failed to save gear.';
            }
        }
    }
    
    if (!empty($errors)) {
        setFlash('error', implode(' ', $errors));
    }
    
    redirect(ADMIN_URL . '/content/gears.php');
}

// Get all gears
try {
    $stmt = $conn->query("SELECT * FROM gears ORDER BY display_order, name");
    $gears = $stmt->fetchAll();
} catch (PDOException $e) {
    $gears = [];
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?> Admin</title>
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/admin.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include '../includes/header.php'; ?>
            
            <div class="admin-content">
                <?php
                $flash = getFlash();
                if ($flash):
                ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>">
                        <?php echo htmlspecialchars($flash['message']); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Add New Gear Form -->
                <div class="form-card" style="margin-bottom: 30px;">
                    <h2><i class="fas fa-plus-circle"></i> Add New Gear</h2>
                    <form method="POST" id="gearForm" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="id" id="gear_id" value="0">
                        
                        <div class="form-group">
                            <label for="name">Gear Name *</label>
                            <input type="text" id="name" name="name" required placeholder="e.g., Canon EOS R5, iPhone 15 Pro">
                        </div>
                        
                        <div class="form-group" id="current-image-container" style="display: none;">
                            <label>Current Image</label>
                            <img src="" id="current_image_preview" class="image-preview" alt="Current Gear Image" style="max-width: 200px; border-radius: 8px;">
                        </div>
                        
                        <div class="form-group">
                            <label for="gear_image">Gear Image (Optional)</label>
                            <input type="file" id="gear_image" name="gear_image" accept="image/*">
                            <small style="color: #666;">Upload product image (Max 5MB). Leave empty to keep existing image.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="affiliate_link">Affiliate Link (Optional)</label>
                            <input type="url" id="affiliate_link" name="affiliate_link" placeholder="https://amzn.to/...">
                            <small style="color: #666;">Amazon affiliate link or product purchase link</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="display_order">Display Order</label>
                            <input type="number" id="display_order" name="display_order" value="0" min="0">
                            <small style="color: #666;">Lower numbers appear first</small>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <span id="btnText">Add Gear</span>
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Gears List -->
                <div class="data-table">
                    <div class="table-header">
                        <h2><i class="fas fa-camera"></i> Photography Gears</h2>
                    </div>
                    
                    <?php if (count($gears) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Image</th>
                                    <th>Gear Name</th>
                                    <th>Affiliate Link</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($gears as $gear): ?>
                                    <tr>
                                        <td><?php echo $gear['display_order']; ?></td>
                                        <td>
                                            <?php if (!empty($gear['image_url'])): ?>
                                                <img src="<?php echo asset_url($gear['image_url']); ?>" 
                                                     alt="<?php echo htmlspecialchars($gear['name']); ?>" 
                                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                            <?php else: ?>
                                                <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-image" style="color: #ccc; font-size: 24px;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-weight: 600;">
                                            <i class="fas fa-camera" style="color: #6c63ff; margin-right: 5px;"></i>
                                            <?php echo htmlspecialchars($gear['name']); ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($gear['affiliate_link'])): ?>
                                                <a href="<?php echo htmlspecialchars($gear['affiliate_link']); ?>" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-external-link-alt"></i> View Link
                                                </a>
                                            <?php else: ?>
                                                <span style="color: #999;">No link</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="table-actions">
                                            <button onclick='editGear(<?php echo json_encode($gear); ?>)' 
                                                    class="btn btn-icon btn-edit" 
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="?delete=1&id=<?php echo $gear['id']; ?>&token=<?php echo $csrf_token; ?>" 
                                               class="btn btn-icon btn-delete" 
                                               title="Delete"
                                               onclick="return confirm('Are you sure you want to delete this gear?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-camera"></i>
                            <h3>No gears added yet</h3>
                            <p>Add your photography equipment to showcase them on the contact page</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function editGear(gear) {
            document.getElementById('gear_id').value = gear.id;
            document.getElementById('name').value = gear.name;
            document.getElementById('affiliate_link').value = gear.affiliate_link || '';
            document.getElementById('display_order').value = gear.display_order;
            document.getElementById('btnText').textContent = 'Update Gear';
            
            // Show current image preview if exists
            const currentImageContainer = document.getElementById('current-image-container');
            const currentImagePreview = document.getElementById('current_image_preview');
            
            if (gear.image_url) {
                currentImagePreview.src = '<?php echo SITE_URL; ?>/' + gear.image_url;
                currentImageContainer.style.display = 'block';
            } else {
                currentImageContainer.style.display = 'none';
            }
            
            // Scroll to form
            document.getElementById('gearForm').scrollIntoView({ behavior: 'smooth' });
        }
        
        function resetForm() {
            document.getElementById('gearForm').reset();
            document.getElementById('gear_id').value = '0';
            document.getElementById('btnText').textContent = 'Add Gear';
            document.getElementById('current-image-container').style.display = 'none';
        }
    </script>
</body>
</html>
