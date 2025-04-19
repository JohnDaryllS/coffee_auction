<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Coffee Auction</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    /* Modern About Page Styles */
.about-hero {
    position: relative;
    height: 80vh;
    overflow: hidden;
}

.hero-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    z-index: 1;
    animation: zoomIn 20s infinite alternate;
}

@keyframes zoomIn {
    from { transform: scale(1); }
    to { transform: scale(1.1); }
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to right, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%);
    z-index: 2;
}

.hero-content {
    position: relative;
    z-index: 3;
    color: white;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    padding: 0 10%;
    max-width: 1200px;
    margin: 0 auto;
}

.hero-content h1 {
    font-size: 4rem;
    margin-bottom: 1rem;
    line-height: 1.2;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.hero-content p {
    font-size: 1.5rem;
    max-width: 600px;
    margin-bottom: 2rem;
}

.scroll-down {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    font-size: 2rem;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0) translateX(-50%); }
    40% { transform: translateY(-20px) translateX(-50%); }
    60% { transform: translateY(-10px) translateX(-50%); }
}

/* Section Styles */
.about-story, .about-team, .about-cta {
    padding: 6rem 0;
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-subtitle {
    display: block;
    color: var(--primary-color);
    font-weight: 600;
    letter-spacing: 2px;
    margin-bottom: 1rem;
    text-transform: uppercase;
    font-size: 0.9rem;
}

.section-title {
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
    position: relative;
    display: inline-block;
}

.divider {
    width: 80px;
    height: 3px;
    background-color: var(--secondary-color);
    margin: 0 auto;
}

/* Timeline */
.story-timeline {
    position: relative;
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem 0;
}

.story-timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 2px;
    height: 100%;
    background-color: var(--accent-color);
}

.timeline-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 3rem;
    position: relative;
}

.timeline-year {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 1.5rem;
    font-weight: 600;
    z-index: 2;
    box-shadow: 0 10px 20px rgba(111, 78, 55, 0.2);
}

.timeline-content {
    width: calc(50% - 100px);
    padding: 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    position: relative;
}

.timeline-content::before {
    content: '';
    position: absolute;
    top: 30px;
    width: 20px;
    height: 20px;
    background: white;
    transform: rotate(45deg);
}

.timeline-item:nth-child(odd) .timeline-content {
    margin-left: auto;
}

.timeline-item:nth-child(odd) .timeline-content::before {
    right: -10px;
}

.timeline-item:nth-child(even) .timeline-content {
    text-align: right;
}

.timeline-item:nth-child(even) .timeline-content::before {
    left: -10px;
}

