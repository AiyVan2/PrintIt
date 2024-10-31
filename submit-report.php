<?php
// Include the database connection file
include 'db.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize the form data
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $issue_type = isset($_POST['issue_type']) ? $_POST['issue_type'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';

    // Validate required fields
    if ($order_id > 0 && !empty($issue_type)) {
        // Prepare SQL query to insert data into the 'reports' table
        $query = "INSERT INTO report_inquiry (order_id, issue_type, description) VALUES (?, ?, ?)";

        // Prepare the statement
        if ($stmt = $conn->prepare($query)) {
            // Bind parameters
            $stmt->bind_param("iss", $order_id, $issue_type, $description);

            // Execute and check for success
            if ($stmt->execute()) {
                header('Location: Student.php');
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error in preparing statement: " . $conn->error;
        }
    } else {
        echo "Please fill in all required fields.";
    }

    // Close the database connection
    $conn->close();
} else {
    echo "Invalid request.";
}
?>