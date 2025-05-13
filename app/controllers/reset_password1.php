<?php
require_once dirname(__DIR__) . '/models/user.php';

session_start();

if (isset($_GET["token"])) {
    $token = $_GET["token"];
    $user = User::getUserByResetToken($token);
    if (!$user) {
        $_SESSION['errors'] = "Lien invalide ou expiré.";
        header("Location: login.php");
        exit;
    }
    $_SESSION['userId'] = $user['id'];
    $_SESSION['reset_token'] = $token;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['resetmdp1'])) {
        if (!isset($_SESSION['reset_token'])) {
            $_SESSION['errors'] = "Token manquant.";
            header("Location: login.php");
            exit;
        }
        $token = $_SESSION['reset_token'];

        $password = trim($_POST['password']);
        $password = htmlspecialchars($password);

        if (empty($password)) {
            $_SESSION["errors"] = "Veuillez entrer un mot de passe.";
            header("Location: reset_password1.php?token=" . $token);
            exit;
        }
        $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
        if (!preg_match($passwordRegex, $password)) {
            $_SESSION["errors"] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
            header("Location: reset_password1.php?token=" . $token);
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        User::updatePasswordById($_SESSION['userId'], $hashedPassword);
        User::clearResetToken($_SESSION['userId']);

        unset($_SESSION['userId']);
        unset($_SESSION['reset_token']);

        $_SESSION['success'] = "Mot de passe mis à jour avec succès.";
        header("Location: login.php");
        exit;
    }
}

require_once dirname(__DIR__) . '/views/reset_password1.html';

?>