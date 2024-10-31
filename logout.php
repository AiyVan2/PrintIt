<?php
// Include the db.php file to connect to the database
include 'db.php';

// Destroy the session
session_destroy();

// Redirect to the login page
header('Location: index.html');
exit;
?>