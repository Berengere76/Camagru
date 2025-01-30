<!-- pour gerer l'inscription des users -->

<?php

require_once dirname(__DIR__) . '/models/user.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["register"])) {
        $username = trim($_POST["username"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];

        User::register($username, $email, $password);
    }
}

?>