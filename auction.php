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
    .upcoming-auctions, .active-auctions, .ended-auctions {
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
        position: relative;
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .auction-card.upcoming {
        opacity: 0.9;
        border: 2px solid #6c757d;
    }

    .auction-card.upcoming .auction-image {
        filter: grayscale(30%);
    }

    .auction-card.ended {
        opacity: 0.8;
        background-color: #f8f9fa;
    }

    .auction-card.ended td {
        color: #6c757d;
    }

    .auction-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .auction-card.ended:hover, .auction-card.upcoming:hover {
        transform: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .limited-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: rgba(255, 193, 7, 0.95);
        color: #000;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: bold;
        z-index: 2;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .auction-image {
        position: relative;
        height: 180px;
        background-size: cover;
        background-position: center;
        z-index: 1;
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

    .time-upcoming, .time-remaining, .time-ended {
        font-size: 0.9rem;
        margin: 0.5rem 0 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .time-upcoming {
        color: #6c757d;
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
            <?php
            $now = new DateTime();
            $upcomingAuctions = [];
            $activeAuctions = [];
            $endedAuctions = [];
            
            // Get all auctions and separate them
            $stmt = $pdo->query("SELECT * FROM items ORDER BY bid_start_date ASC");
            while ($item = $stmt->fetch()) {
                $start_date = new DateTime($item['bid_start_date']);
                $end_date = new DateTime($item['bid_end_date']);
                
                if ($end_date <= $now) {
                    $endedAuctions[] = $item;
                } elseif ($start_date > $now) {
                    $upcomingAuctions[] = $item;
                } else {
                    $activeAuctions[] = $item;
                }
            }
            
            // Display upcoming auctions
            if (!empty($upcomingAuctions)): ?>
                <section class="upcoming-auctions">
                    <div class="section-header">
                        <h2>Upcoming Auctions</h2>
                        <div class="time-info">
                            <i class="fas fa-clock"></i> Current Time: <?php echo date('M j, Y H:i'); ?>
                        </div>
                    </div>
                    
                    <div class="auction-grid">
                        <?php
                        $upcomingChunks = array_chunk($upcomingAuctions, 4);
                        foreach ($upcomingChunks as $chunk): ?>
                            <div class="auction-row">
                                <?php foreach ($chunk as $item) {
                                    displayAuctionItem($item, 'upcoming', $is_admin_view);
                                } ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
            
            <!-- Active Auctions Section -->
            <section class="active-auctions">
                <div class="section-header">
                    <h2>Active Auctions</h2>
                    <div class="time-info">
                        <i class="fas fa-clock"></i> Current Time: <?php echo date('M j, Y H:i'); ?>
                    </div>
                </div>
                
                <div class="auction-grid">
                    <?php
                    $activeChunks = array_chunk($activeAuctions, 4);
                    foreach ($activeChunks as $chunk): ?>
                        <div class="auction-row">
                            <?php foreach ($chunk as $item) {
                                displayAuctionItem($item, 'active', $is_admin_view);
                            } ?>
                        </div>
                    <?php endforeach; ?>
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
                    foreach ($endedChunks as $chunk): ?>
                        <div class="auction-row">
                            <?php foreach ($chunk as $item) {
                                displayAuctionItem($item, 'ended', $is_admin_view);
                            } ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </main>

    <footer class="modern-footer">
        <!-- Footer content remains the same -->
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
function displayAuctionItem($item, $status, $isAdminView) {
    global $pdo;
    
    // Get highest bid for this item
    $bidStmt = $pdo->prepare("SELECT MAX(bid_amount) as max_bid, 
                             (SELECT COUNT(*) FROM bids WHERE item_id = ?) as bid_count
                             FROM bids WHERE item_id = ?");
    $bidStmt->execute([$item['id'], $item['id']]);
    $bid = $bidStmt->fetch();
    $currentBid = $bid['max_bid'] ? $bid['max_bid'] : $item['starting_price'];
    $bidCount = $bid['bid_count'];
    
    // Calculate time based on status
    $now = new DateTime();
    $start_date = new DateTime($item['bid_start_date']);
    $end_date = new DateTime($item['bid_end_date']);
    
    if ($status === 'upcoming') {
        $timeInfo = $now->diff($start_date);
        $timeRemaining = $timeInfo->format('%a days %h hours %i minutes');
    } elseif ($status === 'active') {
        $timeInfo = $now->diff($end_date);
        $timeRemaining = $timeInfo->format('%a days %h hours %i minutes');
    } else {
        $timeRemaining = 'Ended';
    }
    
    echo '<div class="auction-card ' . $status . '" id="item-' . $item['id'] . '">';
    
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
            
    if ($status === 'upcoming') {
        echo '<div class="time-upcoming">
                <i class="fas fa-clock"></i> Starts in ' . $timeRemaining . '
              </div>';
        echo '<div class="alert alert-info">Bidding not yet started</div>';
    } elseif ($status === 'active') {
        echo '<div class="time-remaining">
                <i class="fas fa-clock"></i> ' . $timeRemaining . ' left
              </div>';
        echo '<a href="product_view.php?id=' . $item['id'] . '" class="btn btn-outline">Place Bid</a>';
    } else {
        echo '<div class="time-ended">
                <i class="fas fa-ban"></i> Ended
              </div>';
        echo '<a href="product_view.php?id=' . $item['id'] . '" class="btn btn-outline">View Results</a>';
    }
    
    echo '</div>
    </div>';
}
?>