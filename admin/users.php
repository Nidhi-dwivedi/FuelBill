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

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Don't allow deleting the admin user
    if ($id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    header('Location: users.php');
    exit();
}

// Get all users
$stmt = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - <?php echo APP_NAME; ?></title>
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
                <a href="users.php" class="block py-2.5 px-4 rounded transition duration-200 bg-blue-700 text-white">
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
                        <h1 class="text-xl font-semibold text-gray-800">Users</h1>
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
                    <h2 class="text-2xl font-semibold">All Users</h2>
                    <a href="../register.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-plus mr-2"></i>Add User
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $user['id']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $user['username']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $user['full_name']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $user['email']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <a href="?delete=<?php echo $user['id']; ?>" 
                                                   class="text-red-600 hover:text-red-900 mr-3"
                                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="#" onclick="openEditModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['full_name']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo htmlspecialchars($user['phone']); ?>', '<?php echo htmlspecialchars($user['address']); ?>')" 
                                               class="text-blue-600 hover:text-blue-900">
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

    <!-- Edit User Modal -->
    <div id="editUserModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Edit User</h3>
                <button onclick="document.getElementById('editUserModal').classList.add('hidden')" 
                        class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editUserForm" method="POST" action="update-user.php">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_full_name">
                        Full Name
                    </label>
                    <input type="text" id="edit_full_name" name="full_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_email">
                        Email
                    </label>
                    <input type="email" id="edit_email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_phone">
                        Phone
                    </label>
                    <input type="tel" id="edit_phone" name="phone"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_address">
                        Address
                    </label>
                    <textarea id="edit_address" name="address" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')" 
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Save Changes
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

        function openEditModal(id, fullName, email, phone, address) {
            document.getElementById('edit_user_id').value = id;
            document.getElementById('edit_full_name').value = fullName;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_phone').value = phone;
            document.getElementById('edit_address').value = address;
            document.getElementById('editUserModal').classList.remove('hidden');
        }
    </script>
</body>
</html>