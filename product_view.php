<?php
include 'db_connect.php';

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: auction.php');
    exit;
}

$product_id = (int)$_GET['id'];

// Get product details
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: auction.php');
    exit;
}

// Get current highest bid
$bidStmt = $pdo->prepare("SELECT MAX(bid_amount) as max_bid, 
                         COUNT(*) as bid_count FROM bids WHERE item_id = ?");
$bidStmt->execute([$product_id]);
$bidInfo = $bidStmt->fetch();
$currentBid = $bidInfo['max_bid'] ? $bidInfo['max_bid'] : $product['starting_price'];
$bidCount = $bidInfo['bid_count'];

// Calculate time remaining
$now = new DateTime();
$end_date = new DateTime($product['bid_end_date']);
$is_active = $end_date > $now;
$time_remaining = $is_active ? $now->diff($end_date)->format('%a days %h hours %i minutes') : 'Auction ended';

// Check if user can see bid history
$show_bid_history = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Coffee Auction</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <div class="logo">
                <img src="images/crop.png" alt="Coffee-Auction" style="width:50px;">
                <span class="logo-text">Coffee Auction</span>
            </div>
        </div>
        <div class="navbar-center">
            <a href="index.php" class="nav-link">Home</a>
            <a href="auction.php" class="nav-link">Auction</a>
            <a href="about.php" class="nav-link">About</a>
        </div>
        <div class="navbar-right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-greeting">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="register.php" class="btn btn-primary">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container product-view">
        <div class="product-header">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <a href="auction.php" class="btn btn-outline">Back to Auctions</a>
        </div>

        <div class="product-details">
            <div class="product-image">
                <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>
            
            <div class="product-info">
                <div class="description">
                    <h3>Description</h3>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                </div>
                
                <div class="bid-info">
                    <div class="info-row">
                        <span class="label">Starting Price:</span>
                        <span class="value">₱<?= number_format($product['starting_price'], 2) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Current Bid:</span>
                        <span class="value">₱<?= number_format($currentBid, 2) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Bids:</span>
                        <span class="value"><?= $bidCount ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Status:</span>
                        <span class="value <?= $is_active ? 'active' : 'ended' ?>">
                            <?= $is_active ? 'Active' : 'Ended' ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label"><?= $is_active ? 'Time Remaining:' : 'Ended On:' ?></span>
                        <span class="value"><?= $is_active ? $time_remaining : $end_date->format('M j, Y H:i') ?></span>
                    </div>
                </div>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($is_active): ?>
                        <div class="bid-form">
                            <form action="process_bid.php" method="post">
                                <input type="hidden" name="item_id" value="<?= $product_id ?>">
                                <div class="form-group">
                                    <label for="bid-amount">Your Bid (₱)</label>
                                    <div class="bid-amount-container">
                                        <input type="number" id="bid-amount" name="bid_amount" 
                                            min="<?= $currentBid + 0.5 ?>" step="0.5" 
                                            value="<?= $currentBid + 0.5 ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="anonymous-checkbox">
                                        <input type="checkbox" name="is_anonymous" value="1">
                                        <span>Bid anonymously (your name will be hidden)</span>
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary">Place Bid</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            This auction has ended
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <a href="login.php">Login</a> to place a bid
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($show_bid_history): ?>
            <div class="bid-history">
                <h2>Bid History</h2>
                <?php
                $historyStmt = $pdo->prepare("SELECT b.*, u.fullname 
                                             FROM bids b JOIN users u ON b.user_id = u.id 
                                             WHERE b.item_id = ? ORDER BY b.bid_amount DESC");
                $historyStmt->execute([$product_id]);
                $bidHistory = $historyStmt->fetchAll();
                
                if (count($bidHistory) > 0): ?>
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Bidder</th>
                                <th>Amount</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bidHistory as $bid): ?>
                                <tr>
                                    <td>
                                        <?php if ($bid['is_anonymous'] && $bid['user_id'] != $_SESSION['user_id']): ?>
                                            <span class="anonymous-bidder">Anonymous Bidder</span>
                                        <?php else: ?>
                                            <?= htmlspecialchars($bid['fullname']) ?>
                                            <?php if ($bid['is_anonymous']): ?>
                                                <span class="anonymous-you">(You)</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>₱<?= number_format($bid['bid_amount'], 2) ?></td>
                                    <td><?= date('M j, Y H:i', strtotime($bid['timestamp'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No bids yet. Be the first to bid!</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <a href="login.php">Login</a> to view bid history
            </div>
        <?php endif; ?>
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