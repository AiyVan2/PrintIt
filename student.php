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

// Get pending orders count for current user
$pending_query = "SELECT COUNT(*) as count FROM orders WHERE user_id = $user_id AND status = 'pending'";
$pending_result = mysqli_query($conn, $pending_query);
$pending_count = mysqli_fetch_assoc($pending_result)['count'];

// Get accepted/completed orders count for current user
$completed_query = "SELECT COUNT(*) as count FROM orders WHERE user_id = $user_id AND status = 'accepted'";
$completed_result = mysqli_query($conn, $completed_query);
$completed_count = mysqli_fetch_assoc($completed_result)['count'];

// Get total orders count for current user
$total_count = $pending_count + $completed_count;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Printing Services</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Improved Navigation with gradient -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-print text-2xl"></i>
                    <span class="text-xl font-bold">Print IT</span>
                </div>
                <div class="flex items-center space-x-4">
                <span class="text-sm">
                        <i class="fas fa-user-graduate mr-2"></i>Student
                    </span>
                    <a href="logout.php" class="hover:bg-blue-700 px-4 py-2 rounded-md transition duration-300 flex items-center space-x-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-8">
         <!-- Welcome Section with improved styling -->
        <div class="bg-white rounded-xl shadow-sm p-8 mb-8">
            <h1 class="text-3xl font-bold text-gray-800 text-center">Welcome to Your Dashboard</h1>
            <p class="text-gray-600 mt-2 text-lg text-center">Your go-to place for printing</p>
        </div>

       <!-- New Pickup Locations Section with side-by-side layout -->
    <div class="bg-blue-50 rounded-xl shadow-sm p-8 mb-8">
    <div class="flex justify-center items-start mb-6 ">
        <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
        <h2 class="text-xl font-bold text-gray-800 ml-3 ">Pickup Locations</h2>
    </div>
    
    <!-- Grid container for side-by-side cards -->
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Library Location Card -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-blue-100 rounded p-2">
                        <i class="fas fa-print text-blue-600 text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Library</h3>
                    <p class="text-gray-600">Available during school hours</p>
                    <p class="text-blue-600 text-sm mt-1">
                        <i class="far fa-clock mr-2"></i>Mon-Sat: 7:00 AM - 5:00 PM
                    </p>
                </div>
            </div>
        </div>

        <!-- Alternative Location - Gazebo -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-green-100 rounded p-2">
                        <i class="fas fa-umbrella-beach text-green-600 text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Gazebo</h3>
                    <p class="text-gray-600">Alternative pickup location</p>
                    <p class="text-green-600 text-sm mt-1">
                        <i class="fas fa-info-circle mr-2"></i>Available when library is closed
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
        <!-- Stats Overview Grid -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <!-- Pending Orders Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between">
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-yellow-600"><?php echo $pending_count; ?></span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mt-4">Pending Orders</h3>
                <p class="text-gray-500">Awaiting confirmation</p>
            </div>

            <!-- Completed Orders Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between">
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-green-600"><?php echo $completed_count; ?></span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mt-4">Completed Orders</h3>
                <p class="text-gray-500">Successfully printed</p>
            </div>

            <!-- Total Orders Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between">
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-blue-600"><?php echo $total_count; ?></span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mt-4">Total Orders</h3>
                <p class="text-gray-500">All time</p>
            </div>
        </div>

        <!-- Quick Actions Grid -->
        <div class="grid md:grid-cols-2 gap-8 mb-8">
            <!-- Action Cards -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Quick Actions</h2>
                <div class="grid gap-4">
                    <a href="place-order.php" class="group flex items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl hover:from-blue-100 hover:to-blue-200 transition duration-300">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-full p-3 group-hover:from-blue-600 group-hover:to-blue-700 transition duration-300">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-800">Place New Order</h3>
                            <p class="text-sm text-gray-600">Submit a new printing request</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-gray-600"></i>
                    </a>
                    
                    <a href="view-orders.php" class="group flex items-center p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-xl hover:from-green-100 hover:to-green-200 transition duration-300">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-full p-3 group-hover:from-green-600 group-hover:to-green-700 transition duration-300">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-800">View Orders</h3>
                            <p class="text-sm text-gray-600">Check your order history and status</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-gray-600"></i>
                    </a>

                    <a href="report-issue.php" class="group flex items-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl hover:from-purple-100 hover:to-purple-200 transition duration-300">
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-full p-3 group-hover:from-purple-600 group-hover:to-purple-700 transition duration-300">
                            <i class="fas fa-bug text-white"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-800">Submit Report</h3>
                            <p class="text-sm text-gray-600">Submit Inquiries or Report</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-gray-600"></i>
                    </a>
                </div>
            </div>

            <!-- Pricing Cards -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Current Printing Rates</h2>
                <div class="grid gap-6">
                    <!-- Color Printing -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-palette text-blue-600 text-xl mr-3"></i>
                            <h3 class="text-lg font-semibold text-gray-800">Color Printing</h3>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex justify-between items-center">
                                <span class="font-medium">A4</span>
                                <span class="text-blue-600 font-semibold">₱6.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-medium">A3</span>
                                <span class="text-blue-600 font-semibold">₱20.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-medium">Legal</span>
                                <span class="text-blue-600 font-semibold">₱7.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-medium">Letter</span>
                                <span class="text-blue-600 font-semibold">₱5.50</span>
                            </div>
                        </div>
                    </div>

                    <!-- Black & White Printing -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-tint text-gray-600 text-xl mr-3"></i>
                            <h3 class="text-lg font-semibold text-gray-800">Black & White Printing</h3>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex justify-between items-center">
                                <span class="font-medium">A4</span>
                                <span class="text-gray-600 font-semibold">₱3.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-medium">A3</span>
                                <span class="text-gray-600 font-semibold">₱10.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-medium">Legal</span>
                                <span class="text-gray-600 font-semibold">₱3.50</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-medium">Letter</span>
                                <span class="text-gray-600 font-semibold">₱2.75</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gradient-to-r from-gray-100 to-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <p class="text-center text-gray-600">© 2024 Printing Services. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>