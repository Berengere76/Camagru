<?php
require_once dirname(__DIR__) . '/models/user.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["register"])) {
        $username = trim($_POST["username"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];

        if (User::register($username, $email, $password)) {
            // header("Location: http://localhost:8000/login");
            // exit();
            echo "Register success";
        }
        else {
            echo "Register failed";
        }
    }
}
?>