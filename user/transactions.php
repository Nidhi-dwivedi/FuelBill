<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Database connection
$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION['user_id'];

// Get all transactions for the user
$stmt = $conn->prepare("
    SELECT t.*, f.name as fuel_name 
    FROM transactions t
    LEFT JOIN fuel_types f ON t.fuel_type_id = f.id
    WHERE t.user_id = ?
    ORDER BY t.transaction_date DESC
");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - <?php echo APP_NAME; ?></title>
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
                <a href="dashboard.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="profile.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
                    <i class="fas fa-user mr-2"></i>Profile
                </a>
                <a href="transactions.php" class="block py-2.5 px-4 rounded transition duration-200 bg-blue-700 text-white">
                    <i class="fas fa-exchange-alt mr-2"></i>Transactions
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
                        <h1 class="text-xl font-semibold text-gray-800">Transactions</h1>
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
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold">Your Transactions</h2>
                    <p class="text-gray-600">View all your fuel and service transactions</p>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fuel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Liters</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?php echo $transaction['id']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo ucfirst($transaction['transaction_type']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo $transaction['fuel_name'] ?? 'N/A'; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo $transaction['liters'] ? $transaction['liters'] . ' L' : 'N/A'; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            ₹<?php echo number_format($transaction['amount'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M d, Y H:i', strtotime($transaction['transaction_date'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <a href="view-receipt.php?id=<?php echo $transaction['id']; ?>" 
                                               target="_blank" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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