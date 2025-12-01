<?php
/**
 * FAQs Management - Admin
 */
require_once '../../config/config.php';
requireLogin();

$pageTitle = 'FAQs Management';

$db = new Database();
$conn = $db->getConnection();

// Handle delete action
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $csrf_token = $_GET['token'] ?? '';
    
    if (verifyCSRFToken($csrf_token)) {
        try {
            $stmt = $conn->prepare("DELETE FROM faqs WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'FAQ deleted successfully!');
        } catch (PDOException $e) {
            setFlash('error', 'Failed to delete FAQ.');
        }
    }
    
    redirect(ADMIN_URL . '/content/faqs.php');
}

// Handle add/edit FAQ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $question = sanitize($_POST['question'] ?? '');
    $answer = sanitize($_POST['answer'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $display_order = (int)($_POST['display_order'] ?? 0);
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        setFlash('error', 'Invalid request.');
    } elseif (empty($question) || empty($answer)) {
        setFlash('error', 'Question and answer are required.');
    } else {
        try {
            if ($id > 0) {
                // Update existing FAQ
                $stmt = $conn->prepare("
                    UPDATE faqs 
                    SET question = ?, answer = ?, is_active = ?, display_order = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$question, $answer, $is_active, $display_order, $id]);
                setFlash('success', 'FAQ updated successfully!');
            } else {
                // Add new FAQ
                $stmt = $conn->prepare("
                    INSERT INTO faqs (question, answer, is_active, display_order) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$question, $answer, $is_active, $display_order]);
                setFlash('success', 'FAQ added successfully!');
            }
        } catch (PDOException $e) {
            setFlash('error', 'Failed to save FAQ.');
        }
    }
    
    redirect(ADMIN_URL . '/content/faqs.php');
}

// Get all FAQs
try {
    $stmt = $conn->query("SELECT * FROM faqs ORDER BY display_order, id");
    $faqs = $stmt->fetchAll();
} catch (PDOException $e) {
    $faqs = [];
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
                
                <!-- Add New FAQ Form -->
                <div class="form-card" style="margin-bottom: 30px;">
                    <h2><i class="fas fa-plus-circle"></i> Add New FAQ</h2>
                    <form method="POST" id="faqForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="id" id="faq_id" value="0">
                        
                        <div class="form-group">
                            <label for="question">Question *</label>
                            <input type="text" id="question" name="question" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="answer">Answer *</label>
                            <textarea id="answer" name="answer" rows="4" required></textarea>
                        </div>
                        
                        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label for="display_order">Display Order</label>
                                <input type="number" id="display_order" name="display_order" value="0" min="0">
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_active" id="is_active" checked>
                                    Active (show on website)
                                </label>
                            </div>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <span id="btnText">Add FAQ</span>
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- FAQs List -->
                <div class="data-table">
                    <div class="table-header">
                        <h2><i class="fas fa-question-circle"></i> All FAQs</h2>
                    </div>
                    
                    <?php if (count($faqs) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Question</th>
                                    <th>Answer Preview</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($faqs as $faq): ?>
                                    <tr>
                                        <td><?php echo $faq['display_order']; ?></td>
                                        <td style="font-weight: 600;">
                                            <?php echo htmlspecialchars($faq['question']); ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $preview = htmlspecialchars($faq['answer']);
                                            echo substr($preview, 0, 80) . (strlen($preview) > 80 ? '...' : '');
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $faq['is_active'] ? 'badge-success' : 'badge-secondary'; ?>">
                                                <?php echo $faq['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <button onclick='editFAQ(<?php echo json_encode($faq); ?>)' 
                                                    class="btn btn-icon btn-edit" 
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="?delete=1&id=<?php echo $faq['id']; ?>&token=<?php echo $csrf_token; ?>" 
                                               class="btn btn-icon btn-delete" 
                                               title="Delete"
                                               onclick="return confirm('Are you sure you want to delete this FAQ?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-question-circle"></i>
                            <h3>No FAQs yet</h3>
                            <p>Add your first FAQ to get started</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function editFAQ(faq) {
            document.getElementById('faq_id').value = faq.id;
            document.getElementById('question').value = faq.question;
            document.getElementById('answer').value = faq.answer;
            document.getElementById('display_order').value = faq.display_order;
            document.getElementById('is_active').checked = faq.is_active == 1;
            document.getElementById('btnText').textContent = 'Update FAQ';
            
            // Scroll to form
            document.getElementById('faqForm').scrollIntoView({ behavior: 'smooth' });
        }
        
        function resetForm() {
            document.getElementById('faqForm').reset();
            document.getElementById('faq_id').value = '0';
            document.getElementById('btnText').textContent = 'Add FAQ';
        }
    </script>
</body>
</html>
