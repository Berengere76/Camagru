<?php
require_once dirname(__DIR__) . '/config/database.php';

class User {

    public static function register($username, $email, $password) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Ce nom d'utilisateur ou cet email est déjà utilisé");
        }
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $email, $hashedPassword]);
    }

    public static function login($username, $password) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        return ($user && password_verify($password, $user["password"])) ? $user : null;
    }

    public static function getUserById($user_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }  
    
}
?>