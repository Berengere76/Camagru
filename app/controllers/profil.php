<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/user.php';
require_once dirname(__DIR__) . '/models/image.php';

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['test'])) {
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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['imageid'])) {
    header("Content-Type: application/json");

    $images = Image::getImagesByUserId($_SESSION["user_id"]);

    if (!$images) {
        http_response_code(404);
        echo json_encode(["error" => "Aucune image trouvée"]);
        exit;
    }

    echo json_encode($images);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    header("Content-Type: application/json");

    $imageUrl = $_POST['image_url'] ?? null;
    $userId = $_SESSION["user_id"];

    if (!$imageUrl) {
        echo json_encode(["error" => "Image non spécifiée"]);
        exit;
    }

    if (Image::deleteImage($userId, $imageUrl)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Échec de la suppression"]);
    }
    exit;
}

require_once dirname(__DIR__) . '/views/profil.html';
?>
