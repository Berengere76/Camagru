<?php
require_once __DIR__ . '/models/user.php';

session_start();

if (isset($_GET["token"])) {
    $token = $_GET["token"];

    if (User::verifyUser($token)) {
        $_SESSION["success"] = "Votre compte a été vérifié avec succès. Vous pouvez maintenant vous connecter.";
        header("Location: /index.php?page=login");
        exit;
    } else {
        $_SESSION["errors"] = "Le lien de vérification est invalide ou a expiré.";
        header("Location: /index.php?page=register");
        exit;
    }
} else {
    $_SESSION["errors"] = "Lien de vérification incorrect.";
    header("Location: /index.php?page=register");
    exit;
}

?>