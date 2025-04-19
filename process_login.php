<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $loginType = $_POST['login_type'];
    
    if ($loginType === 'admin') {
        // Check against fixed admin credentials
        $admin1_email = 'admincoffeeauction1@coffeeauction.com';
        $admin1_pass = 'secretadmincoffeeauctionone';
        $admin2_email = 'admincoffeeauction2@coffeeauction.com';
        $admin2_pass = 'secretadmincoffeeauctiontwo';
        
        if (($email === $admin1_email && $password === $admin1_pass) || 
            ($email === $admin2_email && $password === $admin2_pass)) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: admin.php');
            exit;
        } else {
            header('Location: login.php?error=invalid');
            exit;
        }
    } else {
        // Regular user login
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'approved') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['fullname'];
                header('Location: index.php');
                exit;
            } else {
                header('Location: login.php?error=pending');
                exit;
            }
        } else {
            header('Location: login.php?error=invalid');
            exit;
        }
    }
}
?>