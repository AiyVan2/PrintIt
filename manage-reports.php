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
    <nav class="bg-purple-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold">Reports Table</span>
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
            <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Reports Table</h2>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-yellow-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inquiry ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Reported</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        
    // Get accepted/completed orders count for current user
    $report_query = "SELECT * FROM report_inquiry";
    $report_result = mysqli_query($conn, $report_query);

    // Check if the query was successful
    if ($report_result) {
        // Fetch data and display each row
        while ($report_data = mysqli_fetch_assoc($report_result)) {
            ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    <?php echo htmlspecialchars($report_data['inquiry_id']); ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?php echo htmlspecialchars($report_data['order_id']); ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?php echo htmlspecialchars($report_data['issue_type']); ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?php echo nl2br(htmlspecialchars($report_data['description'])); ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?php echo htmlspecialchars($report_data['reported_at']); ?>
                </td>
            </tr>
            <?php
        }
    } else {
        echo "<tr><td colspan='5' class='text-center text-gray-500'>No reports found.</td></tr>";
    }
    ?>
    </tbody>
                    </table>
                </div>
            </div>
        </div>
    <footer class="bg-gray-100 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-gray-600 text-sm">© 2024 Printing Services. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>