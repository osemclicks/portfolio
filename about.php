<?php
/**
 * About Us Page
 */
require_once 'config/config.php';

$db = new Database();
$conn = $db->getConnection();

// Track visitor
trackVisitor($conn, 'about');

// Get page content
try {
    $stmt = $conn->prepare("SELECT section, content FROM page_content WHERE page = 'about'");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    
    $content = [];
    foreach ($rows as $row) {
        $content[$row['section']] = $row['content'];
    }
} catch (PDOException $e) {
    $content = [
        'who_we_are' => 'Content not available.',
        'our_story' => 'Content not available.'
    ];
}

$pageTitle = 'About Us - ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="icon" href="<?php echo asset_url('images/Logo/osemclickslogo-black.ico'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/logo-fix.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Team Section Side-by-Side Layout */
        .team-grid {
            display: flex;
            flex-direction: column;
            gap: 40px;
            margin-top: 40px;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 20px;
        }

        .team-card {
            display: flex;
            flex-direction: row;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            align-items: stretch;
            min-height: 350px;
        }

        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        .team-image {
            width: 40%;
            min-height: 100%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .team-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .team-card:hover .team-image img {
            transform: scale(1.05);
        }

        .team-info {
            padding: 40px;
            width: 60%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
        }

        .team-info h3 {
            font-size: 2.2rem;
            margin-bottom: 5px;
            color: #333;
            font-weight: 700;
        }

        .team-info .role {
            color: #6c63ff;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: inline-block;
        }

        .team-info .bio {
            font-size: 1rem;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.8;
        }

        .team-social {
            display: flex;
            gap: 15px;
            justify-content: flex-start;
        }

        .team-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background-color: #f8f9fa;
            color: #333;
            border-radius: 50%;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            border: 1px solid #eee;
        }

        .team-social a:hover {
            background-color: #6c63ff;
            color: #fff;
            border-color: #6c63ff;
            transform: translateY(-3px);
        }

        @media (max-width: 768px) {
            .team-card {
                flex-direction: column;
            }
            
            .team-image {
                width: 100%;
                height: 300px;
            }
            
            .team-info {
                width: 100%;
                padding: 30px;
                text-align: center;
            }
            
            .team-social {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- About Section -->
    <section id="about" class="about" style="padding-top: 120px;">
        <div class="container">
            <h2 class="section-title">Our Team</h2>
            
            <?php
            // Fetch team members
            try {
                $stmt = $conn->query("SELECT * FROM team_members ORDER BY display_order ASC, created_at DESC");
                $teamMembers = $stmt->fetchAll();
            } catch (PDOException $e) {
                $teamMembers = [];
            }
            ?>
            
            <?php if (empty($teamMembers)): ?>
                <p style="text-align: center;">Team members will be added soon.</p>
            <?php else: ?>
                <div class="team-grid">
                    <?php foreach ($teamMembers as $member): ?>
                        <div class="team-card">
                            <div class="team-image">
                                <img src="<?php echo asset_url($member['image_path']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                            </div>
                            <div class="team-info">
                                <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                                <p class="role"><?php echo htmlspecialchars($member['role']); ?></p>
                                <?php if (!empty($member['bio'])): ?>
                                    <p class="bio"><?php echo nl2br(html_entity_decode($member['bio'])); ?></p>
                                <?php endif; ?>
                                <div class="team-social">
                                    <?php if (!empty($member['instagram'])): ?>
                                        <a href="<?php echo htmlspecialchars($member['instagram']); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($member['facebook'])): ?>
                                        <a href="<?php echo htmlspecialchars($member['facebook']); ?>" target="_blank"><i class="fab fa-facebook"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($member['twitter'])): ?>
                                        <a href="<?php echo htmlspecialchars($member['twitter']); ?>" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($member['linkedin'])): ?>
                                        <a href="<?php echo htmlspecialchars($member['linkedin']); ?>" target="_blank"><i class="fab fa-linkedin"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 80px;">
                <h3 style="font-size: 2rem; margin-bottom: 30px; text-align: center;">Who We Are</h3>
                <div style="max-width: 900px; margin: 0 auto; line-height: 1.8; white-space: pre-line;">
                    <?php echo htmlspecialchars($content['who_we_are'] ?? ''); ?>
                </div>
            </div>
            
            <div style="margin-top: 60px;">
                <h3 style="font-size: 2rem; margin-bottom: 30px; text-align: center;">Our Story</h3>
                <div style="max-width: 900px; margin: 0 auto; line-height: 1.8; white-space: pre-line;">
                    <?php echo htmlspecialchars($content['our_story'] ?? ''); ?>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="<?php echo asset_url('js/script.js'); ?>"></script>
</body>
</html>
