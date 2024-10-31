<?php
// Include the db.php file to connect to the database
include 'db.php';
session_start();

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'superadmin') {
    header('Location: index.html');
    exit;
}

// Get the user ID from the URL and validate it
$user_id_to_edit = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($user_id_to_edit <= 0) {
    header('Location: manage-users.php');
    exit;
}

// Prepare and execute the query safely
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id_to_edit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($result);

// If user doesn't exist, redirect back to manage users
if (!$user_data) {
    header('Location: manage-users.php');
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    
    // Validate inputs
    $valid = true;
    $error_message = "";
    
    if (empty($username)) {
        $valid = false;
        $error_message = "Username cannot be empty";
    }
    
    if (!in_array($role, ['student', 'admin', 'superadmin', 'suspended'])) {
        $valid = false;
        $error_message = "Invalid role selected";
    }
    
    if ($valid) {
        // Update the user data in the database
        $update_query = "UPDATE users SET username = ?, role = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "ssi", $username, $role, $user_id_to_edit);
        
        // If password is provided, update it
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "sssi", $username, $password, $role, $user_id_to_edit);
        }
        
        if (mysqli_stmt_execute($update_stmt)) {
            header('Location: manage-users.php?success=1');
            exit;
        } else {
            $error_message = "Error updating user";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - PrintIT Services</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-purple-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="superadmin.php" class="text-xl font-bold hover:text-indigo-200 transition duration-150">
                        Super Admin User Setting
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="manage-users.php" class="hover:bg-indigo-800 px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Users
                    </a>
                    <a href="logout.php" class="hover:bg-indigo-800 px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit User</h1>

                <?php if (isset($error_message) && !empty($error_message)): ?>
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700"><?php echo htmlspecialchars($error_message); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <form action="edit-user.php?id=<?php echo $user_id_to_edit; ?>" method="post" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo htmlspecialchars($user_data['username']); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               required>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                            <span class="text-gray-500 text-xs ml-1">(Leave blank to keep current password)</span>
                        </label>
                        <input type="password" id="password" name="password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select id="role" name="role"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="student" <?php echo ($user_data['role'] == 'student') ? 'selected' : ''; ?>>
                                Student
                            </option>
                            <option value="admin" <?php echo ($user_data['role'] == 'admin') ? 'selected' : ''; ?>>
                                Admin
                            </option>
                            <option value="superadmin" <?php echo ($user_data['role'] == 'superadmin') ? 'selected' : ''; ?>>
                                Super Admin
                            </option>
                            <option value="suspended" <?php echo ($user_data['role'] == 'suspended') ? 'selected' : ''; ?>>
                                Suspended
                            </option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="manage-users.php" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-save mr-2"></i>
                            Update User
                        </button>
                    </div>
                </form>
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