<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';

$auth = new Auth();

if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header('Location: ../login.php');
    exit();
}

$db = new Database();
$conn = $db->connect();

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

$stmt = $conn->prepare("
    SELECT t.id, t.transaction_date, u.username, u.full_name, 
           f.name as fuel_name, t.liters, t.amount, t.transaction_type
    FROM transactions t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN fuel_types f ON t.fuel_type_id = f.id
    $where_clause
    ORDER BY t.transaction_date DESC
");
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="transactions_report_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// Header row
fputcsv($output, [
    'Transaction ID',
    'Date & Time',
    'Username',
    'Customer Name',
    'Transaction Type',
    'Fuel Type',
    'Liters',
    'Amount'
]);

// Data rows
foreach ($transactions as $transaction) {
    fputcsv($output, [
        $transaction['id'],
        $transaction['transaction_date'],
        $transaction['username'],
        $transaction['full_name'],
        ucfirst($transaction['transaction_type']),
        $transaction['fuel_name'] ?? 'N/A',
        $transaction['liters'] ? $transaction['liters'] . ' L' : 'N/A',
        '₹' . number_format($transaction['amount'], 2)
    ]);
}

fclose($output);
exit();
?>