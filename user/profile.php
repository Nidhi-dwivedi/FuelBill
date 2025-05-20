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
$user = $auth->getUser($user_id);

$message = '';
$error = '';

if (isset($_GET['success'])) {
    $message = $_GET['success'];
}

if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    try {
        $stmt = $conn->prepare("
            UPDATE users 
            SET full_name = ?, email = ?, phone = ?, address = ?
            WHERE id = ?
        ");
        $stmt->execute([$full_name, $email, $phone, $address, $user_id]);
        
        $_SESSION['full_name'] = $full_name;
        $message = 'Profile updated successfully!';
    } catch (PDOException $e) {
        $error = 'Error updating profile: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo APP_NAME; ?></title>
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
                <a href="profile.php" class="block py-2.5 px-4 rounded transition duration-200 bg-blue-700 text-white">
                    <i class="fas fa-user mr-2"></i>Profile
                </a>
                <a href="transactions.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
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
                        <h1 class="text-xl font-semibold text-gray-800">Profile</h1>
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
                <div class="max-w-3xl mx-auto">
                    <?php if ($message): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-6">Personal Information</h2>
                        
                        <form method="POST" action="profile.php">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                                        Username
                                    </label>
                                    <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" disabled>
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="full_name">
                                        Full Name *
                                    </label>
                                    <input type="text" id="full_name" name="full_name" required
                                           value="<?php echo htmlspecialchars($user['full_name']); ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                                        Email *
                                    </label>
                                    <input type="email" id="email" name="email" required
                                           value="<?php echo htmlspecialchars($user['email']); ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                                        Phone Number
                                    </label>
                                    <input type="tel" id="phone" name="phone"
                                           value="<?php echo htmlspecialchars($user['phone']); ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="address">
                                    Address
                                </label>
                                <textarea id="address" name="address" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($user['address']); ?></textarea>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold mb-6">Change Password</h2>
                        
                        <form method="POST" action="update-password.php">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="current_password">
                                    Current Password *
                                </label>
                                <input type="password" id="current_password" name="current_password" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="new_password">
                                    New Password *
                                </label>
                                <input type="password" id="new_password" name="new_password" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div class="mb-6">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm_password">
                                    Confirm New Password *
                                </label>
                                <input type="password" id="confirm_password" name="confirm_password" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                                    Change Password
                                </button>
                            </div>
                        </form>
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