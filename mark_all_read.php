<?php
include 'db_connect.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit;
?>