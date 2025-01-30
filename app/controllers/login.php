<?php
require_once dirname(__DIR__) . '/models/user.php';

session_start();

$errors = [];
$success = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["login"])) {
        $username = trim($_POST["username"]);
        $password = $_POST["password"];

        if (User::login($username, $password)) {
            $_SESSION["username"] = $username;
            header("Location: home.php");
            exit;
        } else {
            echo "Login failed";
        }
    }
}

if (isset($_GET["register"]) && $_GET["register"] === "success") {
    $success[] = "Inscription réussie";
}

require_once dirname(__DIR__) . '/views/login.php';
