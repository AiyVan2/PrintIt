
<?php
// Include the db.php file to connect to the database
include 'db.php';
session_start();
// Check if the user is logged in and has the admin role
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin') {
  $user_id = $_SESSION['user_id'];
} else {
  header('Location: index.html');
  exit;
}
// Get pending orders count
$pending_query = "SELECT COUNT(*) as status FROM orders WHERE status = 'pending'";
$pending_result = mysqli_query($conn, $pending_query);
$pending_count = mysqli_fetch_assoc($pending_result)['status'];

// Get completed orders count
$completed_query = "SELECT COUNT(*) as status FROM orders WHERE status = 'accepted'";
$completed_result = mysqli_query($conn, $completed_query);
$completed_count = mysqli_fetch_assoc($completed_result)['status'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student Partners | Printing Services</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <nav class="bg-indigo-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <i class="fas fa-print mr-2"></i>
                    <span class="text-xl font-bold">Student Partner</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">
                        <i class="fas fa-user-circle mr-2"></i>Student Partner
                    </span>
                    <a href="logout.php" class="hover:bg-indigo-800 px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Pending Orders</h3>
                        <p class="text-2xl font-bold text-yellow-600"><?php echo $pending_count; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Complete</h3>
                        <p class="text-2xl font-bold text-green-600"><?php echo $completed_count; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Manage Orders</h2>
                <a href="manage-orders.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm">
                    View All Orders
                </a>
            </div>
        </div>
    </main>

    <footer class="bg-gray-100 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-gray-600 text-sm">© 2024 Printing Services. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>