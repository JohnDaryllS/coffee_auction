<?php
include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    
    if ($action === 'add') {
        // Validate and sanitize inputs
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $starting_price = (float)$_POST['starting_price'];
        $bid_start_date = $_POST['bid_start_date'];
        $bid_end_date = $_POST['bid_end_date'];
        $is_limited = isset($_POST['is_limited']) ? 1 : 0;
        $quantity = $is_limited ? (int)$_POST['quantity'] : 0;
        
        // Validate required fields
        if (empty($name) || empty($description) || empty($bid_start_date) || empty($bid_end_date)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: admin.php#items-tab');
            exit;
        }
        
        // Validate price
        if ($starting_price <= 0) {
            $_SESSION['error'] = 'Starting price must be greater than 0';
            header('Location: admin.php#items-tab');
            exit;
        }
        
        // Validate dates
        $now = new DateTime();
        $start_date = new DateTime($bid_start_date);
        $end_date = new DateTime($bid_end_date);
        
        if ($start_date <= $now) {
            $_SESSION['error'] = 'Start date must be in the future';
            header('Location: admin.php#items-tab');
            exit;
        }
        
        if ($end_date <= $start_date) {
            $_SESSION['error'] = 'End date must be after start date';
            header('Location: admin.php#items-tab');
            exit;
        }
        
        // Validate quantity for limited items
        if ($is_limited && $quantity <= 0) {
            $_SESSION['error'] = 'Quantity must be at least 1 for limited items';
            header('Location: admin.php#items-tab');
            exit;
        }
        
        // Handle file upload
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Image upload failed';
            header('Location: admin.php#items-tab');
            exit;
        }
        
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error'] = 'File is not an image';
            header('Location: admin.php#items-tab');
            exit;
        }
        
        // Check file size (max 2MB)
        if ($_FILES["image"]["size"] > 2000000) {
            $_SESSION['error'] = 'File is too large (max 2MB)';
            header('Location: admin.php#items-tab');
            exit;
        }
        
        // Allow certain file formats
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $_SESSION['error'] = 'Only JPG, JPEG, PNG & GIF files are allowed';
            header('Location: admin.php#items-tab');
            exit;
        }
        
        // Generate unique filename
        $new_filename = uniqid() . '.' . $imageFileType;
        $target_path = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_path)) {
            // Insert into database
            $stmt = $pdo->prepare("INSERT INTO items 
                                 (name, description, starting_price, bid_start_date, bid_end_date, image, is_limited, quantity, items_sold) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
            $stmt->execute([
                $name, 
                $description, 
                $starting_price, 
                $bid_start_date, 
                $bid_end_date, 
                $new_filename, 
                $is_limited, 
                $quantity
            ]);
            
            $_SESSION['message'] = 'Item added successfully';
            header('Location: admin.php#items-tab');
            exit;
        } else {
            $_SESSION['error'] = 'Error uploading file';
            header('Location: admin.php#items-tab');
            exit;
        }
    } 
    elseif ($action === 'delete') {
        $item_id = (int)$_POST['item_id'];
        
        try {
            $pdo->beginTransaction();
            
            // First get image filename to delete it
            $stmt = $pdo->prepare("SELECT image FROM items WHERE id = ?");
            $stmt->execute([$item_id]);
            $item = $stmt->fetch();
            
            if ($item) {
                // Delete associated bids first
                $stmt = $pdo->prepare("DELETE FROM bids WHERE item_id = ?");
                $stmt->execute([$item_id]);
                
                // Then delete the item
                $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
                $stmt->execute([$item_id]);
                
                // Delete image file
                $image_path = "images/" . $item['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                
                $pdo->commit();
                $_SESSION['message'] = 'Item deleted successfully';
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Error deleting item: ' . $e->getMessage();
        }
        
        header('Location: admin.php#items-tab');
        exit;
    }
    elseif ($action === 'edit') {
        // Handle item editing
        $item_id = (int)$_POST['item_id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $starting_price = (float)$_POST['starting_price'];
        $bid_start_date = $_POST['bid_start_date'];
        $bid_end_date = $_POST['bid_end_date'];
        $is_limited = isset($_POST['is_limited']) ? 1 : 0;
        $quantity = $is_limited ? (int)$_POST['quantity'] : 0;
        
        // Validate inputs
        if (empty($name) || empty($description) || empty($bid_start_date) || empty($bid_end_date)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: admin.php?edit_item='.$item_id.'#items-tab');
            exit;
        }
        
        $now = new DateTime();
        $start_date = new DateTime($bid_start_date);
        $end_date = new DateTime($bid_end_date);
        
        if ($start_date <= $now) {
            $_SESSION['error'] = 'Cannot change start date to past time';
            header('Location: admin.php?edit_item='.$item_id.'#items-tab');
            exit;
        }
        
        if ($end_date <= $start_date) {
            $_SESSION['error'] = 'End date must be after start date';
            header('Location: admin.php?edit_item='.$item_id.'#items-tab');
            exit;
        }
        
        if ($is_limited && $quantity <= 0) {
            $_SESSION['error'] = 'Quantity must be at least 1 for limited items';
            header('Location: admin.php?edit_item='.$item_id.'#items-tab');
            exit;
        }
        
        // Check if new image was uploaded
        if (!empty($_FILES["image"]["name"])) {
            // Handle file upload (same validation as add action)
            $target_dir = "images/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check === false) {
                $_SESSION['error'] = 'File is not an image';
                header('Location: admin.php?edit_item='.$item_id.'#items-tab');
                exit;
            }
            
            if ($_FILES["image"]["size"] > 2000000) {
                $_SESSION['error'] = 'File is too large (max 2MB)';
                header('Location: admin.php?edit_item='.$item_id.'#items-tab');
                exit;
            }
            
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                $_SESSION['error'] = 'Only JPG, JPEG, PNG & GIF files are allowed';
                header('Location: admin.php?edit_item='.$item_id.'#items-tab');
                exit;
            }
            
            // Generate unique filename
            $new_filename = uniqid() . '.' . $imageFileType;
            $target_path = $target_dir . $new_filename;
            
            // Get old image to delete it
            $stmt = $pdo->prepare("SELECT image FROM items WHERE id = ?");
            $stmt->execute([$item_id]);
            $old_image = $stmt->fetchColumn();
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_path)) {
                // Delete old image
                if (file_exists("images/" . $old_image)) {
                    unlink("images/" . $old_image);
                }
                
                // Update database with new image
                $stmt = $pdo->prepare("UPDATE items SET 
                                      name = ?, description = ?, starting_price = ?, 
                                      bid_start_date = ?, bid_end_date = ?, image = ?, 
                                      is_limited = ?, quantity = ?
                                      WHERE id = ?");
                $stmt->execute([
                    $name, $description, $starting_price, 
                    $bid_start_date, $bid_end_date, $new_filename, 
                    $is_limited, $quantity, $item_id
                ]);
                
                $_SESSION['message'] = 'Item updated successfully';
                header('Location: admin.php#items-tab');
                exit;
            } else {
                $_SESSION['error'] = 'Error uploading file';
                header('Location: admin.php?edit_item='.$item_id.'#items-tab');
                exit;
            }
        } else {
            // Update without changing image
            $stmt = $pdo->prepare("UPDATE items SET 
                                  name = ?, description = ?, starting_price = ?, 
                                  bid_start_date = ?, bid_end_date = ?, 
                                  is_limited = ?, quantity = ?
                                  WHERE id = ?");
            $stmt->execute([
                $name, $description, $starting_price, 
                $bid_start_date, $bid_end_date, 
                $is_limited, $quantity, $item_id
            ]);
            
            $_SESSION['message'] = 'Item updated successfully';
            header('Location: admin.php#items-tab');
            exit;
        }
    }
}

// Handle GET requests for editing
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_item'])) {
    $item_id = (int)$_GET['edit_item'];
    $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch();
    
    if ($item) {
        // Return JSON data for edit form
        header('Content-Type: application/json');
        echo json_encode([
            'id' => $item['id'],
            'name' => $item['name'],
            'description' => $item['description'],
            'starting_price' => $item['starting_price'],
            'bid_start_date' => $item['bid_start_date'],
            'bid_end_date' => $item['bid_end_date'],
            'is_limited' => $item['is_limited'],
            'quantity' => $item['quantity'],
            'image' => $item['image']
        ]);
        exit;
    }
}

header('Location: admin.php#items-tab');
exit;