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

// Determine auction status
$now = new DateTime();
$start_date = new DateTime($product['bid_start_date']);
$end_date = new DateTime($product['bid_end_date']);
$is_upcoming = $start_date > $now;
$is_active = !$is_upcoming && $end_date > $now;
$is_ended = $end_date <= $now;

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
if ($is_upcoming) {
    $time_remaining = $now->diff($start_date)->format('%a days %h hours %i minutes until start');
} elseif ($is_active) {
    $time_remaining = $now->diff($end_date)->format('%a days %h hours %i minutes remaining');
} else {
    $time_remaining = 'Auction ended';
}

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
        
        /* Product View Specific Styles */
        .product-view {
            padding: 2rem 0;
        }
        
        .product-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .product-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }
        
        .product-image {
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .product-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .product-info {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        .description {
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .bid-info {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-row .label {
            font-weight: 500;
            color: #555;
            min-width: 120px;
        }
        
        .info-row .value {
            font-weight: 600;
            text-align: right;
        }
        
        .info-row .value.active {
            color: var(--success-color);
        }
        
        .info-row .value.ended {
            color: var(--error-color);
        }
        
        .info-row .value.upcoming {
            color: #6c757d;
        }
        
        /* Bid Form Styling */
        .bid-form {
            background: #ffffff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(111, 78, 55, 0.1);
            border: 1px solid #e5c8a8;
            margin-top: 1.5rem;
        }
        
        .bid-form .form-group {
            margin-bottom: 1.5rem;
        }
        
        .bid-form label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 500;
            color: #6F4E37;
            font-size: 1.1rem;
        }
        
        .bid-form input[type="number"] {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5c8a8;
            border-radius: 8px;
            font-size: 1.2rem;
            color: #6F4E37;
            transition: all 0.3s ease;
            background: #f9f5f0;
        }
        
        .bid-form input[type="number"]:focus {
            outline: none;
            border-color: #6F4E37;
            box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.2);
        }
        
        .bid-form .btn-primary {
            width: 100%;
            padding: 1rem;
            background-color: #6F4E37;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .bid-form .btn-primary:hover {
            background-color: #8B6B4D;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(111, 78, 55, 0.2);
        }
        
        /* Bid amount input styling */
        .bid-amount-container {
            position: relative;
        }
        
        .bid-amount-container::before {
            content: '₱';
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: #6F4E37;
            font-weight: 500;
        }
        
        .bid-form input#bid-amount {
            padding-left: 2.5rem;
            font-weight: 600;
        }
        
        /* Bid History */
        .bid-history {
            margin-top: 3rem;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .history-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1.5rem;
        }
        
        .history-table th {
            background-color: #f8f9fa;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
        }
        
        .history-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        .history-table tr:last-child td {
            border-bottom: none;
        }
        
        /* Anonymous Bidding Styles */
        .anonymous-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            padding: 0.5rem 0;
        }
        
        .anonymous-checkbox input {
            width: auto;
            margin: 0;
        }
        
        .anonymous-bidder {
            color: #666;
            font-style: italic;
        }
        
        .anonymous-you {
            font-size: 0.8em;
            color: #6F4E37;
            margin-left: 0.5rem;
        }
        
        .admin-real-identity {
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .product-details {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
                padding: 1rem 0;
            }
            
            .info-row .value {
                text-align: left;
                width: 100%;
            }
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
            
            <?php if ($is_ended && $highestBid): ?>
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
                        <span class="value <?= $is_upcoming ? 'upcoming' : ($is_active ? 'active' : 'ended') ?>">
                            <?= $is_upcoming ? 'Upcoming' : ($is_active ? 'Active' : 'Ended') ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label"><?= $is_upcoming ? 'Starts On:' : ($is_active ? 'Ends On:' : 'Ended On:') ?></span>
                        <span class="value">
                            <?= $is_upcoming ? 
                                $start_date->format('M j, Y H:i') : 
                                ($is_active ? $end_date->format('M j, Y H:i') : $end_date->format('M j, Y H:i')) 
                            ?>
                        </span>
                    </div>
                    <?php if ($is_upcoming || $is_active): ?>
                        <div class="info-row">
                            <span class="label">Time:</span>
                            <span class="value <?= $is_upcoming ? 'upcoming' : ($is_active ? 'active' : 'ended') ?>">
                                <?= $time_remaining ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($is_upcoming): ?>
                    <div class="alert alert-info">
                        Bidding will start on <?= $start_date->format('M j, Y H:i') ?>
                    </div>
                <?php elseif ($is_active && !$is_admin_view && isset($_SESSION['user_id'])): ?>
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
                <?php elseif ($is_ended): ?>
                    <div class="alert alert-info">
                        This auction has ended
                    </div>
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
                <p>No bids yet. <?= $is_upcoming ? 'Bidding will start soon' : 'Be the first to bid!' ?></p>
            <?php endif; ?>
        </div>
    </main>

    <footer class="modern-footer">
        <!-- Footer content remains the same -->
    </footer>
</body>
</html>