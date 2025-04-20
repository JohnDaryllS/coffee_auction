<?php
// Database configuration
$host = 'localhost';
$db   = 'coffee_auction';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Set default timezone for the application
date_default_timezone_set('Asia/Manila');

// Data Source Name
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Set timezone for database connection
    $pdo->exec("SET time_zone = '+08:00'");
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Start session
session_start();

// Define constants for easy access
define('SITE_NAME', 'Coffee Auction');
define('CURRENCY', '₱');

/**
 * Get current datetime in correct timezone for database storage
 * @return string Formatted datetime string
 */
function getCurrentDateTime() {
    $date = new DateTime('now', new DateTimeZone('Asia/Manila'));
    return $date->format('Y-m-d H:i:s');
}

/**
 * Add a new notification for a user
 * @param int $user_id The ID of the user to notify
 * @param string $message The notification message
 */
function addNotification($user_id, $message) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$user_id, $message]);
}

/**
 * Get all notifications for a user
 * @param int $user_id The user ID
 * @return array Array of notifications
 */
function getUserNotifications($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

/**
 * Get unread notification count for a user
 * @param int $user_id The user ID
 * @return int Number of unread notifications
 */
function getUnreadNotificationCount($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

/**
 * Format a datetime string to relative time with accurate timezone
 * @param string $datetime The datetime string from database
 * @param bool $full Whether to show full details or just the most significant unit
 * @return string Formatted relative time string with accurate time
 */
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    
    // For very recent events (less than 1 minute ago), show exact time
    if (empty($string)) {
        $ago->setTimezone(new DateTimeZone('Asia/Manila'));
        return $ago->format('h:i A') . ' (just now)';
    }
    
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

/**
 * Check for ended auctions and notify winners
 * This should be called periodically (e.g., via cron job or on page load)
 */
function checkEndedAuctions() {
    global $pdo;
    
    // Find auctions that have ended but haven't had winners notified yet
    $stmt = $pdo->query("SELECT i.id, i.name, b.user_id, b.bid_amount 
                        FROM items i 
                        JOIN bids b ON i.id = b.item_id 
                        WHERE i.bid_end_date < NOW() 
                        AND i.notified = 0
                        AND b.bid_amount = (
                            SELECT MAX(bid_amount) 
                            FROM bids 
                            WHERE item_id = i.id
                        )");
    $endedAuctions = $stmt->fetchAll();

    foreach ($endedAuctions as $auction) {
        addNotification(
            $auction['user_id'], 
            "Congratulations! You won the auction for " . $auction['name'] . 
            " with a bid of " . CURRENCY . number_format($auction['bid_amount'], 2)
        );
        
        // Mark auction as notified
        $pdo->prepare("UPDATE items SET notified = 1 WHERE id = ?")
            ->execute([$auction['id']]);
    }
}

// Check for ended auctions on each page load (remove this in production and use cron job instead)
checkEndedAuctions();