<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$auth = new Auth();

if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Database connection
$db = new Database();
$conn = $db->connect();

// Get stats for dashboard
$users_count = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$transactions_count = $conn->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$total_revenue = $conn->query("SELECT SUM(amount) FROM transactions")->fetchColumn();
$today_revenue = $conn->query("SELECT SUM(amount) FROM transactions WHERE DATE(transaction_date) = CURDATE()")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-blue-800 text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out">
            <div class="flex items-center space-x-2 px-4">
                <i class="fas fa-gas-pump text-2xl"></i>
                <span class="text-xl font-bold"><?php echo APP_NAME; ?></span>
            </div>
            <nav>
                <a href="dashboard.php" class="block py-2.5 px-4 rounded transition duration-200 bg-blue-700 text-white">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="users.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
                    <i class="fas fa-users mr-2"></i>Users
                </a>
                <a href="transactions.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
                    <i class="fas fa-exchange-alt mr-2"></i>Transactions
                </a>
                <a href="reports.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
                    <i class="fas fa-chart-bar mr-2"></i>Reports
                </a>
                <a href="../logout.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar -->
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center py-4 px-6">
                    <div class="flex items-center">
                        <button class="md:hidden mr-4 text-gray-500 focus:outline-none">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">Dashboard</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button class="text-gray-500 focus:outline-none">
                                <i class="fas fa-bell"></i>
                            </button>
                            <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
                        </div>
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white">
                                <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                            </div>
                            <span class="ml-2 text-sm font-medium"><?php echo $_SESSION['full_name']; ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Stats Cards -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Total Users</p>
                                <h3 class="text-2xl font-bold"><?php echo $users_count; ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                <i class="fas fa-exchange-alt text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Total Transactions</p>
                                <h3 class="text-2xl font-bold"><?php echo $transactions_count; ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                                <i class="fas fa-rupee-sign text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Total Revenue</p>
                                <h3 class="text-2xl font-bold">₹<?php echo number_format((float)$total_revenue, 2); ?></h3>

                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                                <i class="fas fa-calendar-day text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Today's Revenue</p>
                                <h3 class="text-2xl font-bold">₹<?php echo number_format((float)$today_revenue, 2); ?></h3>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Recent Transactions</h2>
                        <a href="transactions.php" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $stmt = $conn->query("
                                    SELECT t.*, u.username 
                                    FROM transactions t
                                    JOIN users u ON t.user_id = u.id
                                    ORDER BY t.transaction_date DESC
                                    LIMIT 5
                                ");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>
                                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>#{$row['id']}</td>
                                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$row['username']}</td>
                                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$row['transaction_type']}</td>
                                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>₹{$row['amount']}</td>
                                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$row['transaction_date']}</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Recent Users</h2>
                        <a href="users.php" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $stmt = $conn->query("
                                    SELECT id, username, full_name, email, role 
                                    FROM users 
                                    ORDER BY created_at DESC
                                    LIMIT 5
                                ");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>
                                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$row['id']}</td>
                                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$row['username']}</td>
                                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$row['full_name']}</td>
                                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$row['email']}</td>
                                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$row['role']}</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        document.querySelector('.md\\:hidden').addEventListener('click', function() {
            document.querySelector('.transform').classList.toggle('-translate-x-full');
        });
    </script>
</body>
</html>