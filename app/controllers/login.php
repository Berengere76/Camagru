<?php
require_once dirname(__DIR__) . '/models/user.php';

session_start();

$errors = [];
$success = $_SESSION["success"] ?? null;
unset($_SESSION["success"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["login"])) {
        $username = trim($_POST["username"]);
        $password = $_POST["password"];
    
        $user = User::login($username, $password);
    
        if ($user) {
            $_SESSION["username"] = $user["username"];  
            $_SESSION["user_id"] = $user["id"];
            header("Location: home.php");
            exit;
        } else {
            $errors[] = "Échec d'authentification";
        }
    }    
}

require_once dirname(__DIR__) . '/views/login.php';
