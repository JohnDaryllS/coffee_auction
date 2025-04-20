<?php
include 'db_connect.php';

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: auction.php');
    exit;
}

$product_id = (int)$_GET['id'];
$is_admin_view = isset($_GET['admin']) && isset($_SESSION['admin_logged_in']);

// Get product details
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: auction.php');
    exit;
}

// Get current highest bid and winner information
$bidStmt = $pdo->prepare("SELECT b.*, u.fullname, u.email 
                         FROM bids b 
                         JOIN users u ON b.user_id = u.id 
                         WHERE b.item_id = ? 
                         ORDER BY b.bid_amount DESC 
                         LIMIT 1");
$bidStmt->execute([$product_id]);
$highestBid = $bidStmt->fetch();

$currentBid = $highestBid ? $highestBid['bid_amount'] : $product['starting_price'];
$bidCount = $pdo->query("SELECT COUNT(*) FROM bids WHERE item_id = $product_id")->fetchColumn();

// Calculate time remaining
$now = new DateTime();
$end_date = new DateTime($product['bid_end_date']);
$is_active = $end_date > $now;
$time_remaining = $is_active ? $now->diff($end_date)->format('%a days %h hours %i minutes') : 'Auction ended';

// Get full bid history (admin can see all, including anonymous bids)
$historyStmt = $pdo->prepare("SELECT b.*, u.fullname, u.email 
                             FROM bids b 
                             JOIN users u ON b.user_id = u.id 
                             WHERE b.item_id = ? 
                             ORDER BY b.bid_amount DESC");
$historyStmt->execute([$product_id]);
$bidHistory = $historyStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Coffee Auction</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="images/crop.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Admin-specific styles */
        .admin-view-banner {
            background-color: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .winner-info {
            background-color: #d4edda;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #28a745;
        }
        
        .bidder-identity {
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .anonymous-real {
            font-style: italic;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .admin-bid-info {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="navbar-left">
        <div class="logo">
            <img src="images/crop.png" alt="Coffee-Auction" style="width:50px;">
            <span class="logo-text">Coffee Auction</span>
        </div>
    </div>
    <?php if (!$is_admin_view): ?>
        <div class="navbar-center">
            <a href="index.php" class="nav-link">Home</a>
            <a href="auction.php" class="nav-link">Auction</a>
            <a href="about.php" class="nav-link">About</a>
        </div>
    <?php endif; ?>
    <div class="navbar-right">
        <?php if ($is_admin_view): ?>
            <a href="admin.php?tab=items" class="btn btn-outline">Back to Admin</a>
        <?php elseif (isset($_SESSION['user_id'])): ?>
            <span class="user-greeting">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="logout.php" class="btn btn-outline">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-outline">Login</a>
            <a href="register.php" class="btn btn-primary">Register</a>
        <?php endif; ?>
    </div>
</nav>

    <main class="container product-view">
        <?php if ($is_admin_view): ?>
            <div class="admin-view-banner">
                <div>
                    <i class="fas fa-user-shield"></i> ADMIN VIEW MODE - Bidding Disabled
                </div>
                <div>
                    <a href="product_view.php?id=<?= $product_id ?>" class="btn btn-outline btn-small">
                        View as Regular User
                    </a>
                </div>
            </div>
            
            <?php if (!$is_active && $highestBid): ?>
                <div class="winner-info">
                    <h3><i class="fas fa-trophy"></i> Auction Winner</h3>
                    <div class="admin-bid-info">
                        <p><strong>Name:</strong> <?= htmlspecialchars($highestBid['fullname']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($highestBid['email']) ?></p>
                        <p><strong>Winning Bid:</strong> ₱<?= number_format($highestBid['bid_amount'], 2) ?></p>
                        <p><strong>Bid Time:</strong> <?= date('M j, Y H:i', strtotime($highestBid['timestamp'])) ?></p>
                        <?php if ($highestBid['is_anonymous']): ?>
                            <p class="anonymous-real">(Bid was placed anonymously to other users)</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="product-header">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <a href="<?= $is_admin_view ? 'admin.php?tab=items' : 'auction.php' ?>" class="btn btn-outline">
                Back to <?= $is_admin_view ? 'Admin' : 'Auctions' ?>
            </a>
        </div>

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
                
                <?php if (!$is_admin_view && isset($_SESSION['user_id'])): ?>
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
                <?php elseif ($is_admin_view): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Admin view mode - bidding disabled
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <a href="login.php">Login</a> to place a bid
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="bid-history">
            <h2>Bid History</h2>
            <?php if (count($bidHistory) > 0): ?>
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Bidder</th>
                            <th>Amount</th>
                            <th>Time</th>
                            <?php if ($is_admin_view): ?>
                                <th>Real Identity</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bidHistory as $bid): ?>
                            <tr>
                                <td>
                                    <?php if ($bid['is_anonymous'] && !$is_admin_view && $bid['user_id'] != ($_SESSION['user_id'] ?? 0)): ?>
                                        <span class="anonymous-bidder">Anonymous Bidder</span>
                                    <?php else: ?>
                                        <span class="bidder-identity"><?= htmlspecialchars($bid['fullname']) ?></span>
                                        <?php if ($bid['is_anonymous']): ?>
                                            <span class="anonymous-you">(Anonymous)</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>₱<?= number_format($bid['bid_amount'], 2) ?></td>
                                <td><?= date('M j, Y H:i', strtotime($bid['timestamp'])) ?></td>
                                <?php if ($is_admin_view): ?>
                                    <td class="admin-real-identity">
                                        <?= htmlspecialchars($bid['fullname']) ?> 
                                        <br><small><?= htmlspecialchars($bid['email']) ?></small>
                                        <?php if ($bid['is_anonymous']): ?>
                                            <br><span class="anonymous-real">(Shown as anonymous to others)</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No bids yet. Be the first to bid!</p>
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
</body>
</html>