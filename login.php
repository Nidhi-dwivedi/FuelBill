<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$auth = new Auth();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if ($auth->login($username, $password)) {
        if ($auth->isAdmin()) {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: user/dashboard.php');
        }
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <i class="fas fa-gas-pump text-4xl text-blue-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Welcome Back</h1>
                <p class="text-gray-600">Sign in to your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Username</label>
                    <input type="text" id="username" name="username" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    Sign In
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-600">Don't have an account? 
                    <a href="register.php" class="text-blue-600 hover:text-blue-800">Register here</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>