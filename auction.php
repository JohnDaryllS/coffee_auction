<?php 
include 'db_connect.php';

// Check if admin is viewing
$is_admin_view = isset($_GET['admin']) && isset($_SESSION['admin_logged_in']);
$can_bid = isset($_SESSION['user_id']) && !$is_admin_view;

// Check for new item parameter
$highlight_item = isset($_GET['new_item']) ? (int)$_GET['new_item'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_admin_view ? 'Admin Auction View' : 'Coffee Auctions'; ?> - Coffee Auction</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="images/crop.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<style>
    /* Auction Container */
.auction-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Auction Sections */
.active-auctions, .ended-auctions {
    margin-bottom: 4rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.time-info {
    color: #666;
    font-size: 0.9rem;
}

.time-info i {
    color: var(--primary-color);
    margin-right: 0.5rem;
}

/* Auction Grid Layout */
.auction-grid {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.auction-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

/* Auction Card Styles */
.auction-card {
        position: relative; /* Add this to make absolute positioning work */
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
}

.limited-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: rgba(255, 193, 7, 0.95); /* Slightly transparent */
        color: #000;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: bold;
        z-index: 2; /* Ensure it stays above the image */
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

.auction-card.ended {
    opacity: 0.8;
    background-color: #f8f9fa;
}

.auction-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.auction-card.ended:hover {
    transform: none;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.auction-image {
        position: relative; /* Add this for proper z-index stacking */
        height: 180px;
        background-size: cover;
        background-position: center;
        z-index: 1; /* Below the badge */
}

.auction-details {
    padding: 1.5rem;
}

.auction-details h3 {
    font-size: 1.1rem;
    margin-bottom: 1rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.bid-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.current-bid {
    font-weight: 600;
    color: var(--primary-color);
}

.bid-count {
    color: #666;
    font-size: 0.9rem;
}

.time-remaining, .time-ended {
    font-size: 0.9rem;
    margin: 0.5rem 0 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.time-remaining {
    color: var(--primary-color);
}

.time-ended {
    color: var(--error-color);
}

.auction-details .btn {
    width: 100%;
    text-align: center;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .auction-row {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}

@media (max-width: 480px) {
    .auction-row {
        grid-template-columns: 1fr;
    }
}
</style>
<body>
    <?php if ($is_admin_view): ?>
    <nav class="navbar admin-nav">
        <div class="navbar-left">
            <div class="logo">
                <img src="images/crop.png" alt="Coffee-Auction" style="width:50px;">
                <span class="logo-text">Coffee Auction</span>
            </div>
        </div>
        <div class="navbar-center">
            <a href="admin.php" class="nav-link">Admin Dashboard</a>
            <a href="about.php" class="nav-link">About</a>
        </div>
        <div class="navbar-right">
            <span class="admin-greeting">Admin Panel</span>
            <a href="logout.php" class="btn btn-outline">Logout</a>
        </div>
    </nav>
    <?php else: ?>
    <nav class="navbar">
        <div class="navbar-left">
            <div class="logo">
                <img src="images/crop.png" alt="Coffee-Auction" style="width:50px;">
                <span class="logo-text">Coffee Auction</span>
            </div>
        </div>
        <div class="navbar-center">
            <a href="index.php" class="nav-link">Home</a>
            <a href="auction.php" class="nav-link active">Auction</a>
            <a href="about.php" class="nav-link">About</a>
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
    <?php endif; ?>

    <main class="container auction-container <?php echo $is_admin_view ? 'admin-view' : ''; ?>">
        <h1><?php echo $is_admin_view ? 'Auction Management' : 'Current Coffee Auctions'; ?></h1>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php 
                if ($_GET['error'] == 'lowbid') echo "Your bid must be higher than the current bid";
                if ($_GET['error'] == 'adminnocanbid') echo "Admins cannot place bids";
                if ($_GET['error'] == 'auctionended') echo "This auction has ended";
                if ($_GET['error'] == 'outofstock') echo "This item is out of stock";
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php 
                if ($_GET['success'] == 'bid') echo "Bid placed successfully!";
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$can_bid && !$is_admin_view): ?>
            <div class="alert alert-info">
                You need to <a href="login.php">login</a> or <a href="register.php">register</a> to place bids.
            </div>
        <?php endif; ?>
        
        <div class="auction-container">
            <section class="active-auctions">
                <div class="section-header">
                    <h2>Active Auctions</h2>
                    <div class="time-info">
                        <i class="fas fa-clock"></i> Current Time: <?php echo date('M j, Y H:i'); ?>
                    </div>
                </div>
                
                <div class="auction-grid">
                    <?php
                    $now = new DateTime();
                    $activeAuctions = [];
                    $endedAuctions = [];
                    
                    // Get all auctions and separate active from ended
                    $stmt = $pdo->query("SELECT * FROM items ORDER BY bid_end_date ASC");
                    while ($item = $stmt->fetch()) {
                        $end_date = new DateTime($item['bid_end_date']);
                        if ($end_date > $now) {
                            $activeAuctions[] = $item;
                        } else {
                            $endedAuctions[] = $item;
                        }
                    }
                    
                    // Display active auctions (max 4 per row)
                    $activeChunks = array_chunk($activeAuctions, 4);
                    foreach ($activeChunks as $chunk) {
                        echo '<div class="auction-row">';
                        foreach ($chunk as $item) {
                            displayAuctionItem($item, false, $is_admin_view);
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
            </section>

            <?php if (!empty($endedAuctions)): ?>
            <section class="ended-auctions">
                <div class="section-header">
                    <h2>Ended Auctions</h2>
                </div>
                
                <div class="auction-grid">
                    <?php
                    $endedChunks = array_chunk($endedAuctions, 4);
                    foreach ($endedChunks as $chunk) {
                        echo '<div class="auction-row">';
                        foreach ($chunk as $item) {
                            displayAuctionItem($item, true, $is_admin_view);
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
            </section>
            <?php endif; ?>
        </div>
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

    <script>
        // Auto-refresh the page every minute to update timers
        setTimeout(function(){
            window.location.reload(1);
        }, 60000);
    </script>
</body>
</html>

<?php
// Helper function to display auction items
function displayAuctionItem($item, $isEnded, $isAdminView) {
    global $pdo;
    
    // Get highest bid for this item
    $bidStmt = $pdo->prepare("SELECT MAX(bid_amount) as max_bid, 
                             (SELECT COUNT(*) FROM bids WHERE item_id = ?) as bid_count
                             FROM bids WHERE item_id = ?");
    $bidStmt->execute([$item['id'], $item['id']]);
    $bid = $bidStmt->fetch();
    $currentBid = $bid['max_bid'] ? $bid['max_bid'] : $item['starting_price'];
    $bidCount = $bid['bid_count'];
    
    // Calculate time remaining if not ended
    $timeRemaining = '';
    if (!$isEnded) {
        $now = new DateTime();
        $end_date = new DateTime($item['bid_end_date']);
        $interval = $now->diff($end_date);
        $timeRemaining = $interval->format('%a days %h hours %i minutes');
    }
    
    echo '<div class="auction-card' . ($isEnded ? ' ended' : '') . '" id="item-' . $item['id'] . '">';
    
    if ($isAdminView) {
        echo '<div class="admin-view-indicator">Admin View</div>';
    }
    
    // Limited quantity badge
    if ($item['is_limited']) {
        $remaining = $item['quantity'] - $item['items_sold'];
        echo '<div class="limited-badge">Limited: ' . $remaining . ' left</div>';
    }
    
    echo '<div class="auction-image" style="background-image: url(images/' . $item['image'] . ')"></div>
        <div class="auction-details">
            <h3>' . htmlspecialchars($item['name']) . '</h3>
            <div class="bid-info">
                <span class="current-bid">₱' . number_format($currentBid, 2) . '</span>
                <span class="bid-count">' . $bidCount . ' bid' . ($bidCount != 1 ? 's' : '') . '</span>
            </div>';
            
    if ($isEnded) {
        echo '<div class="time-ended">
                <i class="fas fa-ban"></i> Ended
              </div>';
    } else {
        echo '<div class="time-remaining">
                <i class="fas fa-clock"></i> ' . $timeRemaining . ' left
              </div>';
    }
    
    if ($isAdminView) {
        echo '<div class="admin-auction-actions">
                <a href="admin.php?edit_item=' . $item['id'] . '#items-tab" class="btn btn-outline btn-small">Edit</a>
                <form action="process_item.php" method="post" class="inline-form">
                    <input type="hidden" name="item_id" value="' . $item['id'] . '">
                    <button type="submit" name="action" value="delete" class="btn btn-error btn-small">Delete</button>
                </form>
              </div>';
    } else {
        echo '<a href="product_view.php?id=' . $item['id'] . '" class="btn btn-outline">' . 
             ($isEnded ? 'View Results' : 'Place Bid') . '</a>';
    }
    
    echo '</div>
    </div>';
}
?>