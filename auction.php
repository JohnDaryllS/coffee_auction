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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
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
                <span class="user-greeting">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="logout.php" class="btn btn-outline">Logout</a>
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
        
        <div class="auction-list">
            <?php
            $now = new DateTime();
            $stmt = $pdo->query("SELECT * FROM items ORDER BY bid_end_date ASC");
            
            if ($stmt->rowCount() > 0) {
                while ($item = $stmt->fetch()) {
                    // Get highest bid for this item
                    $bidStmt = $pdo->prepare("SELECT MAX(bid_amount) as max_bid, 
                                            (SELECT COUNT(*) FROM bids WHERE item_id = ?) as bid_count
                                            FROM bids WHERE item_id = ?");
                    $bidStmt->execute([$item['id'], $item['id']]);
                    $bid = $bidStmt->fetch();
                    $currentBid = $bid['max_bid'] ? $bid['max_bid'] : $item['starting_price'];
                    $bidCount = $bid['bid_count'];
                    
                    // Calculate time remaining
                    $end_date = new DateTime($item['bid_end_date']);
                    $is_active = $end_date > $now;
                    $time_remaining = '';
                    
                    if ($is_active) {
                        $interval = $now->diff($end_date);
                        $time_remaining = $interval->format('%a days %h hours %i minutes');
                    }
                    
                    // Check if limited item is available
                    $is_available = true;
                    if ($item['is_limited'] && $item['items_sold'] >= $item['quantity']) {
                        $is_available = false;
                    }
                    
                    echo '<div class="auction-item' . 
                         ($item['id'] == $highlight_item ? ' highlight-new' : '') . 
                         ($is_active ? '' : ' auction-ended') . 
                         '" id="item-' . $item['id'] . '">';
                    
                    if ($is_admin_view) {
                        echo '<div class="admin-view-indicator">Admin View</div>';
                    }
                    
                    // Limited quantity badge
                    if ($item['is_limited']) {
                        $remaining = $item['quantity'] - $item['items_sold'];
                        echo '<div class="limited-badge">Limited: ' . $remaining . ' left</div>';
                    }
                    
                    echo '<div class="auction-item-image" style="background-image: url(images/' . $item['image'] . ')"></div>
                        <div class="auction-item-details">
                            <h3>' . htmlspecialchars($item['name']) . '</h3>
                            <p class="item-description">' . htmlspecialchars($item['description']) . '</p>
                            
                            <div class="bid-info">
                                <div class="bid-current">
                                    <span class="bid-label">Current Bid:</span>
                                    <span class="bid-amount">$' . number_format($currentBid, 2) . '</span>
                                </div>
                                <div class="bid-count">
                                    ' . $bidCount . ' bid' . ($bidCount != 1 ? 's' : '') . '
                                </div>
                            </div>
                            
                            <div class="time-info">';
                    
                    if ($is_active) {
                        echo '<div class="time-remaining">
                                <strong>Ends in:</strong> ' . $time_remaining . '
                              </div>
                              <div class="end-time">
                                (Ends at ' . $end_date->format('M j, Y H:i') . ')
                              </div>';
                    } else {
                        echo '<div class="time-ended">
                                <strong>Auction Ended</strong> on ' . $end_date->format('M j, Y H:i') . '
                              </div>';
                    }
                    
                    echo '</div>';
                    
                    if ($can_bid) {
                        if ($is_active && $is_available) {
                            echo '<form action="process_bid.php" method="post" class="bid-form">
                                    <input type="hidden" name="item_id" value="' . $item['id'] . '">
                                    <div class="form-group">
                                        <label for="bid-amount-' . $item['id'] . '">Your Bid ($)</label>
                                        <input type="number" id="bid-amount-' . $item['id'] . '" name="bid_amount" 
                                            min="' . ($currentBid + 0.5) . '" step="0.5" 
                                            value="' . ($currentBid + 0.5) . '" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Place Bid</button>
                                </form>';
                        } elseif (!$is_available) {
                            echo '<div class="out-of-stock">Out of Stock</div>';
                        } else {
                            echo '<div class="bid-notice">Bidding for this item has ended</div>';
                        }
                    } elseif ($is_admin_view) {
                        echo '<div class="admin-auction-actions">
                                <a href="admin.php?edit_item=' . $item['id'] . '#items-tab" class="btn btn-outline btn-small">Edit</a>
                                <form action="process_item.php" method="post" class="inline-form">
                                    <input type="hidden" name="item_id" value="' . $item['id'] . '">
                                    <button type="submit" name="action" value="delete" class="btn btn-error btn-small">Delete</button>
                                </form>
                              </div>';
                    }
                    
                    echo '</div>
                        </div>';
                }
            } else {
                echo '<div class="empty-state">
                        <p>No auction items available at this time.</p>
                        ' . ($is_admin_view ? '<a href="admin.php#items-tab" class="btn btn-primary">Add New Item</a>' : '') . '
                      </div>';
            }
            ?>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="images/crop.png" alt="Coffee-Auction" style="width:50px;">
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

    <script>
        // Auto-refresh the page every minute to update timers
        setTimeout(function(){
            window.location.reload(1);
        }, 60000);
    </script>
</body>
</html>