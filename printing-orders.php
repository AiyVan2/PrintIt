<?php
// Include the db.php file to connect to the database
include 'db.php';
session_start();

// Check if the user is logged in and has the student role
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'student') {
    $user_id = $_SESSION['user_id'];
} else {
    header('Location: index.html');
    exit;
}

// Function to calculate price
function calculatePrice($size, $color_option, $num_pages) {
    $price_per_page = 0;

    // Set the price per page based on size and color option
    if ($color_option == "Color") {
        switch ($size) {
            case "A4":
                $price_per_page = 6.00; // Store as cents: ₱0.15
                break;
            case "A3":
                $price_per_page = 20.00; // Store as cents: ₱0.25
                break;
            case "Legal":
                $price_per_page = 7.00; // Store as cents: ₱0.25
                break;    
            case "Letter":
                $price_per_page = 5.50; // Store as cents: ₱0.20
                break;
        }
    } else { // Black and White
        switch ($size) {
            case "A4":
                $price_per_page = 3.00; // Store as cents: ₱0.05
                break;
            case "A3":
                $price_per_page = 10.00; // Store as cents: ₱0.15
                break;
            case "Legal":
                $price_per_page = 3.50; // Store as cents: ₱0.25
                break;    
            case "Letter":
                $price_per_page = 2.75; // Store as cents: ₱0.10
                break;
        }
    }

    // Calculate total price based on number of pages (in cents)
    return $price_per_page * $num_pages;
}

// Function to parse page input
function parsePages($input) {
    $pages = [];
    
    // Split the input by commas
    $inputs = explode(',', $input);

    foreach ($inputs as $part) {
        // Trim whitespace
        $part = trim($part);

        // Check for ranges (e.g., 1-6)
        if (strpos($part, '-') !== false) {
            list($start, $end) = explode('-', $part);
            $start = (int)trim($start);
            $end = (int)trim($end);
            // Generate the range and merge with pages array
            $pages = array_merge($pages, range($start, $end));
        } else {
            // Add individual page number
            $pages[] = (int)$part;
        }
    }

    // Remove duplicates and sort the pages
    return array_unique(array_filter($pages));
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the file and other details from the form
    $file = $_FILES['file'];
    $size = $_POST['size'];
    $color_option = $_POST['color_option'];
    $pages_input = $_POST['pages']; // Changed variable name for clarity
    $delivery_date = $_POST['delivery_date'];
    $delivery_time = $_POST['delivery_time'];
    $special_request = $_POST['special_request'];

    // Parse the page input
    $pages_array = parsePages($pages_input);
    $num_pages = count($pages_array); // Count the number of unique pages

    // Calculate the estimated price in cents
    $price = calculatePrice($size, $color_option, $num_pages);

    // Debug: Output the captured form data
    echo "<pre>";
    echo "Size: $size\n";
    echo "Color Option: $color_option\n";
    echo "Pages: " . implode(', ', $pages_array) . "\n"; // Display selected pages
    echo "Number of Pages: $num_pages\n"; // Display total number of pages
    echo "Price: ₱" . number_format($price, 2) . "\n"; // Display price as PHP
    echo "Delivery Date: $delivery_date\n";
    echo "Delivery Time: $delivery_time\n";
    echo "Special Request: $special_request\n";
    echo "</pre>";

    // Check if the file is uploaded successfully
    if ($file['error'] == 0) {
        // Get the file name and type
        $file_name = $file['name'];
        $file_type = $file['type'];

        // Check if the file type is allowed
        if ($file_type == 'application/pdf' ||  $file_type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $file_type == 'image/jpeg' || $file_type == 'image/png') {
            // Upload the file to the server
            $upload_dir = 'Printing Files/';
            $file_path = $upload_dir . $file_name;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            move_uploaded_file($file['tmp_name'], $file_path);

            // Insert the order into the database
            $specifications = "Size: $size \n".
                "Color Option: $color_option \n".
                "Pages: " . implode(', ', $pages_array) . "\n". // Include the selected pages
                "Price: ₱" . number_format($price, 2) . "\n". // Display price as PHP
                "Delivery Date: $delivery_date \n" .
                "Delivery Time: $delivery_time \n";

            $query = "INSERT INTO orders (user_id, file_name, specifications, requests, order_price, status) 
                      VALUES ('$user_id', '$file_name', '$specifications', '$special_request', '$price', 'pending')";

            // Debug: Output the SQL query
            echo "<pre>SQL Query: $query</pre>";

            // Execute the query and check for errors
            if (mysqli_query($conn, $query)) {
                // Redirect to the view orders page
                header('Location: view-orders.php');
                exit;
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Only PDF, JPEG, and PNG files are allowed.";
        }
    } else {
        echo "Error uploading file.";
    }
}
?>
