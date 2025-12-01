<?php
/**
 * Page Content Management - Admin CMS
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'Page Content Management';

$db = new Database();
$conn = $db->getConnection();

// Get all page content sections
try {
    $stmt = $conn->query("
        SELECT * FROM page_content 
        ORDER BY page, section
    ");
    $contentSections = $stmt->fetchAll();
    
    // Group by page
    $groupedContent = [];
    foreach ($contentSections as $section) {
        $groupedContent[$section['page']][] = $section;
    }
} catch (PDOException $e) {
    $groupedContent = [];
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
                <?php
                $flash = getFlash();
                if ($flash):
                ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>">
                        <?php echo htmlspecialchars($flash['message']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="data-table">
                    <div class="table-header">
                        <h2><i class="fas fa-file-alt"></i> Website Content Sections</h2>
                    </div>
                    
                    <?php if (count($groupedContent) > 0): ?>
                        <?php foreach ($groupedContent as $page => $sections): ?>
                            <div style="margin-bottom: 40px;">
                                <h3 style="text-transform: capitalize; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-left: 4px solid #6c63ff;">
                                    <i class="fas fa-file"></i> <?php echo htmlspecialchars($page); ?> Page
                                </h3>
                                
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Section</th>
                                            <th>Content Preview</th>
                                            <th>Last Updated</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sections as $section): ?>
                                            <tr>
                                                <td style="font-weight: 600; text-transform: capitalize;">
                                                    <?php echo htmlspecialchars(str_replace('_', ' ', $section['section'])); ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $preview = strip_tags($section['content']);
                                                    echo htmlspecialchars(substr($preview, 0, 100)) . (strlen($preview) > 100 ? '...' : ''); 
                                                    ?>
                                                </td>
                                                <td><?php echo formatDate($section['updated_at']); ?></td>
                                                <td class="table-actions">
                                                    <a href="edit-section.php?id=<?php echo $section['id']; ?>" 
                                                       class="btn btn-icon btn-edit" 
                                                       title="Edit Content">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-file-alt"></i>
                            <h3>No content sections found</h3>
                            <p>Content sections will be managed here</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
