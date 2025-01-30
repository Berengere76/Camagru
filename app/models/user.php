<!-- pour gerer les donnees des users dans la bdd -->

<?php

require_once dirname(__DIR__) . '/config/database.php';

class User {
    public static function register($username, $email, $password) {
        global $pdo;
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $email, $hashedPassword]);
    }
}

?>