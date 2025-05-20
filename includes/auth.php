<?php
require_once 'db.php';

class Auth {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function register($username, $email, $password, $full_name, $phone = null, $address = null) {
        // Check if user exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        
        if ($stmt->rowCount() > 0) {
            return false; // User already exists
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user
        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password, full_name, phone, address) 
            VALUES (:username, :email, :password, :full_name, :phone, :address)
        ");
        
        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password,
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address
        ]);
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            return true;
        }

        return false;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function getUser($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>