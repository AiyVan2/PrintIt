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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders - PrintIT Services</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

<!-- Navigation -->
<nav class="bg-blue-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-2">
                <a href="student.php" class="text-xl font-bold hover:text-gray-200 transition duration-150">
                <i class="fas fa-print mr-2"></i>Print IT
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm">
                    <i class="fas fa-user-graduate mr-2"></i>Student
                </span>
                <a href="logout.php" class="hover:bg-blue-700 px-3 py-2 rounded-md text-sm">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>
    </div>
</nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">My Orders</h1>
            <a href="place-order.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                <i class="fas fa-plus mr-2"></i>New Order
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overall Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accepted By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        // Query the database to get all orders for the current user and join to get the admin's username
                        $query = "SELECT orders.*, users.username AS admin_username 
                                FROM orders 
                                LEFT JOIN users ON orders.admin_id = users.id 
                                WHERE orders.user_id = ? 
                                ORDER BY orders.id DESC";
                        
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, "s", $user_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        while ($order_data = mysqli_fetch_assoc($result)) {
                            $status_class = '';
                            $status_bg = '';
                            
                            switch(strtolower($order_data['status'])) {
                                case 'pending':
                                    $status_class = 'text-yellow-800';
                                    $status_bg = 'bg-yellow-100';
                                    break;
                                case 'accepted':
                                    $status_class = 'text-green-800';
                                    $status_bg = 'bg-green-100';
                                    break;
                                case 'rejected':
                                    $status_class = 'text-red-800';
                                    $status_bg = 'bg-red-100';
                                    break;
                                default:
                                    $status_class = 'text-gray-800';
                                    $status_bg = 'bg-gray-100';
                            }
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #<?php echo $order_data['id']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php 
                                    $file_path = 'Printing Files/' . htmlspecialchars($order_data['file_name']);
                                    ?>
                                    <a href="<?php echo $file_path; ?>" download class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-download mr-2"></i><?php echo htmlspecialchars($order_data['file_name']); ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo nl2br(htmlspecialchars($order_data['specifications'])); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo empty($order_data['requests']) ? 'No request' : htmlspecialchars($order_data['requests']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo empty($order_data['request_price']) ? 'Not yet added' : '₱' . number_format($order_data['request_price'], 2); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    ₱<?php echo number_format((!empty($order_data['request_price']) ? $order_data['request_price'] : 0) + $order_data['order_price'], 2); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class . ' ' . $status_bg; ?>">
                                        <?php echo htmlspecialchars($order_data['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $order_data['admin_username'] ? htmlspecialchars($order_data['admin_username']) : 'N/A'; ?>
                                </td>
                            </tr>
                            <?php if (strtolower($order_data['status']) === 'accepted') { ?>
                                <tr class="bg-blue-50">
                                    <td colspan="8" class="px-6 py-4">
                                        <div class="bg-white p-4 rounded-lg border border-blue-200">
                                            <h3 class="text-lg font-semibold text-blue-800 mb-4">
                                                <i class="fas fa-money-bill-wave mr-2"></i>Payment Information
                                            </h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <p class="text-sm text-gray-600 mb-2">Please send your payment via:</p>
                                                    <div class="space-y-2">
                                                        <p class="text-sm">
                                                            <span class="font-semibold">GCash Number:</span> 09123456789
                                                        </p>
                                                        <p class="text-sm">
                                                            <span class="font-semibold">Account Name:</span> Print IT Services
                                                        </p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-600 mb-2">After payment:</p>
                                                    <div class="space-y-2">
                                                        <p class="text-sm">Please send your proof of payment and order number (#<?php echo $order_data['id']; ?>) to:</p>
                                                        <p class="text-sm font-semibold text-blue-600">
                                                            <i class="fas fa-envelope mr-2"></i>printit2024@gmail.com
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
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