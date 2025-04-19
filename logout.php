<?php
include 'db_connect.php';

// Destroy all session data
$_SESSION = array();
session_destroy();

// Redirect to homepage
header('Location: index.php');
exit;
?>