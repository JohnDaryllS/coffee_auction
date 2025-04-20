<?php
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Notifications</title>
    <!-- Include your CSS and other headers -->
</head>
<body>
    <!-- Include your navbar -->
    
    <main class="container">
        <h1>Your Notifications</h1>
        
        <div class="notifications-page">
            <div class="notification-actions">
                <a href="mark_all_read.php" class="btn btn-outline">Mark all as read</a>
            </div>
            
            <div class="notifications-list">
                <?php if (empty($notifications)): ?>
                    <div class="empty-notifications">You have no notifications</div>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="notification-item <?= $notification['is_read'] ? 'read' : 'unread' ?>">
                            <div class="notification-message">
                                <?= htmlspecialchars($notification['message']) ?>
                            </div>
                            <div class="notification-meta">
                                <span class="notification-time">
                                    <?= time_elapsed_string($notification['created_at']) ?>
                                </span>
                                <?php if (!$notification['is_read']): ?>
                                    <a href="mark_read.php?id=<?= $notification['id'] ?>" class="mark-read-link">
                                        Mark as read
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- Include your footer -->
</body>
</html>