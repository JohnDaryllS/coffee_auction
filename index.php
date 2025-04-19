<?php include 'db_connect.php'; ?>

<?php 
// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);
$is_home = ($current_page == 'index.php' || $current_page == 'user_account.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Auction - Premium Coffee Beans</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            <a href="index.php" class="nav-link <?= $is_home ? 'active' : '' ?>">Home</a>
            <a href="auction.php" class="nav-link <?= $current_page == 'auction.php' ? 'active' : '' ?>">Auction</a>
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

    <header class="hero">
        <div class="hero-content">
            <h1>Discover Rare & Premium Coffee Beans</h1>
            <p>Bid on the world's finest coffee selections from small farms and specialty growers</p>
            <a href="auction.php" class="btn btn-primary btn-large">View Auctions</a>
        </div>
    </header>

    <main class="container">
        <section class="featured-auctions">
            <h2>Featured Auctions</h2>
            <div class="auction-grid">
                <?php
                $stmt = $pdo->query("SELECT * FROM items LIMIT 4");
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
                            <p class="current-bid">Current Bid: $' . number_format($currentBid, 2) . '</p>
                            <a href="product_view.php?id=' . (int)$item['id'] . '" class="btn btn-outline">View Auction</a>
                        </div>
                    </div>';
                }
                ?>
            </div>
        </section>
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
</body>
</html>