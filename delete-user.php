<?php
// Include the db.php file to connect to the database
include 'db.php';
session_start();
// Check if the user is logged in and has the admin role
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'superadmin') {
  $user_id = $_SESSION['user_id'];
} else {
  header('Location: index.html');
  exit;
}

// Get the user ID from the URL
$user_id_to_delete = $_GET['id'];

// Query the database to delete the user
$query = "DELETE FROM users WHERE id = '$user_id_to_delete'";
mysqli_query($conn, $query);

// Redirect to the manage users page
header('Location: manage-users.php');
exit;
?>