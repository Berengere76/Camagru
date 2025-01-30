<?php
require_once dirname(__DIR__) . '/models/user.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["login"])) {
        $username = trim($_POST["username"]);
        $password = $_POST["password"];

        if (User::login($username, $password)) {
            echo "Login success";
        }
        else {
            echo "Login failed";
        }
    }
}
?>