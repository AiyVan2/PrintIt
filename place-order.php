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
    <title>Place Order - Student Printing Services</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
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
                    <a href="logout.php" class="hover:bg-indigo-800 px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Place Your Print Order</h1>
            
            <form action="printing-orders.php" method="post" enctype="multipart/form-data" class="space-y-6">
                <!-- File Upload Section -->
                <div class="space-y-2">
    <label class="block text-sm font-medium text-gray-700">Upload File</label>
    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 transition-colors">
        <div class="space-y-1 text-center">
            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
            <div class="flex text-sm text-gray-600">
                <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                    <span>Upload a file</span>
                    <input id="file" name="file" type="file" class="sr-only" required onchange="updateFileName(this)">
                </label>
                <p class="pl-1">or drag and drop</p>
            </div>
            <p class="text-xs text-gray-500">PDF, DOC, DOCX up to 10MB</p>
            <!-- Add this div for showing selected filename -->
            <div id="fileInfo" class="mt-3 hidden">
                <p class="text-sm text-gray-700">Selected file: <span id="fileName" class="font-medium"></span></p>
                <button type="button" onclick="clearFile()" class="mt-1 text-sm text-red-600 hover:text-red-700">
                    <i class="fas fa-times mr-1"></i>Remove file
                </button>
            </div>
        </div>
    </div>
</div>

                <!-- Print Options Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Size Selection -->
                    <div>
                        <label for="size" class="block text-sm font-medium text-gray-700">Paper Size</label>
                        <select id="size" name="size" onchange="calculatePrice()" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                            <option value="A4">A4</option>
                            <option value="A3">A3</option>
                            <option value="Legal">Legal</option>
                            <option value="Letter">Letter</option>
                        </select>
                    </div>

                    <!-- Color Option -->
                    <div>
                        <label for="color_option" class="block text-sm font-medium text-gray-700">Color Option</label>
                        <select id="color_option" name="color_option" onchange="calculatePrice()" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                            <option value="Color">Color</option>
                            <option value="Black and White">Black and White</option>
                        </select>
                    </div>

                    <!-- Pages Input -->
                    <div>
                        <label for="pages" class="block text-sm font-medium text-gray-700">Pages to Print</label>
                        <input type="text" id="pages" name="pages" pattern="^(\d+(-\d+)?)(,\s*\d+(-\d+)?)*$" placeholder="e.g., 1-5, 7, 10" oninput="calculatePrice()" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Enter page numbers and/or ranges separated by commas</p>
                    </div>

                    <!-- Delivery Date -->
                    <div>
                        <label for="delivery_date" class="block text-sm font-medium text-gray-700">Delivery Date</label>
                        <input type="date" id="delivery_date" name="delivery_date" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Delivery Time -->
                    <div>
                        <label for="delivery_time" class="block text-sm font-medium text-gray-700">Delivery Time</label>
                        <input type="time" id="delivery_time" name="delivery_time" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" min="07:00" max="17:00" oninput="validateTime()">
                        <p id="time-error" class="text-red-500 text-sm mt-1 hidden">Please select a time between 7:00 AM and 5:00 PM.</p>
                    </div>
                </div>

                <!-- Special Request -->
                <div>
                    <label for="special_request" class="block text-sm font-medium text-gray-700">Special Request</label>
                    <textarea id="special_request" name="special_request" rows="3" placeholder="Any special instructions or requests" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <!-- Price Display -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-medium text-gray-700">Estimated Price:</span>
                        <span id="price" class="text-2xl font-bold text-indigo-600">₱0.00</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Place Order
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-gray-100 mt-8">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <p class="text-center text-gray-600 text-sm">© 2024 Printing Services. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Function to calculate price based on input
        function calculatePrice() {
            const size = document.getElementById('size').value;
            const colorOption = document.getElementById('color_option').value;
            const pagesInput = document.getElementById('pages').value;
            let pricePerPage = 0;

            // Determine price per page based on size and color option
            if (colorOption === "Color") {
                switch (size) {
                    case "A4": pricePerPage = 6.00; break;
                    case "A3": pricePerPage = 20.00; break;
                    case "Legal": pricePerPage = 7.00; break;
                    case "Letter": pricePerPage = 5.50; break;
                }
            } else { // Black and White
                switch (size) {
                    case "A4": pricePerPage = 3.00; break;
                    case "A3": pricePerPage = 10.00; break;
                    case "Legal": pricePerPage = 3.50; break;
                    case "Letter": pricePerPage = 2.75; break;
                }
            }

            // Parse pages input
            const pages = parsePages(pagesInput);
            const numPages = pages.length; // Count the number of unique pages
            const totalPrice = numPages * pricePerPage;

            // Update price display
            const priceDisplay = document.getElementById('price');
            priceDisplay.textContent = `₱${totalPrice.toFixed(2)}`;
        }

        // Function to parse page input
        function parsePages(input) {
            if (!input.trim()) return [1]; // Default to 1 page if empty
            
            const pages = [];
            const inputs = input.split(',');

            inputs.forEach(part => {
                part = part.trim();
                if (part.includes('-')) {
                    const [start, end] = part.split('-').map(p => parseInt(p.trim()));
                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                } else if (!isNaN(part)) {
                    pages.push(parseInt(part));
                }
            });

            return [...new Set(pages)]; // Remove duplicates
        }

        // Function to set the minimum delivery date to tomorrow
        function setMinDeliveryDate() {
            const today = new Date();
            today.setDate(today.getDate() + 1); // Set to tomorrow
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const yyyy = today.getFullYear();
            const minDate = `${yyyy}-${mm}-${dd}`;
            document.getElementById('delivery_date').setAttribute('min', minDate);
        }

        // Initialize the page
        window.onload = function() {
            setMinDeliveryDate();
            calculatePrice(); // Initialize price display
        };


        function validateTime() {
        const input = document.getElementById("delivery_time");
        const error = document.getElementById("time-error");
        const time = input.value;
        
        // Check if time is within the allowed range
        if (time < "07:00" || time > "17:00") {
            error.classList.remove("hidden");
            input.setCustomValidity("Please select a time between 7:00 AM and 5:00 PM.");
        } else {
            error.classList.add("hidden");
            input.setCustomValidity("");
        }
    }



    // Add these new functions to your existing script section
function updateFileName(input) {
    const fileInfo = document.getElementById('fileInfo');
    const fileNameSpan = document.getElementById('fileName');
    
    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        fileNameSpan.textContent = fileName;
        fileInfo.classList.remove('hidden');
    } else {
        fileInfo.classList.add('hidden');
    }
}

function clearFile() {
    const fileInput = document.getElementById('file');
    const fileInfo = document.getElementById('fileInfo');
    
    fileInput.value = ''; // Clear the file input
    fileInfo.classList.add('hidden');
}
    </script>
</body>
</html>
