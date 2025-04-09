<?php
require_once dirname(__DIR__) . '/models/user.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["register"])) {
        $username = trim($_POST["username"]);
        $username = htmlspecialchars($username);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];

        if (empty($username) || empty($email) || empty($password)) {
            $_SESSION["errors"] = "Tous les champs sont obligatoires";
            header("Location: register.php");
            exit;
        }

        try {
            User::register($username, $email, $password);
            $_SESSION["success"] = "Inscription réussie";
            header("Location: login.php");
            exit;
        } catch (Exception $e) {
            $_SESSION["errors"] = $e->getMessage();
        }
    }
}

require_once dirname(__DIR__) . '/views/register.html';
?>