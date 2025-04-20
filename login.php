<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Coffee Auction</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            <h2>Login to Coffee Auction</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    if ($_GET['error'] == 'invalid') echo "Invalid email or password";
                    if ($_GET['error'] == 'pending') echo "Your account is pending admin verification.";
                    ?>
                </div>
            <?php endif; ?>
            
            <form action="process_login.php" method="post">
                <div class="form-group">
                    <label for="login-type">Login As:</label>
                    <select id="login-type" name="login_type" class="form-control">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            
            <div class="auth-footer">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </main>

    <footer class="modern-footer">
    <div class="footer-container">
        <!-- Footer Columns -->
        <div class="footer-grid">
            <!-- About Column -->
            <div class="footer-column">
                <div class="footer-logo">
                    <img src="images/crop.png" alt="Coffee Auction" class="logo-img">
                    <span class="logo-texts">Coffee Auction</span>
                </div>
                <p class="footer-about">
                    Revolutionizing how specialty coffee reaches enthusiasts worldwide. 
                    Connecting farmers directly with coffee lovers through our unique auction platform.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Quick Links Column -->
            <div class="footer-column">
                <h3 class="footer-title">Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="auction.php">Current Auctions</a></li>
                    <li><a href="about.php">Our Story</a></li>
                    <li><a href="#">How It Works</a></li>
                    <li><a href="#">Coffee Guides</a></li>
                </ul>
            </div>

            <!-- Contact Column -->
            <div class="footer-column">
                <h3 class="footer-title">Contact Us</h3>
                <ul class="contact-info">
                    <li class="contact-item">
                        <i class="fas fa-map-marker-alt contact-icon"></i>
                        <span>123 Coffee Street, Portland, OR 97205</span>
                    </li>
                    <li class="contact-item">
                        <i class="fas fa-phone-alt contact-icon"></i>
                        <span>+1 (503) 555-0199</span>
                    </li>
                    <li class="contact-item">
                        <i class="fas fa-envelope contact-icon"></i>
                        <span>info@coffeeauction.com</span>
                    </li>
                    <li class="contact-item">
                        <i class="fas fa-clock contact-icon"></i>
                        <span>Mon-Fri: 9AM - 5PM</span>
                    </li>
                </ul>
            </div>

            <!-- Newsletter Column -->
            <div class="footer-column">
                <h3 class="footer-title">Newsletter</h3>
                <p class="newsletter-text">
                    Subscribe to get updates on new auctions, coffee tips, and exclusive offers.
                </p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Your email address" required>
                    <button type="submit" class="subscribe-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> Coffee Auction. All rights reserved.
            </div>
            <div class="legal-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
</body>
</html>