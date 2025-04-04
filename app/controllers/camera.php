<?php
require_once dirname(__DIR__) . '/models/user.php';
require_once dirname(__DIR__) . '/models/image.php';

session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data || !isset($data['image'])) {
        echo json_encode(["error" => "Aucune image reçue"]);
        exit;
    }

    $imageData = str_replace('data:image/png;base64,', '', $data['image']);
    $imageData = base64_decode($imageData);

    if (!$imageData) {
        echo json_encode(["error" => "Erreur de décodage"]);
        exit;
    }

    $uploadDir = dirname(__DIR__) . "/uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imageName = uniqid() . ".png";
    $imagePath = $uploadDir . $imageName;

    if (!file_put_contents($imagePath, $imageData)) {
        echo json_encode(["error" => "Erreur lors de l'enregistrement"]);
        exit;
    }

    $imageUrl = "uploads/" . $imageName;
    $success = Image::saveImage($_SESSION["user_id"], $imageUrl);

    if ($success) {
        echo json_encode(["success" => "Image enregistrée", "image_url" => $imageUrl]);
    } else {
        echo json_encode(["error" => "Erreur SQL"]);
    }
    exit;
}

require_once dirname(__DIR__) . '/views/camera.html';
?>