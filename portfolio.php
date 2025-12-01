<?php
/**
 * Portfolio Gallery Page
 */
require_once 'config/config.php';

$db = new Database();
$conn = $db->getConnection();

trackVisitor($conn, 'portfolio');

// Get search query
$search = sanitize($_GET['search'] ?? '');

// Get filter category
$filterCategory = (int)($_GET['category'] ?? 0);

// Pagination
$currentPage = max(1, (int)($_GET['page'] ?? 1));

// Build query
$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM portfolio p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if ($filterCategory > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $filterCategory;
}

// Get total count
$countSql = str_replace("SELECT p.*, c.name as category_name, c.slug as category_slug", "SELECT COUNT(*)", $sql);
$stmtCount = $conn->prepare($countSql);
$stmtCount->execute($params);
$totalItems = $stmtCount->fetchColumn();

// Get pagination data
$pagination = getPagination($totalItems, ITEMS_PER_PAGE, $currentPage);

// Add ordering and limit
$sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$params[] = ITEMS_PER_PAGE;
$params[] = $pagination['offset'];

// Get portfolio items
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$portfolioItems = $stmt->fetchAll();

// Get categories for filter
$stmtCat = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $stmtCat->fetchAll();

$pageTitle = 'Portfolio - ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="icon" href="<?php echo asset_url('images/Logo/osemclickslogo-black.ico'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .search-bar {
            max-width: 600px;
            margin: 0 auto 30px;
            position: relative;
        }
        
        .search-bar input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            font-size: 1rem;
        }
        
        .search-bar button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: #6c63ff;
            color: #fff;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }
        
        .pagination a, .pagination span {
            padding: 10px 15px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }
        
        .pagination a:hover {
            background: #6c63ff;
            color: #fff;
            border-color: #6c63ff;
        }
        
        .pagination .active {
            background: #6c63ff;
            color: #fff;
            border-color: #6c63ff;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section id="portfolio" class="portfolio" style="padding-top: 120px;">
        <div class="container">
            <h2 class="section-title">Our Portfolio</h2>
            
            <!-- Search Bar -->
            <div class="search-bar">
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Search portfolio..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            
            <!-- Category Filter -->
            <div class="portfolio-filter">
                <a href="portfolio.php" class="filter-btn <?php echo $filterCategory === 0 ? 'active' : ''; ?>">All</a>
                <?php foreach ($categories as $category): ?>
                    <a href="?category=<?php echo $category['id']; ?>" 
                       class="filter-btn <?php echo $filterCategory === $category['id'] ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Portfolio Grid -->
            <?php if (count($portfolioItems) > 0): ?>
                <div class="portfolio-grid">
                    <?php foreach ($portfolioItems as $item): ?>
                        <a href="portfolio-detail.php?id=<?php echo $item['id']; ?>" class="portfolio-item" style="cursor: pointer; text-decoration: none;">
                            <img src="<?php echo asset_url($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <div class="overlay">
                                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                <p><?php echo htmlspecialchars($item['category_name']); ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="pagination">
                        <?php if ($pagination['has_prev']): ?>
                            <a href="?page=<?php echo $currentPage - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo $filterCategory > 0 ? '&category=' . $filterCategory : ''; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <?php if ($i === $currentPage): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo $filterCategory > 0 ? '&category=' . $filterCategory : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['has_next']): ?>
                            <a href="?page=<?php echo $currentPage + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo $filterCategory > 0 ? '&category=' . $filterCategory : ''; ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 80px 20px; color: #999;">
                    <i class="fas fa-images" style="font-size: 4rem; margin-bottom: 20px;"></i>
                    <h3>No portfolio items found</h3>
                    <p><?php echo !empty($search) ? 'Try a different search term' : 'Check back soon for new work!'; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="<?php echo asset_url('js/script.js'); ?>"></script>
</body>
</html>
