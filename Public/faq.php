<?php
// Public/faq.php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database and auth - try multiple paths
$db_paths = [
    __DIR__ . '/../Include/db.php',
    __DIR__ . '/../includes/db.php',
    dirname(__DIR__) . '/Include/db.php',
    dirname(__DIR__) . '/includes/db.php',
    '../Include/db.php',
    '../includes/db.php'
];

$db_loaded = false;
foreach ($db_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $db_loaded = true;
        break;
    }
}

if (!$db_loaded) {
    die('Error: Database configuration file not found. Please check your Include/db.php or includes/db.php file exists.');
}

// Get database connection using getDB() function
try {
    $conn = getDB();
} catch (Exception $e) {
    die('Error: Database connection failed. ' . $e->getMessage());
}

$auth_paths = [
    __DIR__ . '/../Include/auth.php',
    __DIR__ . '/../includes/auth.php',
    dirname(__DIR__) . '/Include/auth.php',
    dirname(__DIR__) . '/includes/auth.php',
    '../Include/auth.php',
    '../includes/auth.php'
];

$auth_loaded = false;
foreach ($auth_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $auth_loaded = true;
        break;
    }
}

// Fallback auth functions if not loaded
if (!$auth_loaded || !function_exists('is_logged_in')) {
    function is_logged_in() {
        return isset($_SESSION['user_id']);
    }
    
    function get_current_user() {
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'] ?? 'User',
                'email' => $_SESSION['user_email'] ?? '',
                'role' => $_SESSION['user_role'] ?? 'customer'
            ];
        }
        return null;
    }
}

$user = get_current_user();

