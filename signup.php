<?php
// Include the db.php file to connect to the database
include 'db.php';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get the username, password, and role from the form
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  $role = $_POST['role'];

  // Check if the username and password are not empty
  if (empty($username) || empty($password)) {
    echo "Username and password cannot be empty.";
    exit; // Stop further execution
  }

  // Query the database to insert the new user
  $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
  
  // Execute the query and check for errors
  if (mysqli_query($conn, $query)) {
    // Redirect to the login page
    header('Location: index.html');
    exit;
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}
?>
