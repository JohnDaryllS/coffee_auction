<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Coffee Auction</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <div class="logo">
                <span class="logo-icon">☕</span>
                <span class="logo-text">Coffee Auction</span>
            </div>
        </div>
        <div class="navbar-center">
            <a href="index.php" class="nav-link">Home</a>
            <a href="auction.php" class="nav-link">Auction</a>
            <a href="about.php" class="nav-link">About</a>
        </div>
        <div class="navbar-right">
            <a href="login.php" class="btn btn-outline">Login</a>
            <a href="register.php" class="btn btn-primary">Register</a>
        </div>
    </nav>

    <main class="container auth-container">
        <div class="auth-card">
            <h2>Create an Account</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    Registration successful. Please wait for admin approval.
                </div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    if ($_GET['error'] == 'email') echo "Email already exists";
                    if ($_GET['error'] == 'password') echo "Passwords do not match";
                    ?>
                </div>
            <?php endif; ?>
            
            <form action="process_register.php" method="post">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
            
            <div class="auth-footer">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <span class="logo-icon">☕</span>
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