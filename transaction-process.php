<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/mailer.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Database connection
$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $transaction_type = $_POST['transaction_type'];
    $amount = $_POST['amount'];
    $fuel_type_id = isset($_POST['fuel_type_id']) ? $_POST['fuel_type_id'] : null;
    $liters = isset($_POST['liters']) ? $_POST['liters'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;
    
    try {
        // Insert transaction
        $stmt = $conn->prepare("
            INSERT INTO transactions (user_id, fuel_type_id, liters, amount, transaction_type, description)
            VALUES (:user_id, :fuel_type_id, :liters, :amount, :transaction_type, :description)
        ");
        
        $stmt->execute([
            'user_id' => $user_id,
            'fuel_type_id' => $fuel_type_id,
            'liters' => $liters,
            'amount' => $amount,
            'transaction_type' => $transaction_type,
            'description' => $description
        ]);
        
        $transaction_id = $conn->lastInsertId();
        
        // Get user and fuel info for email
        $user = $auth->getUser($user_id);
        $fuel_name = 'N/A';
        
        if ($fuel_type_id) {
            $stmt = $conn->prepare("SELECT name FROM fuel_types WHERE id = ?");
            $stmt->execute([$fuel_type_id]);
            $fuel = $stmt->fetch(PDO::FETCH_ASSOC);
            $fuel_name = $fuel['name'];
        }
        
        // Send receipt email
        Mailer::sendReceipt(
            $user['email'],
            $user['full_name'],
            $transaction_id,
            $amount,
            $fuel_name,
            $liters,
            date('Y-m-d H:i:s')
        );
        
        // Redirect based on user role
        if ($auth->isAdmin()) {
            header('Location: admin/transactions.php');
        } else {
            header('Location: user/transactions.php');
        }
        exit();
        
    } catch (PDOException $e) {
        die("Error processing transaction: " . $e->getMessage());
    }
}

header('Location: index.php');
exit();
?>