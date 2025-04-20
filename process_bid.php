<?php
session_start();
include 'db_connect.php';

// Check if user is logged in (and not admin)
if (!isset($_SESSION['user_id']) || isset($_SESSION['admin_logged_in'])) {
    $_SESSION['error'] = 'You must be logged in to place bids';
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_SESSION['user_id'];
    $item_id = (int)$_POST['item_id'];
    $bid_amount = (float)$_POST['bid_amount'];
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;

    try {
        $pdo->beginTransaction();

        // 1. Validate the item exists and is active
        $stmt = $pdo->prepare("SELECT i.*, 
                             (SELECT MAX(bid_amount) FROM bids WHERE item_id = i.id) as current_bid
                             FROM items i WHERE i.id = ?");
        $stmt->execute([$item_id]);
        $item = $stmt->fetch();

        if (!$item) {
            throw new Exception('Invalid auction item');
        }

        // 2. Check auction end time
        $now = new DateTime();
        $end_date = new DateTime($item['bid_end_date']);
        if ($end_date <= $now) {
            throw new Exception('This auction has ended');
        }

        // 3. Check if limited item is available
        if ($item['is_limited'] && $item['items_sold'] >= $item['quantity']) {
            throw new Exception('This item is out of stock');
        }

        // 4. Determine current highest bid
        $current_bid = $item['current_bid'] ? $item['current_bid'] : $item['starting_price'];

        // 5. Validate bid amount
        if ($bid_amount <= $current_bid) {
            throw new Exception('Your bid must be higher than the current bid');
        }

        // 6. Check minimum bid increment
        $min_increment = 0.50; // Minimum $0.50 increment
        if (($bid_amount - $current_bid) < $min_increment) {
            throw new Exception('Minimum bid increment is ₱0.50');
        }

        // 7. Insert the bid
        $stmt = $pdo->prepare("INSERT INTO bids 
                             (user_id, item_id, bid_amount, is_anonymous) 
                             VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $item_id, $bid_amount, $is_anonymous]);

        // 8. If limited item, increment items_sold
        if ($item['is_limited']) {
            $stmt = $pdo->prepare("UPDATE items SET items_sold = items_sold + 1 WHERE id = ?");
            $stmt->execute([$item_id]);
        }

        $pdo->commit();

        $_SESSION['success'] = 'Bid placed successfully!';
        header("Location: product_view.php?id=$item_id");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = $e->getMessage();
        header("Location: product_view.php?id=$item_id");
        exit;
    }
}

// If not a POST request, redirect to auction page
header('Location: auction.php');
exit;
?>