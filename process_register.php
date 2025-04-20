<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    $errors = [];
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        header('Location: register.php?error=password');
        exit;
    }
    
    // Validate phone number
    if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        header('Location: register.php?error=phone');
        exit;
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header('Location: register.php?error=email');
        exit;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        // Insert new user with 'pending' status
        $stmt = $pdo->prepare("INSERT INTO users (fullname, email, phone, password, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$fullname, $email, $phone, $hashed_password]);
        
        // Get the new user ID
        $user_id = $pdo->lastInsertId();
        
        // Add welcome notification
        addNotification($user_id, "Welcome to Coffee Auction! Your account is pending admin approval.");
        
        // Redirect to success page
        header('Location: register.php?success=1');
        exit;
    } catch (PDOException $e) {
        // Log error and redirect
        error_log("Registration error: " . $e->getMessage());
        header('Location: register.php?error=1');
        exit;
    }
} else {
    // Not a POST request, redirect to register page
    header('Location: register.php');
    exit;
}
?>