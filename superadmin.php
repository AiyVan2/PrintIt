<?php
// Include the db.php file to connect to the database
include 'db.php';
session_start();

// Check if the user is logged in and has the admin role
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'superadmin') {
    $user_id = $_SESSION['user_id'];
} else {
    header('Location: index.html');
    exit;
}

// Initialize variables
$totalSuperAdmins = 0;
$totalAdmins = 0;
$totalStudents = 0;
$totalAcceptedOrders = 0;
$totalPendingOrders = 0;

// Query to count total users by role
$userQuery = "SELECT role, COUNT(*) as user_count FROM users GROUP BY role";
$userResult = mysqli_query($conn, $userQuery);
if ($userResult) {
    while ($row = mysqli_fetch_assoc($userResult)) {
        if ($row['role'] == 'superadmin') {
            $totalSuperAdmins = $row['user_count'];
        } elseif ($row['role'] == 'admin') {
            $totalAdmins = $row['user_count'];
        } elseif ($row['role'] == 'student') {
            $totalStudents = $row['user_count'];
        }
    }
}

// Query to count total accepted and pending orders
$orderQuery = "SELECT status, COUNT(*) as order_count FROM orders GROUP BY status";
$orderResult = mysqli_query($conn, $orderQuery);
if ($orderResult) {
    while ($row = mysqli_fetch_assoc($orderResult)) {
        if ($row['status'] == 'accepted') {
            $totalAcceptedOrders = $row['order_count'];
        } elseif ($row['status'] == 'pending') {
            $totalPendingOrders = $row['order_count'];
        }
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin Dashboard - Student Partners | Printing Services</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-purple-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold">SuperAdmin Dashboard</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">
                        <i class="fas fa-user-shield mr-2"></i>SuperAdmin
                    </span>
                    <a href="logout.php" class="hover:bg-purple-800 px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- User Statistics Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">User Management</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- SuperAdmin Card -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-purple-100 rounded-full p-3">
                            <i class="fas fa-user-shield text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">Super Admins</h3>
                            <p class="text-2xl font-bold text-purple-600"><?php echo $totalSuperAdmins; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Admin Card -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-user-tie text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">Admins</h3>
                            <p class="text-2xl font-bold text-blue-600"><?php echo $totalAdmins; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Students Card -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-user-graduate text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">Students</h3>
                            <p class="text-2xl font-bold text-green-600"><?php echo $totalStudents; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Statistics Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Order Statistics</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Accepted Orders -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">Accepted Orders</h3>
                            <p class="text-2xl font-bold text-green-600"><?php echo $totalAcceptedOrders; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Pending Orders -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 rounded-full p-3">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">Pending Orders</h3>
                            <p class="text-2xl font-bold text-yellow-600"><?php echo $totalPendingOrders; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="manage-users.php" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-users text-purple-600 text-xl mr-3"></i>
                    <span class="text-gray-700 font-medium">Manage Users</span>
                </a>
                <a href="manage-orders.php" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-clipboard-list text-purple-600 text-xl mr-3"></i>
                    <span class="text-gray-700 font-medium">Manage Orders</span>
                </a>
                <a href="manage-reports.php" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-clipboard-list text-purple-600 text-xl mr-3"></i>
                    <span class="text-gray-700 font-medium">Report Table</span>
                </a>
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