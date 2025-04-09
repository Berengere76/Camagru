<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/user.php';
require_once dirname(__DIR__) . '/models/image.php';

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['info_profil'])) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (is_array($data)) {
        $_POST = $data;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updatePassword'])) {

    $user_id = $_SESSION["user_id"];
    $password = htmlspecialchars($_POST['password']);
    $new_password = htmlspecialchars($_POST['new_password']);

    if (password_verify($password, $user['password'])) {
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

        if (User::updatePassword($user_id, $hashed_new_password)) {
            echo json_encode(["success" => "Mise à jour réussie"]);
        } else {
            echo json_encode(["error" => "Erreur de mise à jour"]);
        }
    } else {
        echo json_encode(["error" => "Mot de passe actuel incorrect"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateUsername'])) {

    $user_id = $_SESSION["user_id"];
    $username = htmlspecialchars($_POST['username']);
    try {
        User::updateUsername($user_id, $username);
        $_SESSION["username"] = $username;
        $_SESSION["success"] = "Modification réussie";
    } catch (Exception $e) {
        $_SESSION["errors"] = $e->getMessage();
    }
}

require_once dirname(__DIR__) . '/views/profil.html';
?>
