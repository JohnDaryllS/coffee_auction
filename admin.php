<?php 
include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Display messages
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Get current datetime for the form
$currentDateTime = date('Y-m-d\TH:i');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Coffee Auction</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar admin-nav">
        <div class="navbar-left">
            <div class="logo">
                <img src="images/crop.png" alt="Coffee-Auction" style="width:50px;">
                <span class="logo-text">Coffee Auction</span>
            </div>
        </div>
        <div class="navbar-center">
            <a href="admin.php?tab=users" class="nav-link <?= ($_GET['tab'] ?? 'users') === 'users' ? 'active' : '' ?>">Admin Dashboard</a>
        </div>
        <div class="navbar-right">
            <a href="logout.php" class="btn btn-outline">Logout</a>
        </div>
    </nav>

    <main class="container admin-container">
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <h1>Admin Dashboard</h1>
        
        <div class="admin-tabs">
            <button class="tab-btn <?= ($_GET['tab'] ?? 'users') === 'users' ? 'active' : '' ?>" data-tab="users">User Management</button>
            <button class="tab-btn <?= ($_GET['tab'] ?? '') === 'items' ? 'active' : '' ?>" data-tab="items">Coffee Auctions</button>
        </div>
        
        <section class="tab-content <?= ($_GET['tab'] ?? 'users') === 'users' ? 'active' : '' ?>" id="users-tab">
            <h2>User Management</h2>
            
            <div class="admin-actions">
                <button id="search-users-btn" class="btn btn-outline">
                    <i class="fas fa-search"></i> Search Users
                </button>
                <div id="search-users-form" style="display: <?= isset($_GET['search']) ? 'block' : 'none' ?>; margin-top: 1rem;">
                    <form method="get" action="admin.php" class="search-form">
                        <input type="hidden" name="tab" value="users">
                        <div class="form-group">
                            <input type="text" name="search" placeholder="Search by name, email or phone" 
                                   class="form-control" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <?php if (!empty($_GET['search'])): ?>
                                <a href="admin.php?tab=users" class="btn btn-outline">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php
                // Get search parameters
                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                $status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

                // Base query
                $query = "SELECT * FROM users WHERE role = 'user'";
                $params = [];

                // Add conditions
                if (!empty($search)) {
                    $query .= " AND (fullname LIKE ? OR email LIKE ? OR phone LIKE ?)";
                    array_push($params, "%$search%", "%$search%", "%$search%");
                }

                // Add status filter
                $valid_statuses = ['pending', 'approved', 'suspended'];
                if (!empty($status_filter) && in_array($status_filter, $valid_statuses)) {
                    $query .= " AND status = ?";
                    $params[] = $status_filter;
                }

                // Add sorting
                $query .= " ORDER BY status, created_at DESC";

                try {
                    $stmt = $pdo->prepare($query);
                    $stmt->execute($params);
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    die("Database error: " . $e->getMessage());
                }
            ?>
            
            <?php if (count($users) > 0): ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['fullname']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['phone']) ?></td>
                                    <td>
                                        <span class="status-badge <?= $user['status'] ?>">
                                            <?= ucfirst($user['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                    <td class="actions-cell">
                                        <div class="dropdown">
                                            <button class="btn btn-outline btn-small dropdown-toggle">Actions</button>
                                            <div class="dropdown-content">
                                                <?php if ($user['status'] == 'pending'): ?>
                                                    <form action="process_user.php" method="post">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" name="action" value="approve" class="dropdown-link">Approve User</button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <button class="dropdown-link" onclick="openPasswordModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['fullname']) ?>')">Reset Password</button>
                                                
                                                <?php if ($user['status'] == 'approved'): ?>
                                                    <form action="process_user.php" method="post">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" name="action" value="suspend" class="dropdown-link">Suspend User</button>
                                                    </form>
                                                <?php else: ?>
                                                    <form action="process_user.php" method="post">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" name="action" value="activate" class="dropdown-link">Activate User</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>
        </section>
        
        <section class="tab-content <?= ($_GET['tab'] ?? '') === 'items' ? 'active' : '' ?>" id="items-tab">
            <div class="admin-section-header">
                <h2>Coffee Auctions</h2>
                <button id="add-item-btn" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Item
                </button>
            </div>
            
            <div id="add-item-form" class="form-card" style="display: none;">
                <h3>Add New Coffee Auction</h3>
                <form action="process_item.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="item-name">Coffee Name</label>
                        <input type="text" id="item-name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="item-description">Description</label>
                        <textarea id="item-description" name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="item-price">Starting Price (₱)</label>
                        <input type="number" id="item-price" name="starting_price" class="form-control" min="0.01" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="bid-end-date">Bid End Date/Time</label>
                        <input type="datetime-local" id="bid-end-date" name="bid_end_date" 
                               class="form-control" min="<?= $currentDateTime ?>" required>
                        <small>Set the date and time when bidding will close</small>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="is-limited" name="is_limited" value="1">
                            This is a limited quantity item
                        </label>
                    </div>
                    
                    <div class="form-group" id="quantity-group" style="display: none;">
                        <label for="item-quantity">Available Quantity</label>
                        <input type="number" id="item-quantity" name="quantity" class="form-control" min="1" value="1">
                    </div>
                    
                    <div class="form-group">
                        <label for="item-image">Image</label>
                        <input type="file" id="item-image" name="image" class="form-control" accept="image/*" required>
                        <small>Recommended size: 600x400 pixels</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" id="cancel-add-item" class="btn btn-outline">Cancel</button>
                        <button type="submit" name="action" value="add" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>End Date</th>
                            <th>Inventory</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM items ORDER BY bid_end_date ASC");
                        while ($item = $stmt->fetch()): 
                            $now = new DateTime();
                            $end_date = new DateTime($item['bid_end_date']);
                            $is_active = $end_date > $now;
                            $status_class = $is_active ? 'status-active' : 'status-ended';
                        ?>
                            <tr class="<?= !$is_active ? 'auction-ended' : '' ?>">
                                <td><?= $item['id'] ?></td>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td>₱<?= number_format($item['starting_price'], 2) ?></td>
                                <td>
                                    <?php if ($item['is_limited']): ?>
                                        <span class="limited-badge">Limited</span>
                                    <?php else: ?>
                                        <span class="unlimited-badge">Unlimited</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge <?= $status_class ?>">
                                        <?= $is_active ? 'Active' : 'Ended' ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $end_date->format('M j, Y H:i') ?>
                                    <?php if ($is_active): ?>
                                        <div class="time-remaining">
                                            (<?php 
                                            $interval = $now->diff($end_date);
                                            echo $interval->format('%a days %h hours %i minutes left');
                                            ?>)
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item['is_limited']): ?>
                                        <?= ($item['quantity'] - $item['items_sold']) . ' / ' . $item['quantity'] ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <img src="images/<?= htmlspecialchars($item['image']) ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>" 
                                         class="item-thumbnail">
                                </td>
                                <td>
                                    <a href="admin.php?edit_item=<?= $item['id'] ?>#items-tab" 
                                       class="btn btn-outline btn-small">Edit</a>
                                    <form action="process_item.php" method="post" class="inline-form">
                                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                        <button type="submit" name="action" value="delete" 
                                                class="btn btn-error btn-small">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Password Reset Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Reset Password for <span id="userName"></span></h3>
            <form action="process_user.php" method="post" id="passwordForm">
                <input type="hidden" name="user_id" id="modalUserId">
                <input type="hidden" name="action" value="reset_password">
                
                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <input type="password" id="newPassword" name="new_password" class="form-control" required>
                    <small>Minimum 8 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirm_password" class="form-control" required>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('passwordModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Activate tab from URL
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || 'users';
            
            // Activate the requested tab
            document.querySelector(`.tab-btn[data-tab="${activeTab}"]`).click();
            
            // Show search form if coming from search
            if (activeTab === 'users' && urlParams.has('search')) {
                document.getElementById('search-users-form').style.display = 'block';
            }
        });
        
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class from all buttons and tabs
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked button and corresponding tab
                btn.classList.add('active');
                const tabId = btn.getAttribute('data-tab') + '-tab';
                document.getElementById(tabId).classList.add('active');
                
                // Update URL without reload
                const newUrl = new URL(window.location);
                newUrl.searchParams.set('tab', btn.getAttribute('data-tab'));
                window.history.pushState({}, '', newUrl);
            });
        });
        
        // Toggle limited quantity fields
        document.getElementById('is-limited').addEventListener('change', function() {
            document.getElementById('quantity-group').style.display = this.checked ? 'block' : 'none';
            if (this.checked) {
                document.getElementById('item-quantity').required = true;
            } else {
                document.getElementById('item-quantity').required = false;
            }
        });
        
        // Add item form toggle
        document.getElementById('add-item-btn').addEventListener('click', function() {
            document.getElementById('add-item-form').style.display = 'block';
        });
        
        document.getElementById('cancel-add-item').addEventListener('click', function() {
            document.getElementById('add-item-form').style.display = 'none';
        });
        
        // Set minimum datetime for the end date picker
        const endDateInput = document.getElementById('bid-end-date');
        if (endDateInput) {
            const now = new Date();
            // Add 1 hour minimum duration for auctions
            now.setHours(now.getHours() + 1);
            const minDate = now.toISOString().slice(0, 16);
            endDateInput.min = minDate;
        }
        
        // Toggle search form
        document.getElementById('search-users-btn').addEventListener('click', function() {
            const form = document.getElementById('search-users-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });
        
        // Password modal
        function openPasswordModal(userId, userName) {
            document.getElementById('modalUserId').value = userId;
            document.getElementById('userName').textContent = userName;
            document.getElementById('passwordModal').style.display = 'block';
        }
        
        // Close modal when clicking X
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('passwordModal').style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('passwordModal')) {
                document.getElementById('passwordModal').style.display = 'none';
            }
        });
    </script>
</body>
</html>