<?php
// Include the database connection file
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: index.html');
  exit;
}

// Check if an order ID is provided in the URL
if (isset($_GET['id'])) {
  $order_id = $_GET['id'];

  // Query the database to get the order details and admin name
  $query = "
    SELECT orders.*, users.username AS admin_name
    FROM orders
    LEFT JOIN users ON orders.admin_id = users.id
    WHERE orders.id = '$order_id'";
  $result = mysqli_query($conn, $query);

  // Check if the order exists
  if (mysqli_num_rows($result) > 0) {
    // Fetch the order details
    $order_data = mysqli_fetch_assoc($result);
  } else {
    // If the order does not exist, display an error message
    echo "Order not found.";
    exit;
  }
} else {
  // If no order ID is provided, redirect to the main orders page
  header('Location: orders.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - PrintIT Services</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-indigo-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="manage-orders.php" class="text-xl font-bold hover:text-gray-200 transition duration-150">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="logout.php" class="hover:bg-blue-700 px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Order Details #<?php echo htmlspecialchars($order_id); ?></h1>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 space-y-6">
                <!-- File Name -->
                <div class="border-b border-gray-200 pb-4">
                    <h2 class="text-sm font-medium text-gray-500 mb-2">File Name</h2>
                    <div class="flex items-center">
                        <i class="fas fa-file-alt text-gray-400 mr-3"></i>
                        <?php
                        $file_path = 'Printing Files/' . htmlspecialchars($order_data['file_name']);
                        ?>
                        <a href="<?php echo $file_path; ?>" download class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-download mr-2"></i><?php echo htmlspecialchars($order_data['file_name']); ?>
                        </a>
                    </div>
                </div>

                <!-- Specifications -->
                <div class="border-b border-gray-200 pb-4">
                    <h2 class="text-sm font-medium text-gray-500 mb-2">Specifications</h2>
                    <div class="bg-gray-50 rounded-md p-4">
                        <?php echo nl2br(htmlspecialchars($order_data['specifications'])); ?>
                    </div>
                </div>

                <!-- Status -->
                <div class="border-b border-gray-200 pb-4">
                    <h2 class="text-sm font-medium text-gray-500 mb-2">Status</h2>
                    <?php
                    $status_class = '';
                    $status_bg = '';
                    switch(strtolower($order_data['status'])) {
                        case 'pending':
                            $status_class = 'text-yellow-800 bg-yellow-100';
                            break;
                        case 'accepted':
                            $status_class = 'text-green-800 bg-green-100';
                            break;
                        case 'rejected':
                            $status_class = 'text-red-800 bg-red-100';
                            break;
                        default:
                            $status_class = 'text-gray-800 bg-gray-100';
                    }
                    ?>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                        <?php echo htmlspecialchars($order_data['status']); ?>
                    </span>
                </div>

                <!-- Request -->
                <div class="border-b border-gray-200 pb-4">
                    <h2 class="text-sm font-medium text-gray-500 mb-2">Request</h2>
                    <div class="text-gray-700">
                        <?php echo !empty($order_data['requests']) ? htmlspecialchars($order_data['requests']) : 'No special requests'; ?>
                    </div>
                </div>

                <!-- Order Processing Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Accepted By -->
                    <div>
                        <h2 class="text-sm font-medium text-gray-500 mb-2">Accepted By</h2>
                        <div class="flex items-center">
                            <i class="fas fa-user text-gray-400 mr-3"></i>
                            <span class="text-gray-700">
                                <?php echo !empty($order_data['admin_name']) ? htmlspecialchars($order_data['admin_name']) : 'Not Accepted'; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Accepted At -->
                    <div>
                        <h2 class="text-sm font-medium text-gray-500 mb-2">Accepted At</h2>
                        <div class="flex items-center">
                            <i class="fas fa-clock text-gray-400 mr-3"></i>
                            <span class="text-gray-700">
                                <?php echo !empty($order_data['accepted_at']) ? htmlspecialchars($order_data['accepted_at']) : 'Not yet accepted'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-100 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-gray-600 text-sm">© 2024 PrintIT Services. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>