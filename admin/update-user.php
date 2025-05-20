<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';

$auth = new Auth();

if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->connect();
    
    $user_id = $_POST['user_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    try {
        $stmt = $conn->prepare("
            UPDATE users 
            SET full_name = ?, email = ?, phone = ?, address = ?
            WHERE id = ?
        ");
        $stmt->execute([$full_name, $email, $phone, $address, $user_id]);
        
        header('Location: users.php?success=User updated successfully');
        exit();
    } catch (PDOException $e) {
        header('Location: users.php?error=Error updating user');
        exit();
    }
}

header('Location: users.php');
exit();
?>