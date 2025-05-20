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

// Get report data
$daily_revenue = $conn->query("
    SELECT DATE(transaction_date) as date, SUM(amount) as total 
    FROM transactions 
    GROUP BY DATE(transaction_date) 
    ORDER BY date DESC 
    LIMIT 7
")->fetchAll(PDO::FETCH_ASSOC);

$fuel_stats = $conn->query("
    SELECT f.name, COUNT(t.id) as transactions, SUM(t.amount) as revenue, SUM(t.liters) as liters
    FROM fuel_types f
    LEFT JOIN transactions t ON f.id = t.fuel_type_id
    GROUP BY f.id
")->fetchAll(PDO::FETCH_ASSOC);

$monthly_revenue = $conn->query("
    SELECT DATE_FORMAT(transaction_date, '%Y-%m') as month, SUM(amount) as total 
    FROM transactions 
    GROUP BY DATE_FORMAT(transaction_date, '%Y-%m') 
    ORDER BY month DESC 
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="dashboard.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="users.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
                    <i class="fas fa-users mr-2"></i>Users
                </a>
                <a href="transactions.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
                    <i class="fas fa-exchange-alt mr-2"></i>Transactions
                </a>
                <a href="reports.php" class="block py-2.5 px-4 rounded transition duration-200 bg-blue-700 text-white">
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
                        <h1 class="text-xl font-semibold text-gray-800">Reports</h1>
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
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Daily Revenue Chart -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold mb-4">Daily Revenue (Last 7 Days)</h2>
                        <canvas id="dailyRevenueChart" height="300"></canvas>
                    </div>
                    
                    <!-- Monthly Revenue Chart -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold mb-4">Monthly Revenue (Last 6 Months)</h2>
                        <canvas id="monthlyRevenueChart" height="300"></canvas>
                    </div>
                </div>
                
                <!-- Fuel Statistics -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4">Fuel Statistics</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fuel Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transactions</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Liters</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($fuel_stats as $fuel): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $fuel['name']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $fuel['transactions']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $fuel['liters'] ? number_format($fuel['liters'], 2) . ' L' : 'N/A'; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹<?php echo number_format($fuel['revenue'] ?? 0, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Report Generator -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Generate Report</h3>
                    <form method="GET" action="download-report.php" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="report_username">
                                Username
                            </label>
                            <select name="username" id="report_username" class="w-full px-3 py-2 border rounded">
                                <option value="">All Users</option>
                                <?php
                                $users = $conn->query("SELECT DISTINCT username FROM users ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($users as $user) {
                                    echo "<option value='{$user['username']}'>{$user['username']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="report_from_date">
                                From Date
                            </label>
                            <input type="date" name="from_date" id="report_from_date"
                                   class="w-full px-3 py-2 border rounded">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="report_to_date">
                                To Date
                            </label>
                            <input type="date" name="to_date" id="report_to_date"
                                   class="w-full px-3 py-2 border rounded">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                                Download Report
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        document.querySelector('.md\\:hidden').addEventListener('click', function() {
            document.querySelector('.transform').classList.toggle('-translate-x-full');
        });

        // Daily Revenue Chart
        const dailyCtx = document.getElementById('dailyRevenueChart').getContext('2d');
        const dailyChart = new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: [<?php echo implode(',', array_map(function($item) { return "'" . date('M d', strtotime($item['date'])) . "'"; }, $daily_revenue)); ?>],
                datasets: [{
                    label: 'Revenue (₹)',
                    data: [<?php echo implode(',', array_column($daily_revenue, 'total')); ?>],
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Monthly Revenue Chart
        const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: [<?php echo implode(',', array_map(function($item) { return "'" . date('M Y', strtotime($item['month'] . '-01')) . "'"; }, $monthly_revenue)); ?>],
                datasets: [{
                    label: 'Revenue (₹)',
                    data: [<?php echo implode(',', array_column($monthly_revenue, 'total')); ?>],
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>