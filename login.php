<?php
// Include the db.php file to connect to the database
include 'db.php';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get the username and password from the form
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Query the database to check if the username and password exist
  $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
  $result = mysqli_query($conn, $query);

  // Check if the query returned a result
  if (mysqli_num_rows($result) > 0) {
    // Get the user's data from the database
    $user_data = mysqli_fetch_assoc($result);
    $id = $user_data['id'];
    $role = $user_data['role'];

    // Set the session variables
    session_start();
    $_SESSION['user_id'] = $id;
    $_SESSION['role'] = $role;

    // Redirect to the appropriate dashboard based on the user's role
    if($role == 'superadmin'){
      header('Location: superadmin.php');
    }
    elseif ($role == 'admin') {
      header('Location: admin.php');
    } else {
      header('Location: student.php');
    }
    exit;
  } else {
    // If the username and password are incorrect, display an error message
    $error = "Invalid username or password";
    header('Location: index.html');
  }
}
?>

<!-- HTML code to display the login form and error message
<!DOCTYPE html>
<html>
<head>
  <title>Login - Printing Services</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Login</h1>
  <form action="login.php" method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username"><br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password"><br><br>
    <input type="submit" value="Login">
  </form>
</body>
</html> -->