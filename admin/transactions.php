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

// Handle transaction deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ?");
    $stmt->execute([$id]);
    
    header('Location: transactions.php');
    exit();
}

// Build filter conditions
$where = [];
$params = [];

if (isset($_GET['username']) && !empty($_GET['username'])) {
    $where[] = "u.username = ?";
    $params[] = $_GET['username'];
}

if (isset($_GET['from_date']) && !empty($_GET['from_date'])) {
    $where[] = "DATE(t.transaction_date) >= ?";
    $params[] = $_GET['from_date'];
}

if (isset($_GET['to_date']) && !empty($_GET['to_date'])) {
    $where[] = "DATE(t.transaction_date) <= ?";
    $params[] = $_GET['to_date'];
}

$where_clause = $where ? "WHERE " . implode(" AND ", $where) : "";

// Get all transactions with user info
$stmt = $conn->prepare("
    SELECT t.*, u.username, u.full_name, f.name as fuel_name 
    FROM transactions t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN fuel_types f ON t.fuel_type_id = f.id
    $where_clause
    ORDER BY t.transaction_date DESC
");
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get fuel types for the form
$fuel_types = $conn->query("SELECT * FROM fuel_types")->fetchAll(PDO::FETCH_ASSOC);
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
                <a href="users.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
                    <i class="fas fa-users mr-2"></i>Users
                </a>
                <a href="transactions.php" class="block py-2.5 px-4 rounded transition duration-200 bg-blue-700 text-white">
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
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">All Transactions</h2>
                    <button onclick="document.getElementById('addTransactionModal').classList.remove('hidden')" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-plus mr-2"></i>Add Transaction
                    </button>
                </div>

                <!-- Filter Section -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Filter Transactions</h3>
                    <form method="GET" action="transactions.php" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                                Username
                            </label>
                            <select name="username" id="username" class="w-full px-3 py-2 border rounded">
                                <option value="">All Users</option>
                                <?php
                                $users = $conn->query("SELECT DISTINCT username FROM users ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($users as $user) {
                                    $selected = isset($_GET['username']) && $_GET['username'] === $user['username'] ? 'selected' : '';
                                    echo "<option value='{$user['username']}' $selected>{$user['username']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="from_date">
                                From Date
                            </label>
                            <input type="date" name="from_date" id="from_date" value="<?php echo $_GET['from_date'] ?? ''; ?>"
                                   class="w-full px-3 py-2 border rounded">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="to_date">
                                To Date
                            </label>
                            <input type="date" name="to_date" id="to_date" value="<?php echo $_GET['to_date'] ?? ''; ?>"
                                   class="w-full px-3 py-2 border rounded">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                                Filter
                            </button>
                            <?php if (isset($_GET['username']) || isset($_GET['from_date']) || isset($_GET['to_date'])): ?>
                            <a href="transactions.php" class="ml-2 bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                                Clear
                            </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fuel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Liters</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?php echo $transaction['id']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo $transaction['full_name'] ?? 'N/A'; ?>
                                        </td>
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
                                            <a href="?delete=<?php echo $transaction['id']; ?>" 
                                               class="text-red-600 hover:text-red-900 mr-3"
                                               onclick="return confirm('Are you sure you want to delete this transaction?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <a href="#" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-edit"></i>
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

    <!-- Add Transaction Modal -->
    <div id="addTransactionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Add New Transaction</h3>
                <button onclick="document.getElementById('addTransactionModal').classList.add('hidden')" 
                        class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="../transaction-process.php" method="POST" id="transactionForm">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="user_id">
                        User
                    </label>
                    <select name="user_id" id="user_id" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <?php
                        $users = $conn->query("SELECT id, username, full_name FROM users")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($users as $user) {
                            echo "<option value='{$user['id']}'>{$user['full_name']} ({$user['username']})</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="transaction_type">
                        Transaction Type
                    </label>
                    <select name="transaction_type" id="transaction_type" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="fuel">Fuel Purchase</option>
                        <option value="service">Service</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="mb-4" id="fuelFields">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="fuel_type_id">
                        Fuel Type
                    </label>
                    <select name="fuel_type_id" id="fuel_type_id"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <?php foreach ($fuel_types as $fuel): ?>
                            <option value="<?php echo $fuel['id']; ?>" data-price="<?php echo $fuel['price_per_liter']; ?>">
                                <?php echo $fuel['name']; ?> (₹<?php echo $fuel['price_per_liter']; ?>/L)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <div class="mt-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="liters">
                            Liters
                        </label>
                        <input type="number" step="0.01" name="liters" id="liters"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div class="mt-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="fuel_amount">
                            Amount (₹)
                        </label>
                        <input type="number" step="0.01" name="fuel_amount" id="fuel_amount" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>
                
                <div class="mb-4 hidden" id="otherFields">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="other_amount">
                        Amount (₹)
                    </label>
                    <input type="number" step="0.01" name="other_amount" id="other_amount" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    
                    <label class="block text-gray-700 text-sm font-bold mb-2 mt-2" for="description">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="2"
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>
                
                <input type="hidden" name="amount" id="final_amount">
                
                <div class="flex justify-end">
                    <button type="button" onclick="document.getElementById('addTransactionModal').classList.add('hidden')" 
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        document.querySelector('.md\\:hidden').addEventListener('click', function() {
            document.querySelector('.transform').classList.toggle('-translate-x-full');
        });

        // Show/hide fields based on transaction type
        document.getElementById('transaction_type').addEventListener('change', function() {
            const type = this.value;
            const fuelFields = document.getElementById('fuelFields');
            const otherFields = document.getElementById('otherFields');
            const fuelAmount = document.getElementById('fuel_amount');
            const otherAmount = document.getElementById('other_amount');
            
            if (type === 'fuel') {
                fuelFields.classList.remove('hidden');
                otherFields.classList.add('hidden');
                fuelAmount.required = true;
                otherAmount.required = false;
            } else {
                fuelFields.classList.add('hidden');
                otherFields.classList.remove('hidden');
                fuelAmount.required = false;
                otherAmount.required = true;
            }
        });

        // Calculate amount based on liters and fuel price
        document.getElementById('liters').addEventListener('input', function() {
            const liters = parseFloat(this.value);
            const fuelSelect = document.getElementById('fuel_type_id');
            const selectedOption = fuelSelect.options[fuelSelect.selectedIndex];
            const pricePerLiter = parseFloat(selectedOption.getAttribute('data-price'));
            
            if (!isNaN(liters) && !isNaN(pricePerLiter)) {
                document.getElementById('fuel_amount').value = (liters * pricePerLiter).toFixed(2);
            }
        });

        // Update amount when fuel type changes
        document.getElementById('fuel_type_id').addEventListener('change', function() {
            const liters = parseFloat(document.getElementById('liters').value);
            const selectedOption = this.options[this.selectedIndex];
            const pricePerLiter = parseFloat(selectedOption.getAttribute('data-price'));
            
            if (!isNaN(liters) && !isNaN(pricePerLiter)) {
                document.getElementById('fuel_amount').value = (liters * pricePerLiter).toFixed(2);
            }
        });

        // Handle form submission
        document.getElementById('transactionForm').addEventListener('submit', function(e) {
            const type = document.getElementById('transaction_type').value;
            const finalAmount = document.getElementById('final_amount');
            
            if (type === 'fuel') {
                finalAmount.value = document.getElementById('fuel_amount').value;
            } else {
                finalAmount.value = document.getElementById('other_amount').value;
            }
            
            // Validate amount
            if (!finalAmount.value || isNaN(finalAmount.value)) {
                e.preventDefault();
                alert('Please enter a valid amount');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>