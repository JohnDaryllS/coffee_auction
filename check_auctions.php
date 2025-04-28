<?php
include 'db_connect.php';

$now = date('Y-m-d H:i:s');

// Find auctions that should become active
$stmt = $pdo->prepare("SELECT id FROM items 
                      WHERE bid_start_date <= ? 
                      AND status = 'upcoming'");
$stmt->execute([$now]);

// You could add a status field if you want to track this in the database
// Or just rely on the date comparison in your queries

// For now, we'll just rely on the date comparison in the main queries