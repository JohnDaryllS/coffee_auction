<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Coffee Auction</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="images/crop.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    /* Modern About Page Styles */
    .about-hero {
        position: relative;
        height: 80vh;
        min-height: 600px;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

    .hero-video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 1;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to right, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 100%);
        z-index: 2;
    }

    .hero-content {
        position: relative;
        z-index: 3;
        color: white;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 5%;
    }

    .hero-content h1 {
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
        line-height: 1.2;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
        animation: fadeInUp 1s ease;
    }

    .hero-content p {
        font-size: 1.3rem;
        max-width: 600px;
        margin-bottom: 2rem;
        opacity: 0.9;
        animation: fadeInUp 1s ease 0.3s forwards;
        opacity: 0;
    }

    .hero-btn {
        animation: fadeInUp 1s ease 0.6s forwards;
        opacity: 0;
    }

    /* Mission Section */
    .mission-section {
        padding: 6rem 0;
        background-color: #f9f5f0;
    }

    .mission-container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
        padding: 0 5%;
    }

    .mission-image {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        transform: perspective(1000px) rotateY(-5deg);
        transition: transform 0.5s ease;
    }

    .mission-image:hover {
        transform: perspective(1000px) rotateY(0deg);
    }

    .mission-image img {
        width: 100%;
        height: auto;
        display: block;
    }

    .mission-content h2 {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        position: relative;
    }

    .mission-content h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 80px;
        height: 4px;
        background-color: var(--secondary-color);
    }

    .mission-content p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #555;
        margin-bottom: 1.5rem;
    }

    /* Values Section */
    .values-section {
        padding: 6rem 0;
        background-color: white;
    }

    .section-header {
        text-align: center;
        margin-bottom: 4rem;
    }

    .section-header h2 {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .section-header p {
        max-width: 700px;
        margin: 0 auto;
        color: #666;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 5%;
    }

    .value-card {
        background: white;
        border-radius: 12px;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        text-align: center;
    }

    .value-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    }

    .value-icon {
        width: 80px;
        height: 80px;
        background-color: var(--accent-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: var(--primary-color);
    }

    .value-card h3 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: var(--primary-color);
    }

    /* Team Section */
    .team-section {
        padding: 6rem 0;
        background-color: #f9f5f0;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 5%;
    }

    .team-member {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .team-member:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    }

    .member-photo {
        height: 350px;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .social-links {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 1.5rem;
        background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
        display: flex;
        justify-content: center;
        gap: 1rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .team-member:hover .social-links {
        opacity: 1;
    }

    .social-links a {
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        background-color: var(--primary-color);
        transform: translateY(-3px);
    }

    .member-info {
        padding: 1.5rem;
        text-align: center;
    }

    .member-info h3 {
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
    }

    .position {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 1rem;
        display: block;
    }

    /* Stats Section */
    .stats-section {
        padding: 4rem 0;
        background-color: var(--primary-color);
        color: white;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 5%;
        text-align: center;
    }

    .stat-item {
        padding: 2rem;
    }

    .stat-number {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    /* CTA Section */
    .cta-section {
        padding: 6rem 0;
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/coffee-beans-bg.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: white;
        text-align: center;
    }

    .cta-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 5%;
    }

    .cta-content h2 {
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
    }

    .cta-content p {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Styles */
    @media (max-width: 992px) {
        .mission-container {
            grid-template-columns: 1fr;
            gap: 3rem;
        }
        
        .mission-image {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .hero-content h1 {
            font-size: 2.8rem;
        }
    }

    @media (max-width: 768px) {
        .hero-content h1 {
            font-size: 2.3rem;
        }
        
        .hero-content p {
            font-size: 1.1rem;
        }
        
        .mission-content h2 {
            font-size: 2rem;
        }
    }

    @media (max-width: 576px) {
        .about-hero {
            height: 70vh;
            min-height: 500px;
        }
        
        .hero-content h1 {
            font-size: 2rem;
        }
        
        .section-header h2 {
            font-size: 2rem;
        }
        
        .value-card {
            padding: 2rem;
        }
    }
</style>
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
            <a href="about.php" class="nav-link active">About</a>
        </div>
        <div class="navbar-right">
        <div class="navbar-right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="notification-container">
                    <span class="user-greeting">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    <?php
                    // Get unread notification count
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE");
                    $stmt->execute([$_SESSION['user_id']]);
                    $unreadCount = $stmt->fetchColumn();
                    ?>
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span class="notification-badge"><?= $unreadCount ?></span>
                        <?php endif; ?>
                        <div class="notification-dropdown">
                            <div class="notification-header">
                                <h4>Notifications</h4>
                                <a href="mark_all_read.php" class="mark-all-read">Mark all as read</a>
                            </div>
                            <div class="notification-list">
                                <?php
                                $notifications = getUserNotifications($_SESSION['user_id']);
                                
                                if (empty($notifications)) {
                                    echo '<div class="notification-item empty">No notifications</div>';
                                } else {
                                    foreach ($notifications as $notification) {
                                        $class = $notification['is_read'] ? 'read' : 'unread';
                                        echo '<div class="notification-item '.$class.'">';
                                        echo htmlspecialchars($notification['message']);
                                        
                                        // Add exact time along with relative time
                                        $createdAt = new DateTime($notification['created_at']);
                                        $createdAt->setTimezone(new DateTimeZone('Asia/Manila'));
                                        echo '<div class="notification-time" title="'.$createdAt->format('M j, Y h:i A').'">';
                                        echo time_elapsed_string($notification['created_at']);
                                        echo '</div>';
                                        
                                        if (!$notification['is_read']) {
                                            echo '<a href="mark_read.php?id='.$notification['id'].'" class="mark-read">Mark read</a>';
                                        }
                                        echo '</div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <a href="logout.php" class="btn btn-outline">Logout</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="register.php" class="btn btn-primary">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <main>
        <!-- Hero Section with Video Background -->
        <section class="about-hero">
            <video autoplay muted loop class="hero-video">
                <source src="videos/coffee-beans.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <h1>Revolutionizing Coffee Trade</h1>
                <p>We connect specialty coffee growers directly with enthusiasts worldwide through our transparent auction platform.</p>
                <a href="auction.php" class="btn btn-primary hero-btn">Explore Auctions</a>
            </div>
        </section>

        <!-- Mission Section -->
        <section class="mission-section">
            <div class="mission-container">
                <div class="mission-image">
                    <img src="images/Untitled-1.jpg" alt="Coffee Farmer">
                </div>
                <div class="mission-content">
                    <h2>Our Mission</h2>
                    <p>At Coffee Auction, we're committed to creating a fair and transparent marketplace that benefits both coffee growers and buyers. Our platform eliminates middlemen, ensuring farmers receive fair compensation for their exceptional crops while buyers get access to the finest specialty coffees.</p>
                    <p>We believe in sustainable practices, direct relationships, and celebrating the hard work that goes into every bean. By connecting growers directly with roasters and enthusiasts, we're changing the way specialty coffee is traded.</p>
                </div>
            </div>
        </section>

        <!-- Values Section -->
        <section class="values-section">
            <div class="section-header">
                <h2>Our Core Values</h2>
                <p>These principles guide everything we do at Coffee Auction</p>
            </div>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Transparency</h3>
                    <p>Full visibility into pricing, origins, and transactions for all participants in the supply chain.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>Sustainability</h3>
                    <p>Promoting environmentally friendly farming practices and long-term relationships.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <h3>Fairness</h3>
                    <p>Ensuring equitable compensation for farmers and fair prices for buyers.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Quality</h3>
                    <p>Focusing exclusively on specialty grade coffees with cupping scores of 85+.</p>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section class="team-section">
            <div class="section-header">
                <h2>Meet Our Team</h2>
                <p>The passionate individuals behind Coffee Auction</p>
            </div>
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-photo" style="background-image: url('images/23.jpg');">
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3>Alex Johnson</h3>
                        <span class="position">Founder & CEO</span>
                        <p>Former Q-grader with 15 years in specialty coffee industry</p>
                    </div>
                </div>
                <div class="team-member">
                    <div class="member-photo" style="background-image: url('images/24.jfif');">
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3>Maria Garcia</h3>
                        <span class="position">Head of Sourcing</span>
                        <p>Direct relationships with farms across Latin America</p>
                    </div>
                </div>
                <div class="team-member">
                    <div class="member-photo" style="background-image: url('images/25.jfif');">
                        <div class="social-links">
                            <a href="#"><i class="fab fa-github"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3>Jamie Smith</h3>
                        <span class="position">Tech Lead</span>
                        <p>Building seamless auction experiences</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <!-- <section class="stats-section">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number" data-count="200">0</div>
                    <div class="stat-label">Farms Connected</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-count="1500">0</div>
                    <div class="stat-label">Auctions Completed</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-count="98">0</div>
                    <div class="stat-label">Customer Satisfaction</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-count="15">0</div>
                    <div class="stat-label">Countries Served</div>
                </div>
            </div>
        </section> -->

        <footer class="modern-footer">
        <!-- Footer content remains the same -->
    </footer>

    <script>
        // Animate stats counting
        function animateStats() {
            const statNumbers = document.querySelectorAll('.stat-number');
            const speed = 200;
            
            statNumbers.forEach(stat => {
                const target = +stat.getAttribute('data-count');
                const count = +stat.innerText;
                const increment = target / speed;
                
                if (count < target) {
                    stat.innerText = Math.ceil(count + increment);
                    setTimeout(animateStats, 1);
                } else {
                    stat.innerText = target;
                }
            });
        }
        
        // Trigger animation when stats section is in view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateStats();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            observer.observe(statsSection);
        }
    </script>
</body>
</html>