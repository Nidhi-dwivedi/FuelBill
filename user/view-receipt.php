<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: transactions.php');
    exit();
}

$db = new Database();
$conn = $db->connect();

$transaction_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get transaction details
$stmt = $conn->prepare("
    SELECT t.*, u.full_name, u.email, f.name as fuel_name 
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    LEFT JOIN fuel_types f ON t.fuel_type_id = f.id
    WHERE t.id = ? AND t.user_id = ?
");
$stmt->execute([$transaction_id, $user_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    header('Location: transactions.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt #<?php echo $transaction['id']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { text-align: center; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #2c3e50; }
        .receipt-title { text-align: center; font-size: 20px; margin: 20px 0; }
        .receipt-details { margin-bottom: 20px; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .detail-label { font-weight: bold; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <div class='logo'>Petrofy</div>
            <div>India</div>
        </div>
        
        <div class='receipt-title'>TRANSACTION RECEIPT</div>
        
        <div class='receipt-details'>
            <div class='detail-row'>
                <span class='detail-label'>Customer Name:</span>
                <span><?php echo htmlspecialchars($transaction['full_name']); ?></span>
            </div>
            <div class='detail-row'>
                <span class='detail-label'>Transaction ID:</span>
                <span>#<?php echo $transaction['id']; ?></span>
            </div>
            <div class='detail-row'>
                <span class='detail-label'>Date & Time:</span>
                <span><?php echo date('M d, Y H:i', strtotime($transaction['transaction_date'])); ?></span>
            </div>
            <div class='detail-row'>
                <span class='detail-label'>Transaction Type:</span>
                <span><?php echo ucfirst($transaction['transaction_type']); ?></span>
            </div>
            <?php if ($transaction['transaction_type'] === 'fuel'): ?>
            <div class='detail-row'>
                <span class='detail-label'>Fuel Type:</span>
                <span><?php echo htmlspecialchars($transaction['fuel_name']); ?></span>
            </div>
            <div class='detail-row'>
                <span class='detail-label'>Liters:</span>
                <span><?php echo $transaction['liters']; ?> L</span>
            </div>
            <?php endif; ?>
            <div class='detail-row'>
                <span class='detail-label'>Amount:</span>
                <span>₹<?php echo number_format($transaction['amount'], 2); ?></span>
            </div>
        </div>
        
        <div class='footer'>
            Thank you for your business!<br>
            For any queries, please contact petrofy8@gmail.com
        </div>
    </div>
</body>
</html>