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

// Get bid history
$historyStmt = $pdo->prepare("SELECT b.*, u.fullname 
                             FROM bids b JOIN users u ON b.user_id = u.id 
                             WHERE b.item_id = ? ORDER BY b.bid_amount DESC");
$historyStmt->execute([$product_id]);
$bidHistory = $historyStmt->fetchAll();

// Check if user can bid
$can_bid = isset($_SESSION['user_id']) && !isset($_SESSION['admin_logged_in']) && $is_active;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Coffee Auction</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <div class="logo">
                <a href=""><img src="images/1.png" alt="" style="width:50px;"></a>
            </div>
        </div>
        <div class="navbar-center">
            <a href="index.php" class="nav-link active">Home</a>
            <a href="auction.php" class="nav-link">Auction</a>
        </div>
        <div class="navbar-right">
            <a href="login.php" class="btn btn-outline">Login</a>
            <a href="register.php" class="btn btn-primary">Register</a>
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
                        <span class="value">$<?= number_format($product['starting_price'], 2) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Current Bid:</span>
                        <span class="value">$<?= number_format($currentBid, 2) ?></span>
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
                
                <?php if ($can_bid): ?>
                    <div class="bid-form">
                        <form action="process_bid.php" method="post">
                            <input type="hidden" name="item_id" value="<?= $product_id ?>">
                            <div class="form-group">
                                <label for="bid-amount">Your Bid ($)</label>
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
                <?php elseif (!isset($_SESSION['user_id'])): ?>
                    <div class="alert alert-info">
                        <a href="login.php">Login</a> to place a bid
                    </div>
                <?php elseif (!$is_active): ?>
                    <div class="alert alert-info">
                        This auction has ended
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
                                <td>$<?= number_format($bid['bid_amount'], 2) ?></td>
                                <td><?= date('M j, Y H:i', strtotime($bid['timestamp'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No bids yet. Be the first to bid!</p>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <span class="logo-icon">☕</span>
                <span class="logo-text">Coffee Auction</span>
            </div>
            <div class="footer-links">
                <a href="#">About Us</a>
                <a href="#">Terms</a>
                <a href="#">Privacy</a>
                <a href="#">Contact</a>
            </div>
        </div>
        <div class="footer-copyright">
            &copy; <?php echo date('Y'); ?> Coffee Auction. All rights reserved.
        </div>
    </footer>
</body>
</html>