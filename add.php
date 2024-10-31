<?php
// Include the database connection file
include 'db.php';

// Initialize error message variable
$error = "";

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';

    // Validate required fields
    if (!empty($username) && !empty($password) && !empty($role)) {
        // // Hash the password (for security)
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL query to insert data into the 'users' table
        $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";

        // Prepare the statement
        if ($stmt = $conn->prepare($query)) {
            // Bind parameters
            $stmt->bind_param("sss", $username, $password, $role);

            // Execute and check for success
            if ($stmt->execute()) {
            header('Location: manage-users.php');
            } else {
                $error = "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            $error = "Error in preparing statement: " . $conn->error;
        }
    } else {
        $error = "Please fill in all required fields.";
    }

    // Close the database connection
    $conn->close();
}
?>