<?php
require_once dirname(__DIR__) . '/config/database.php';

class User
{
    private static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    public static function deleteByEmail($email) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
        return $stmt->execute([$email]);
    }    

    public static function register($username, $email, $password)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Ce nom d'utilisateur ou cet email est déjà utilisé");
        }
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $verificationToken = self::generateToken();
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, verification_token) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashedPassword, $verificationToken])) {
            return $verificationToken;
        }
        return false;
    }

    public static function login($username, $password)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user["password"])) {
            if ($user["is_verified"] == 0) {
                throw new Exception("Votre compte n'a pas été vérifié. Veuillez consulter votre email.");
            }
            return $user;
        } else {
            return null;
        }
    }

    public static function getUserById($user_id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT username, password, email, created_at, is_verified FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function updatePassword($user_id, $new_password)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$new_password, $user_id]);
    }

    public static function updateUsername($user_id, $new_username)
    {
        global $pdo;
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $checkStmt->execute([$new_username]);
        if ($checkStmt->fetch()) {
            throw new Exception("Ce nom d'utilisateur est déjà utilisé");
        }
        $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
        return $stmt->execute([$new_username, $user_id]);
    }

    public static function verifyUser($token)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM users WHERE verification_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        if ($user) {
            $updateStmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
            return $updateStmt->execute([$user["id"]]);
        }
        return false;
    }

    public static function getUserbyImageId($image_id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT u.id, u.username, u.email FROM users u JOIN images i ON u.id = i.user_id WHERE i.id = ?");
        $stmt->execute([$image_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>