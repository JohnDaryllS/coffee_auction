<?php
include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_POST['user_id'];
    $action = $_POST['action'];
    
    try {
        switch ($action) {
            case 'approve':
                $stmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
                $stmt->execute([$user_id]);
                
                // Add notification to user
                addNotification($user_id, "Your account has been approved! You can now place bids.");
                
                $_SESSION['message'] = 'User approved successfully';
                break;
                
            case 'suspend':
                $stmt = $pdo->prepare("UPDATE users SET status = 'suspended' WHERE id = ?");
                $stmt->execute([$user_id]);
                
                // Add notification to user
                addNotification($user_id, "Your account has been suspended. Please contact support.");
                
                $_SESSION['message'] = 'User suspended successfully';
                break;
                
            case 'activate':
                $stmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
                $stmt->execute([$user_id]);
                
                // Add notification to user
                addNotification($user_id, "Your account has been reactivated. Welcome back!");
                
                $_SESSION['message'] = 'User activated successfully';
                break;
                
            case 'reset_password':
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];
                
                if ($new_password !== $confirm_password) {
                    $_SESSION['error'] = 'Passwords do not match';
                    break;
                }
                
                if (strlen($new_password) < 8) {
                    $_SESSION['error'] = 'Password must be at least 8 characters';
                    break;
                }
                
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);
                
                // Add notification to user
                addNotification($user_id, "Your password has been reset by an administrator.");
                
                $_SESSION['message'] = 'Password reset successfully';
                break;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
    
    header('Location: admin.php');
    exit;
}

// If not a POST request, redirect to admin page
header('Location: admin.php');
exit;