<?php
// Include the db.php file to connect to the database
include 'db.php';
session_start();

// Check if the user is logged in and has the appropriate role (admin or superadmin)
if (isset($_SESSION['user_id']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'superadmin')) {
  $user_id = $_SESSION['user_id'];
  $order_id = $_GET['id']; // Get the order ID from the URL

  // If the form is submitted (POST request)
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_price = $_POST['request_price'];

    // Update the order with the status, admin_id, accepted_at, and request_price
    $query = "UPDATE orders 
              SET status = 'accepted', admin_id = '$user_id', accepted_at = NOW(), request_price = '$request_price'
              WHERE id = '$order_id'";
    
    if (mysqli_query($conn, $query)) {
      // Redirect back to manage orders with a success message
      header("Location: manage-orders.php");
      exit;
    } else {
      echo "Error: " . mysqli_error($conn);
    }
  }
} else {
  // Redirect to the login page if not authorized
  header('Location: index.html');
  exit;
}

// Get order details
$order_query = "SELECT * FROM orders WHERE id = '$order_id'";
$order_result = mysqli_query($conn, $order_query);
$order_data = mysqli_fetch_assoc($order_result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Order - PrintIT Services</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="manage-orders.php" class="text-xl font-bold hover:text-gray-200 transition duration-150">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">
                        <i class="fas fa-user-shield mr-2"></i>Admin Panel
                    </span>
                    <a href="logout.php" class="hover:bg-blue-700 px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Accept Order #<?php echo htmlspecialchars($order_data['id']); ?></h1>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <!-- Order Details Section -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-800 mb-4">Order Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <!-- Order ID -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Order ID</h3>
                            <p class="mt-1 text-sm text-gray-900">#<?php echo htmlspecialchars($order_data['id']); ?></p>
                        </div>

                        <!-- User ID -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">User ID</h3>
                            <p class="mt-1 text-sm text-gray-900">#<?php echo htmlspecialchars($order_data['user_id']); ?></p>
                        </div>

                        <!-- File Name -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">File Name</h3>
                            <a href="Printing Files/<?php echo htmlspecialchars($order_data['file_name']); ?>" 
                               download 
                               class="mt-1 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-file-download mr-2"></i>
                                <?php echo htmlspecialchars($order_data['file_name']); ?>
                            </a>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <!-- Specifications -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Specifications</h3>
                            <div class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md p-3">
                                <?php echo nl2br(htmlspecialchars($order_data['specifications'])); ?>
                            </div>
                        </div>

                        <!-- Special Request -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Special Request</h3>
                            <div class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md p-3">
                                <?php echo !empty($order_data['requests']) ? nl2br(htmlspecialchars($order_data['requests'])) : 'No special requests'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accept Order Form Section -->
            <form method="post" class="p-6 bg-gray-50">
                <h2 class="text-lg font-medium text-gray-800 mb-4">Accept Order</h2>
                <div class="max-w-md">
                    <div class="mb-4">
                        <label for="request_price" class="block text-sm font-medium text-gray-700">
                            Request Price (₱)
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₱</span>
                            </div>
                            <input 
                                type="number" 
                                id="request_price" 
                                name="request_price" 
                                step="0.01" 
                                min="0" 
                                required
                                class="pl-7 block w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                placeholder="0.00"
                            >
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <button 
                            type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <i class="fas fa-check mr-2"></i>Accept Order
                        </button>
                        <a 
                            href="manage-orders.php"
                            class="flex-1 bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2 rounded-md text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 text-center"
                        >
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-gray-100 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-gray-600 text-sm">© 2024 PrintIT Services. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>