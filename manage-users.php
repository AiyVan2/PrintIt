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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - PrintIT Services</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-purple-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="superadmin.php" class="text-xl font-bold hover:text-indigo-200 transition duration-150">Super Admin User Setting</a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">
                        <i class="fas fa-user-shield mr-2"></i>Super Admin Account
                    </span>
                    <a href="logout.php" class="hover:bg-indigo-800 px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Manage Users</h1>
            <a href="add-user.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm">
                <i class="fas fa-plus mr-2"></i>Add New User
            </a>
        </div>

        
        <?php
function renderUserTable($role, $bgColor, $textColor) {
    global $conn;
    ?>
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-4"><?php echo ucfirst($role).'s'; ?></h2>
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full table-fixed divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-2/5 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="w-2/5 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="w-1/5 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $query = "SELECT * FROM users WHERE role = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "s", $role);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) == 0) {
                        if ($role === 'suspended') {
                            ?>
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                                        <p>No suspended accounts at this time.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        } else {
                            ?>
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No <?php echo strtolower($role); ?> users found.
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        while ($user_data = mysqli_fetch_assoc($result)) {
                            $isSuspended = $role === 'suspended';
                            $rowClass = $isSuspended ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50';
                            $usernameClass = $isSuspended ? 'text-red-800' : 'text-gray-900';
                            ?>
                            <tr class="<?php echo $rowClass; ?> transition-colors duration-150">
                                <td class="px-6 py-4 text-sm font-medium truncate">
                                    <div class="flex items-center">
                                        <?php if ($isSuspended): ?>
                                            <i class="fas fa-ban text-red-500 mr-2"></i>
                                        <?php endif; ?>
                                        <span class="<?php echo $usernameClass; ?>">
                                            <?php echo htmlspecialchars($user_data['username']); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($isSuspended): ?>
                                        <span class="px-2 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            <?php echo htmlspecialchars($user_data['role']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $bgColor.' '.$textColor; ?>">
                                            <?php echo htmlspecialchars($user_data['role']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex items-center space-x-4">
                                        <a href="edit-user.php?id=<?php echo $user_data['id']; ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 transition-colors duration-150">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($isSuspended): ?>
                                            <a href="reactivate-user.php?id=<?php echo $user_data['id']; ?>" 
                                               class="text-green-600 hover:text-green-900 transition-colors duration-150"
                                               onclick="return confirm('Are you sure you want to reactivate this user?');">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="delete-user.php?id=<?php echo $user_data['id']; ?>" 
                                           class="text-red-600 hover:text-red-900 transition-colors duration-150"
                                           onclick="return confirm('Are you sure you want to delete this user?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php }
                    }
                    mysqli_stmt_close($stmt);
                    ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
}
?>

        <!-- Render tables for each role -->
        <?php
        renderUserTable('superadmin', 'bg-purple-100', 'text-purple-800');
        renderUserTable('suspended', 'bg-purple-100', 'text-purple-800');
        renderUserTable('admin', 'bg-blue-100', 'text-blue-800');
        renderUserTable('student', 'bg-green-100', 'text-green-800');
        ?>
    </main>

    <footer class="bg-gray-100 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-gray-600 text-sm">© 2024 PrintIT Services. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>