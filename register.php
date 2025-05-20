<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$auth = new Auth();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        if ($auth->register($username, $email, $password, $full_name, $phone, $address)) {
            $success = 'Registration successful! You can now login.';
        } else {
            $error = 'Registration failed. Username or email may already exist.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo APP_NAME; ?></title>
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
                <h1 class="text-2xl font-bold text-gray-800">Create an Account</h1>
                <p class="text-gray-600">Join our Petrofy</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $success; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="login.php" class="text-blue-600 hover:text-blue-800 font-medium">Go to Login</a>
                </div>
            <?php else: ?>
                <form method="POST" action="register.php">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Username*</label>
                            <input type="text" id="username" name="username" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email*</label>
                            <input type="email" id="email" name="email" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password*</label>
                                <input type="password" id="password" name="password" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="confirm_password" class="block text-gray-700 text-sm font-medium mb-2">Confirm Password*</label>
                                <input type="password" id="confirm_password" name="confirm_password" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div>
                            <label for="full_name" class="block text-gray-700 text-sm font-medium mb-2">Full Name*</label>
                            <input type="text" id="full_name" name="full_name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-gray-700 text-sm font-medium mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="address" class="block text-gray-700 text-sm font-medium mb-2">Address</label>
                            <textarea id="address" name="address" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" 
                            class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                        Register
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">Already have an account? 
                        <a href="login.php" class="text-blue-600 hover:text-blue-800">Login here</a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>