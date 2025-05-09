<?php
require_once dirname(__DIR__) . '/models/user.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["login"])) {
        $username = trim($_POST["username"]);
        $username = htmlspecialchars($username);
        $password = $_POST["password"];

        try {
            $user = User::login($username, $password);

            if ($user) {
                $_SESSION["username"] = $user["username"];
                $_SESSION["user_id"] = $user["id"];
                header("Location: home.php");
                exit;
            } else {
                $_SESSION["errors"] = "Nom d'utilisateur ou mot de passe incorrect";
                header("Location: login.php");
                exit;
            }
        } catch (Exception $e) {
            $_SESSION["errors"] = $e->getMessage();
            header("Location: login.php");
            exit;
        }
    }
}

require_once dirname(__DIR__) . '/views/login.html';

?>