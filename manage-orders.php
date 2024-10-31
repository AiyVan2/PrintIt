<?php
// Include the db.php file to connect to the database
include 'db.php';
session_start();

// Check if the user is logged in and has the admin role
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin' || $_SESSION['role'] == 'superadmin') {
  $user_id = $_SESSION['user_id'];

  // Determine the correct dashboard URL based on role
  $dashboard_url = $_SESSION['role'] == 'superadmin' ? 'superadmin.php' : 'admin.php';
} else {
  header('Location: index.html');
  exit;
}
// Function to extract delivery date from specifications
function extractDeliveryDate($specifications) {
    if (preg_match('/Delivery Date: (\d{4}-\d{2}-\d{2})/', $specifications, $matches)) {
        return $matches[1];
    }
    return null;
}

// Function to get the color class based on days remaining
function getDaysRemainingClass($days) {
    if ($days <= 2) {
        return 'text-red-600 font-bold';
    } elseif ($days <= 5) {
        return 'text-orange-500 font-bold';
    } else {
        return 'text-green-600 font-bold';
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Printing Services</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
<nav class="bg-indigo-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold">Manage Orders</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="<?php echo $dashboard_url; ?>" class="hover:bg-indigo-800 px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                    <a href="logout.php" class="hover:bg-indigo-800 px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if (isset($_GET['msg'])): ?>
            <div class="mb-4 p-4 rounded-md bg-green-50 text-green-800">
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <!-- Pending Orders Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Pending Orders</h2>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-yellow-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specifications</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overall Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Remaining</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            // Query for pending orders
                            $pending_query = "SELECT orders.*, u1.username AS user_username 
                                            FROM orders 
                                            LEFT JOIN users u1 ON orders.user_id = u1.id 
                                            WHERE orders.status = 'pending'";
                            $pending_result = mysqli_query($conn, $pending_query);

                            while ($order_data = mysqli_fetch_assoc($pending_result)) {
                                $file_path = 'Printing Files/' . htmlspecialchars($order_data['file_name']);
                                
                                // Extract delivery date from specifications
                                $delivery_date = extractDeliveryDate($order_data['specifications']);
                                
                                // Calculate days remaining
                                if ($delivery_date) {
                                    $deadline = new DateTime($delivery_date);
                                    $today = new DateTime();
                                    $days_remaining = $today->diff($deadline)->days;
                                    if ($today > $deadline) {
                                        $days_remaining *= -1; // Make negative if past deadline
                                    }
                                    $days_class = getDaysRemainingClass($days_remaining);
                                }
                            ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo $order_data['id']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $order_data['user_username']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="<?php echo $file_path; ?>" download class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-download mr-1"></i>
                                            <?php echo htmlspecialchars($order_data['file_name']); ?>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php echo nl2br(htmlspecialchars($order_data['specifications'])); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php echo empty($order_data['requests']) ? 'No Requests' : htmlspecialchars($order_data['requests']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo empty($order_data['request_price']) ? 'Not Yet Added' : '₱' . number_format($order_data['request_price'], 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ₱<?php echo number_format((!empty($order_data['request_price']) ? $order_data['request_price'] : 0) + $order_data['order_price'], 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $days_class; ?>">
                                        <?php 
                                        if ($delivery_date) {
                                            echo $days_remaining < 0 ? 'Overdue by ' . abs($days_remaining) . ' days' : $days_remaining . ' days';
                                        } else {
                                            echo 'No deadline set';
                                        }
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="accept-order.php?id=<?php echo $order_data['id']; ?>" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-check mr-1"></i>Accept
                                        </a>
                                        <a href="view-order-detail.php?id=<?php echo $order_data['id']; ?>" 
                                            class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Accepted Orders Section -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Accepted Orders</h2>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-green-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specifications</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overall Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accepted By</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            // Query for accepted orders
                            $accepted_query = "SELECT orders.*, u1.username AS user_username, u2.username AS admin_username 
                                             FROM orders 
                                             LEFT JOIN users u1 ON orders.user_id = u1.id 
                                             LEFT JOIN users u2 ON orders.admin_id = u2.id 
                                             WHERE orders.status = 'accepted'";
                            $accepted_result = mysqli_query($conn, $accepted_query);

                            while ($order_data = mysqli_fetch_assoc($accepted_result)) {
                                $file_path = 'Printing Files/' . htmlspecialchars($order_data['file_name']);
                            ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo $order_data['id']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $order_data['user_username']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="<?php echo $file_path; ?>" download class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-download mr-1"></i>
                                            <?php echo htmlspecialchars($order_data['file_name']); ?>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php echo nl2br(htmlspecialchars($order_data['specifications'])); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php echo empty($order_data['requests']) ? 'No Requests' : htmlspecialchars($order_data['requests']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo empty($order_data['request_price']) ? 'Not Yet Added' : '₱' . number_format($order_data['request_price'], 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ₱<?php echo number_format((!empty($order_data['request_price']) ? $order_data['request_price'] : 0) + $order_data['order_price'], 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $order_data['admin_username']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="view-order-detail.php?id=<?php echo $order_data['id']; ?>" 
                                            class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-100 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-gray-600 text-sm">© 2024 Printing Services. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>