// Handle question submission
$question_success = false;
$question_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_question'])) {
    if (!is_logged_in()) {
        $question_error = 'You must be logged in to submit a question';
    } elseif (!$user || !isset($user['id'])) {
        $question_error = 'Invalid user session. Please log in again.';
    } else {
        $question = trim($_POST['question'] ?? '');
        
        if (empty($question)) {
            $question_error = 'Please enter a question';
        } elseif (strlen($question) < 10) {
            $question_error = 'Question must be at least 10 characters';
        } else {
            try {
                $user_id = intval($user['id']);
                $stmt = $conn->prepare("INSERT INTO faq_questions (user_id, question, status) VALUES (?, ?, 'new')");
                $stmt->bind_param("is", $user_id, $question);
                
                if ($stmt->execute()) {
                    $question_success = true;
                } else {
                    $question_error = 'Failed to submit question. Please try again.';
                }
                $stmt->close();
            } catch (Exception $e) {
                $question_error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// Handle rating submission (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rate_faq'])) {
    header('Content-Type: application/json');
    
    if (!is_logged_in()) {
        echo json_encode(['success' => false, 'message' => 'Must be logged in']);
        exit;
    }
    
    if (!$user || !isset($user['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid user session']);
        exit;
    }
    
    $faq_id = intval($_POST['faq_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $user_id = intval($user['id']);
    
    if ($faq_id > 0 && $rating >= 1 && $rating <= 5) {
        try {
            // Check if user already rated
            $stmt = $conn->prepare("SELECT id FROM faq_ratings WHERE faq_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $faq_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Update existing rating
                $stmt = $conn->prepare("UPDATE faq_ratings SET rating = ? WHERE faq_id = ? AND user_id = ?");
                $stmt->bind_param("iii", $rating, $faq_id, $user_id);
                $success = $stmt->execute();
            } else {
                // Insert new rating
                $stmt = $conn->prepare("INSERT INTO faq_ratings (faq_id, user_id, rating) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $faq_id, $user_id, $rating);
                $success = $stmt->execute();
            }
            
            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
    exit;
}

// Fetch all FAQs
$faqs = [];
$db_error = null;

try {
    $result = $conn->query("SELECT id, question, answer, tags FROM faqs ORDER BY created_at DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $faqs[] = $row;
        }
    }
} catch (Exception $e) {
    $db_error = "FAQ table not found or database error. Please run the database setup. Error: " . $e->getMessage();
}

// Get user's ratings if logged in
$user_ratings = [];
if (is_logged_in() && $user && isset($user['id'])) {
    try {
        $user_id = intval($user['id']);
        $stmt = $conn->prepare("SELECT faq_id, rating FROM faq_ratings WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $user_ratings[$row['faq_id']] = $row['rating'];
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        // Silently fail if ratings table doesn't exist
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - PowerHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Orbitron:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0a0e27; color: #ffffff; line-height: 1.6; padding-top: 80px; }
        .container { max-width: 1400px; margin: 0 auto; padding: 0 20px; }
        
        /* Header */
        .header { background: rgba(10, 14, 39, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(0, 217, 255, 0.1); padding: 20px 0; position: fixed; top: 0; left: 0; right: 0; z-index: 1000; }
        .nav { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-family: 'Orbitron', sans-serif; font-size: 28px; font-weight: 800; background: linear-gradient(135deg, #00d9ff 0%, #4c6fff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-decoration: none; }
        .nav-links { display: flex; gap: 40px; align-items: center; }
        .nav-links a { color: #a0aec0; text-decoration: none; font-weight: 500; transition: color 0.3s; position: relative; }
        .nav-links a:hover, .nav-links a.active { color: #00d9ff; }
        .nav-links a.active::after { content: ''; position: absolute; bottom: -5px; left: 0; right: 0; height: 2px; background: linear-gradient(135deg, #00d9ff 0%, #4c6fff 100%); }
        
        .cart-link { position: relative; }
        .cart-badge { position: absolute; top: -8px; right: -12px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; }
        
        /* Page Header */
        .page-header { padding: 80px 0 60px; background: linear-gradient(135deg, #0a0e27 0%, #1a1f3a 100%); border-bottom: 1px solid rgba(0, 217, 255, 0.1); }
        .page-header h1 { font-family: 'Orbitron', sans-serif; font-size: 48px; background: linear-gradient(135deg, #00d9ff 0%, #4c6fff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 15px; }
        .page-header p { color: #a0aec0; font-size: 18px; max-width: 700px; }
        
        /* Search */
        .faq-search { padding: 40px 0; }
        .search-box { position: relative; max-width: 600px; margin: 0 auto; }
        .search-box input { width: 100%; padding: 18px 50px 18px 20px; background: #131937; border: 1px solid rgba(0, 217, 255, 0.2); border-radius: 12px; color: #ffffff; font-size: 16px; }
        .search-box input:focus { outline: none; border-color: #00d9ff; box-shadow: 0 0 0 3px rgba(0, 217, 255, 0.1); }
        .search-icon { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); color: #a0aec0; font-size: 20px; }
        
        /* FAQ Content */
        .faq-content { padding: 40px 0 100px; }
        .faq-grid { display: grid; grid-template-columns: 250px 1fr; gap: 40px; }
        
        .faq-sidebar { background: #131937; border: 1px solid rgba(0, 217, 255, 0.1); border-radius: 20px; padding: 30px; height: fit-content; position: sticky; top: 120px; }
        .faq-sidebar h3 { font-family: 'Orbitron', sans-serif; font-size: 18px; margin-bottom: 20px; color: #00d9ff; }
        .category-list { list-style: none; }
        .category-list li { margin-bottom: 10px; }
        .category-list a { color: #a0aec0; text-decoration: none; padding: 8px 12px; display: block; border-radius: 8px; transition: all 0.3s; font-size: 14px; }
        .category-list a:hover { background: #1a2347; color: #00d9ff; }
        
        .faq-main { }
        .faq-item { background: #131937; border: 1px solid rgba(0, 217, 255, 0.1); border-radius: 20px; padding: 30px; margin-bottom: 20px; transition: all 0.3s; }
        .faq-item:hover { border-color: rgba(0, 217, 255, 0.3); }
        .faq-question { font-family: 'Orbitron', sans-serif; font-size: 20px; color: #00d9ff; margin-bottom: 15px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
        .faq-toggle { font-size: 24px; color: #a0aec0; transition: transform 0.3s; }
        .faq-item.open .faq-toggle { transform: rotate(180deg); }
        .faq-answer { color: #a0aec0; line-height: 1.8; display: none; padding-top: 15px; border-top: 1px solid rgba(0, 217, 255, 0.1); }
        .faq-item.open .faq-answer { display: block; }
        .faq-tags { margin-top: 15px; display: flex; flex-wrap: wrap; gap: 8px; }
        .faq-tag { background: rgba(0, 217, 255, 0.1); color: #00d9ff; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        
        /* Rating */
        .faq-rating { margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(0, 217, 255, 0.1); display: flex; align-items: center; gap: 15px; }
        .faq-rating-label { color: #a0aec0; font-size: 14px; }
        .star-rating { display: flex; gap: 5px; }
        .star { font-size: 24px; cursor: pointer; color: #a0aec0; transition: all 0.2s; }
        .star:hover, .star.active { color: #fbbf24; }
        .rating-message { color: #10b981; font-size: 14px; display: none; }
        
        /* Ask Question Form */
        .ask-question { background: #131937; border: 1px solid rgba(0, 217, 255, 0.1); border-radius: 20px; padding: 40px; margin-top: 60px; }
        .ask-question h2 { font-family: 'Orbitron', sans-serif; font-size: 28px; color: #00d9ff; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #ffffff; }
        .form-group textarea { width: 100%; padding: 15px; background: #1a2347; border: 1px solid rgba(0, 217, 255, 0.2); border-radius: 12px; color: #ffffff; font-family: 'Inter', sans-serif; font-size: 15px; min-height: 120px; resize: vertical; }
        .form-group textarea:focus { outline: none; border-color: #00d9ff; box-shadow: 0 0 0 3px rgba(0, 217, 255, 0.1); }
        
        .btn-submit { padding: 15px 40px; background: linear-gradient(135deg, #00d9ff 0%, #4c6fff 100%); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0, 217, 255, 0.4); }
        
        .success-message { background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid rgba(16, 185, 129, 0.3); }
        .error-message { background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid rgba(239, 68, 68, 0.3); }
        
        .login-prompt { background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 15px 20px; border-radius: 10px; border: 1px solid rgba(245, 158, 11, 0.3); }
        .login-prompt a { color: #00d9ff; text-decoration: underline; }
        
        .no-results { text-align: center; padding: 60px 20px; color: #a0aec0; }
        .no-results-icon { font-size: 60px; margin-bottom: 20px; opacity: 0.5; }
        
        .db-error { background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 30px; border-radius: 10px; margin: 40px 0; border: 1px solid rgba(239, 68, 68, 0.3); text-align: center; }
        
        /* Footer */
        .footer { background: #070a1f; padding: 40px 0; border-top: 1px solid rgba(0, 217, 255, 0.1); text-align: center; color: #a0aec0; margin-top: 80px; }
        
        @media (max-width: 968px) {
            .faq-grid { grid-template-columns: 1fr; }
            .faq-sidebar { position: static; }
            .page-header h1 { font-size: 36px; }
            .nav-links { gap: 20px; font-size: 14px; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="logo">PowerHub</a>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="products.php">Products</a>
                    <a href="faq.php" class="active">FAQ</a>
                    <a href="cart.php" class="cart-link">
                        Cart <span class="cart-badge" id="cartBadge">0</span>
                    </a>
                    <?php if (is_logged_in()): ?>
                        <a href="account.php">Account</a>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                        <a href="register.php">Register</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <section class="page-header">
            <div class="container">
                <h1>Frequently Asked Questions</h1>
                <p>Find answers to common questions about our power banks, charging technology, and more.</p>
            </div>
        </section>

        <section class="faq-search">
            <div class="container">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search FAQs (e.g., charging, capacity, travel)...">
                    <span class="search-icon">🔍</span>
                </div>
            </div>
        </section>

        <section class="faq-content">
            <div class="container">
                <?php if ($db_error): ?>
                    <div class="db-error">
                        <h3>⚠️ Database Error</h3>
                        <p><?php echo htmlspecialchars($db_error); ?></p>
                    </div>
                <?php else: ?>
                    <div class="faq-grid">
                        <aside class="faq-sidebar">
                            <h3>Categories</h3>
                            <ul class="category-list">
                                <li><a href="javascript:void(0)" onclick="filterByTag('all')">All Questions</a></li>
                                <li><a href="javascript:void(0)" onclick="filterByTag('charging')">Charging</a></li>
                                <li><a href="javascript:void(0)" onclick="filterByTag('capacity')">Capacity</a></li>
                                <li><a href="javascript:void(0)" onclick="filterByTag('safety')">Safety</a></li>
                                <li><a href="javascript:void(0)" onclick="filterByTag('travel')">Travel</a></li>
                                <li><a href="javascript:void(0)" onclick="filterByTag('technology')">Technology</a></li>
                            </ul>
                        </aside>

                        <div class="faq-main">
                            <div id="faqList">
                                <?php if (empty($faqs)): ?>
                                    <div class="no-results">
                                        <div class="no-results-icon">📋</div>
                                        <h3>No FAQs available yet</h3>
                                        <p>Check back soon for frequently asked questions!</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($faqs as $faq): ?>
                                        <div class="faq-item" data-tags="<?php echo htmlspecialchars($faq['tags'] ?? ''); ?>" data-faq-id="<?php echo $faq['id']; ?>">
                                            <div class="faq-question" onclick="toggleFaq(this)">
                                                <span><?php echo htmlspecialchars($faq['question']); ?></span>
                                                <span class="faq-toggle">▼</span>
                                            </div>
                                            <div class="faq-answer">
                                                <p><?php echo nl2br(htmlspecialchars($faq['answer'])); ?></p>
                                                
                                                <?php if (!empty($faq['tags'])): ?>
                                                    <div class="faq-tags">
                                                        <?php foreach (explode(',', $faq['tags']) as $tag): ?>
                                                            <span class="faq-tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="faq-rating">
                                                    <span class="faq-rating-label">Was this helpful?</span>
                                                    <?php if (is_logged_in()): ?>
                                                        <div class="star-rating" data-faq-id="<?php echo $faq['id']; ?>">
                                                            <?php 
                                                            $user_rating = $user_ratings[$faq['id']] ?? 0;
                                                            for ($i = 1; $i <= 5; $i++): 
                                                            ?>
                                                                <span class="star <?php echo $i <= $user_rating ? 'active' : ''; ?>" data-rating="<?php echo $i; ?>" onclick="rateFaq(<?php echo $faq['id']; ?>, <?php echo $i; ?>, this)">★</span>
                                                            <?php endfor; ?>
                                                        </div>
                                                        <span class="rating-message" id="rating-msg-<?php echo $faq['id']; ?>">✓ Thanks for your feedback!</span>
                                                    <?php else: ?>
                                                        <span style="color: #a0aec0; font-size: 14px;"><a href="login.php" style="color: #00d9ff;">Login</a> to rate</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <div id="noResults" class="no-results" style="display: none;">
                                <div class="no-results-icon">🔍</div>
                                <h3>No results found</h3>
                                <p>Try different keywords or browse all questions</p>
                            </div>

                            <div class="ask-question">
                                <h2>Didn't find your answer?</h2>
                                
                                <?php if ($question_success): ?>
                                    <div class="success-message">
                                        ✓ Your question has been submitted! We'll review it and add it to our FAQ soon.
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($question_error): ?>
                                    <div class="error-message">
                                        ✕ <?php echo htmlspecialchars($question_error); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (is_logged_in()): ?>
                                    <form method="POST">
                                        <div class="form-group">
                                            <label for="question">Ask your question:</label>
                                            <textarea name="question" id="question" required placeholder="What would you like to know about our power banks?"></textarea>
                                        </div>
                                        <button type="submit" name="submit_question" class="btn-submit">Submit Question</button>
                                    </form>
                                <?php else: ?>
                                    <div class="login-prompt">
                                        Please <a href="login.php?redirect=faq.php">login</a> or <a href="register.php">register</a> to submit a question.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 PowerHub. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Update cart badge
        function updateCartBadge() {
            const badge = document.getElementById('cartBadge');
            if (!badge) return;
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
            badge.textContent = totalItems;
        }
        updateCartBadge();
        
        // Toggle FAQ item
        function toggleFaq(element) {
            const item = element.closest('.faq-item');
            item.classList.toggle('open');
        }

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const faqItems = document.querySelectorAll('.faq-item');
        const noResults = document.getElementById('noResults');

        if (searchInput && faqItems.length > 0) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                let visibleCount = 0;

                faqItems.forEach(item => {
                    const question = item.querySelector('.faq-question span').textContent.toLowerCase();
                    const answerEl = item.querySelector('.faq-answer p');
                    const answer = answerEl ? answerEl.textContent.toLowerCase() : '';
                    const tags = item.dataset.tags ? item.dataset.tags.toLowerCase() : '';

                    if (query === '' || question.includes(query) || answer.includes(query) || tags.includes(query)) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (noResults) {
                    noResults.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            });
        }

        // Filter by tag
        function filterByTag(tag) {
            if (searchInput) searchInput.value = '';
            let visibleCount = 0;

            faqItems.forEach(item => {
                const tags = item.dataset.tags ? item.dataset.tags.toLowerCase() : '';

                if (tag === 'all' || tags.includes(tag.toLowerCase())) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (noResults) {
                noResults.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }

        // Rate FAQ
        function rateFaq(faqId, rating, starElement) {
            const formData = new FormData();
            formData.append('rate_faq', '1');
            formData.append('faq_id', faqId);
            formData.append('rating', rating);

            fetch('faq.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update stars
                    const starRating = starElement.closest('.star-rating');
                    const stars = starRating.querySelectorAll('.star');
                    stars.forEach((star, index) => {
                        if (index < rating) {
                            star.classList.add('active');
                        } else {
                            star.classList.remove('active');
                        }
                    });

                    // Show message
                    const message = document.getElementById('rating-msg-' + faqId);
                    if (message) {
                        message.style.display = 'inline';
                        setTimeout(() => {
                            message.style.display = 'none';
                        }, 3000);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>