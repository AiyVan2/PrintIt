<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Issue - PrintIT Services</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Report an Issue</h1>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden max-w-2xl">
            <form action="submit-report.php" method="POST" class="p-6 space-y-6">
                <!-- Order ID Field -->
                <div>
                    <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Order ID
                    </label>
                    <input 
                        type="number" 
                        id="order_id" 
                        name="order_id" 
                        required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        placeholder="Enter your order ID"
                    >
                </div>

                <!-- Issue Type Field -->
                <div>
                    <label for="issue_type" class="block text-sm font-medium text-gray-700 mb-1">
                        Issue Type
                    </label>
                    <select 
                        id="issue_type" 
                        name="issue_type" 
                        required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                        <option value="">Select an issue type</option>
                        <option value="Order Not Accepted">Order Not Accepted</option>
                        <option value="Delivery Delay">Delayed Delivery</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Description Field -->
                <div id="description_container" class="hidden">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="4"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        placeholder="Please provide details about your issue"
                    ></textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button 
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <i class="fas fa-paper-plane mr-2"></i>Submit Report
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-gray-100 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-gray-600 text-sm">© 2024 PrintIT Services. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Show/hide description field based on issue type selection
        document.getElementById('issue_type').addEventListener('change', function() {
            const descriptionContainer = document.getElementById('description_container');
            const description = document.getElementById('description');
            
            if (this.value === 'Other') {
                descriptionContainer.classList.remove('hidden');
                description.required = true;
            } else {
                descriptionContainer.classList.add('hidden');
                description.required = false;
            }
        });
    </script>
</body>
</html>