/* Stats Section */
.about-stats {
    background-color: var(--primary-color);
    color: white;
    padding: 4rem 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
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

/* Team Section */
.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.team-member {
    text-align: center;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.team-member:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
}

.member-photo {
    height: 300px;
    background-size: cover;
    background-position: center;
    position: relative;
}

.social-links {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 1rem;
    background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.team-member:hover .social-links {
    opacity: 1;
}

.social-links a {
    color: white;
    margin: 0 0.5rem;
    font-size: 1.2rem;
}

.team-member h3 {
    margin: 1.5rem 0 0.5rem;
    font-size: 1.5rem;
}

.position {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 1rem;
}

.bio {
    padding: 0 1.5rem 1.5rem;
    color: #666;
}

/* CTA Section */
.about-cta {
    text-align: center;
    background-color: var(--light-color);
}

.about-cta h2 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.about-cta p {
    font-size: 1.2rem;
    max-width: 600px;
    margin: 0 auto 2rem;
    color: #666;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .hero-content h1 {
        font-size: 2.5rem;
    }
    
    .story-timeline::before {
        left: 60px;
    }
    
    .timeline-item {
        flex-direction: column;
    }
    
    .timeline-year {
        width: 80px;
        height: 80px;
        margin-bottom: 1rem;
    }
    
    .timeline-content {
        width: calc(100% - 100px);
        margin-left: 100px !important;
        text-align: left !important;
    }
    
    .timeline-content::before {
        left: -10px !important;
        right: auto !important;
        top: -10px;
        transform: rotate(45deg);
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-greeting">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="register.php" class="btn btn-primary">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="about-container">
        <!-- Hero Section -->
        <section class="about-hero">
            <div class="hero-content">
                <h1>More Than Just Coffee</h1>
                <p>We're revolutionizing how specialty coffee reaches enthusiasts worldwide</p>
                <div class="scroll-down">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
            <div class="hero-overlay"></div>
            <div class="hero-image" style="background-image: url('images/21.jpg');"></div>
        </section>

        <!-- Story Section -->
        <section class="about-story">
            <div class="container">
                <div class="section-header">
                    <span class="section-subtitle">Our Journey</span>
                    <h2 class="section-title">From Bean to Cup</h2>
                    <div class="divider"></div>
                </div>
                
                <div class="story-timeline">
                    <div class="timeline-item">
                        <div class="timeline-year">2018</div>
                        <div class="timeline-content">
                            <h3>Founded in Portland</h3>
                            <p>Started as a small group of coffee enthusiasts wanting to connect farmers directly with consumers</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-year">2020</div>
                        <div class="timeline-content">
                            <h3>First Online Auction</h3>
                            <p>Launched our digital platform featuring rare lots from Ethiopia and Colombia</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-year">2023</div>
                        <div class="timeline-content">
                            <h3>Global Reach</h3>
                            <p>Now working with over 200 specialty farms across 15 countries</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="about-stats">
            <div class="container">
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
            </div>
        </section>

        <!-- Team Section -->
        <section class="about-team">
            <div class="container">
                <div class="section-header">
                    <span class="section-subtitle">Meet The Team</span>
                    <h2 class="section-title">Coffee Passionates</h2>
                    <div class="divider"></div>
                </div>
                
                <div class="team-grid">
                    <div class="team-member">
                        <div class="member-photo" style="background-image: url('images/23.jpg');">
                            <div class="social-links">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                        <h3>Alex Johnson</h3>
                        <p class="position">Founder & CEO</p>
                        <p class="bio">Former Q-grader with 15 years in specialty coffee industry</p>
                    </div>
                    <div class="team-member">
                        <div class="member-photo" style="background-image: url('images/24.jfif');">
                            <div class="social-links">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                        <h3>Maria Garcia</h3>
                        <p class="position">Head of Sourcing</p>
                        <p class="bio">Direct relationships with farms across Latin America</p>
                    </div>
                    <div class="team-member">
                        <div class="member-photo" style="background-image: url('images/25.jfif');">
                            <div class="social-links">
                                <a href="#"><i class="fab fa-github"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                        <h3>Jamie Smith</h3>
                        <p class="position">Tech Lead</p>
                        <p class="bio">Building seamless auction experiences</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="about-cta">
            <div class="container">
                <h2>Ready to Experience Premium Coffee?</h2>
                <p>Join our community of coffee enthusiasts and discover rare beans from around the world</p>
                <a href="register.php" class="btn btn-primary">Get Started</a>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="images/crop.png" alt="Coffee-Auction" style="width:50px;">
                <span class="logo-text">Coffee Auction</span>
            </div>
            <div class="footer-links">
                <a href="about.php">About Us</a>
                <a href="#">Terms</a>
                <a href="#">Privacy</a>
                <a href="#">Contact</a>
            </div>
        </div>
        <div class="footer-copyright">
            &copy; <?php echo date('Y'); ?> Coffee Auction. All rights reserved.
        </div>
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
        window.addEventListener('scroll', function() {
            const statsSection = document.querySelector('.about-stats');
            const position = statsSection.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.3;
            
            if (position < screenPosition) {
                animateStats();
                window.removeEventListener('scroll', this);
            }
        });
    </script>
</body>
</html>