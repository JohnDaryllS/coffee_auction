<?php include 'db_connect.php'; ?>

<?php 
// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);
$is_home = ($current_page == 'index.php' || $current_page == 'user_account.php');

// Get the item with the highest current bid
$highestBidStmt = $pdo->query("SELECT i.*, MAX(b.bid_amount) as max_bid 
                              FROM items i 
                              LEFT JOIN bids b ON i.id = b.item_id 
                              WHERE i.bid_end_date > NOW()
                              GROUP BY i.id 
                              ORDER BY max_bid DESC 
                              LIMIT 1");
$highestBidItem = $highestBidStmt->fetch();
$highestBid = $highestBidItem ? ($highestBidItem['max_bid'] ?: $highestBidItem['starting_price']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Auction - Premium Coffee Beans</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="images/crop.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    /* Hero Section */
.hero {
    position: relative;
    height: 80vh;
    overflow: hidden;
    color: white;
    display: flex;
    align-items: center;
    text-align: center;
}

.hero-content {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
    z-index: 2;
}

.hero h1 {
    font-size: 3.5rem;
    margin-bottom: 1.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.hero p {
    font-size: 1.5rem;
    margin-bottom: 2.5rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.highest-bid-banner {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1.5rem;
    margin: 2rem auto;
    max-width: 500px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.bid-info {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.bid-info .label {
    font-size: 1.1rem;
    font-weight: 500;
    color: brown;
}

.bid-info .amount {
    font-size: 2rem;
    font-weight: 700;
    color: #f8c537;
}

.item-info {
    font-style: italic;
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .hero h1 {
        font-size: 2.5rem;
    }
    
    .hero p {
        font-size: 1.2rem;
    }
    
    .bid-info {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .bid-info .amount {
        font-size: 1.8rem;
    }
}
</style>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <div class="logo">
                <img src="images/crop.png" alt="Coffee-Auction" style="width:50px;">
                <span class="logo-text">Coffee Auction</span>
            </div>
        </div>
        <div class="navbar-center">
            <a href="index.php" class="nav-link <?= $is_home ? 'active' : '' ?>">Home</a>
            <a href="auction.php" class="nav-link <?= $current_page == 'auction.php' ? 'active' : '' ?>">Auction</a>
            <a href="about.php" class="nav-link <?= $current_page == 'about.php' ? 'active' : '' ?>">About</a>
        </div>
        <div class="navbar-right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="notification-container">
                    <span class="user-greeting">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    <?php
                    // Get unread notification count
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE");
                    $stmt->execute([$_SESSION['user_id']]);
                    $unreadCount = $stmt->fetchColumn();
                    ?>
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span class="notification-badge"><?= $unreadCount ?></span>
                        <?php endif; ?>
                        <div class="notification-dropdown">
                            <div class="notification-header">
                                <h4>Notifications</h4>
                                <a href="mark_all_read.php" class="mark-all-read">Mark all as read</a>
                            </div>
                            <div class="notification-list">
                                <?php
                                $notifications = getUserNotifications($_SESSION['user_id']);
                                
                                if (empty($notifications)) {
                                    echo '<div class="notification-item empty">No notifications</div>';
                                } else {
                                    foreach ($notifications as $notification) {
                                        $class = $notification['is_read'] ? 'read' : 'unread';
                                        echo '<div class="notification-item '.$class.'">';
                                        echo htmlspecialchars($notification['message']);
                                        
                                        // Add exact time along with relative time
                                        $createdAt = new DateTime($notification['created_at']);
                                        $createdAt->setTimezone(new DateTimeZone('Asia/Manila'));
                                        echo '<div class="notification-time" title="'.$createdAt->format('M j, Y h:i A').'">';
                                        echo time_elapsed_string($notification['created_at']);
                                        echo '</div>';
                                        
                                        if (!$notification['is_read']) {
                                            echo '<a href="mark_read.php?id='.$notification['id'].'" class="mark-read">Mark read</a>';
                                        }
                                        echo '</div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <a href="logout.php" class="btn btn-outline">Logout</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="register.php" class="btn btn-primary">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <h1>Discover Rare & Premium Coffee Beans</h1>
            <p>Bid on the world's finest coffee selections from small farms and specialty growers</p>

            <?php if ($highestBidItem): ?>
                <div class="highest-bid-banner">
                    <div class="bid-info">
                        <span class="label">Highest Current Bid:</span>
                        <span class="amount">₱<?= number_format($highestBid, 2) ?></span>
                    </div>
                    <div class="item-info">
                        For "<?= htmlspecialchars($highestBidItem['name']) ?>"
                    </div>
                </div>
            <?php endif; ?>

            <a href="auction.php" class="btn btn-primary btn-large">View Auctions</a>
        </div>
    </header>

    <main class="container">
    <section class="featured-auctions">
    <div class="section-header">
        <h2>Featured Auctions</h2>
        <a href="auction.php" class="btn btn-outline">View All</a>
    </div>
    <div class="auction-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM items WHERE bid_end_date > NOW() ORDER BY RAND() LIMIT 4");
        while ($item = $stmt->fetch()) {
            // Get highest bid for this item
            $bidStmt = $pdo->prepare("SELECT MAX(bid_amount) as max_bid FROM bids WHERE item_id = ?");
            $bidStmt->execute([$item['id']]);
            $bid = $bidStmt->fetch();
            $currentBid = $bid['max_bid'] ? $bid['max_bid'] : $item['starting_price'];
            
            echo '<div class="auction-card">
                <div class="auction-image" style="background-image: url(images/' . $item['image'] . ')"></div>
                <div class="auction-details">
                    <h3>' . htmlspecialchars($item['name']) . '</h3>
                    <p class="current-bid">Current Bid: ₱' . number_format($currentBid, 2) . '</p>
                    <div class="time-remaining">
                        <i class="fas fa-clock"></i> ' . timeRemaining($item['bid_end_date']) . '
                    </div>
                    <a href="product_view.php?id=' . (int)$item['id'] . '" class="btn btn-outline">View Auction</a>
                </div>
            </div>';
        }
        
        // Helper function to calculate time remaining
        function timeRemaining($endDate) {
            $now = new DateTime();
            $end = new DateTime($endDate);
            $interval = $now->diff($end);
            
            if ($interval->d > 0) {
                return $interval->d . 'd ' . $interval->h . 'h left';
            } elseif ($interval->h > 0) {
                return $interval->h . 'h ' . $interval->i . 'm left';
            } else {
                return $interval->i . 'm left';
            }
        }
        ?>
    </div>
</section>
    </main>

    <footer class="modern-footer">
    <div class="footer-container">
        <!-- Footer Columns -->
        <div class="footer-grid">
            <!-- About Column -->
            <div class="footer-column">
                <div class="footer-logo">
                    <img src="images/crop.png" alt="Coffee Auction" class="logo-img">
                    <span class="logo-texts">Coffee Auction</span>
                </div>
                <p class="footer-about">
                    Revolutionizing how specialty coffee reaches enthusiasts worldwide. 
                    Connecting farmers directly with coffee lovers through our unique auction platform.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Quick Links Column -->
            <div class="footer-column">
                <h3 class="footer-title">Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="auction.php">Current Auctions</a></li>
                    <li><a href="about.php">Our Story</a></li>
                    <li><a href="#">How It Works</a></li>
                    <li><a href="#">Coffee Guides</a></li>
                </ul>
            </div>

            <!-- Contact Column -->
            <div class="footer-column">
                <h3 class="footer-title">Contact Us</h3>
                <ul class="contact-info">
                    <li class="contact-item">
                        <i class="fas fa-map-marker-alt contact-icon"></i>
                        <span>123 Coffee Street, Portland, OR 97205</span>
                    </li>
                    <li class="contact-item">
                        <i class="fas fa-phone-alt contact-icon"></i>
                        <span>+1 (503) 555-0199</span>
                    </li>
                    <li class="contact-item">
                        <i class="fas fa-envelope contact-icon"></i>
                        <span>info@coffeeauction.com</span>
                    </li>
                    <li class="contact-item">
                        <i class="fas fa-clock contact-icon"></i>
                        <span>Mon-Fri: 9AM - 5PM</span>
                    </li>
                </ul>
            </div>

            <!-- Newsletter Column -->
            <div class="footer-column">
                <h3 class="footer-title">Newsletter</h3>
                <p class="newsletter-text">
                    Subscribe to get updates on new auctions, coffee tips, and exclusive offers.
                </p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Your email address" required>
                    <button type="submit" class="subscribe-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> Coffee Auction. All rights reserved.
            </div>
            <div class="legal-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
</body>
</html>