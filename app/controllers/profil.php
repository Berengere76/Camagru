<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/user.php';
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['test'])) {
    header("Content-Type: application/json");

    $user = User::getUserById($_SESSION["user_id"]);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["error" => "Utilisateur non trouvé"]);
        exit;
    }

    echo json_encode($user);
    exit;
}

require_once dirname(__DIR__) . '/views/profil.html';
?>