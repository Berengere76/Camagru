<?php
require_once dirname(__DIR__) . '/models/user.php';
require_once dirname(__DIR__) . '/models/image.php';

session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data || !isset($data['image']) || !isset($data['filter'])) {
        echo json_encode(["error" => "Données manquantes"]);
        exit;
    }

    $imageData = str_replace('data:image/png;base64,', '', $data['image']);
    $imageData = base64_decode($imageData);
    $filterName = $data['filter'];

    if (!$imageData) {
        echo json_encode(["error" => "Erreur de décodage de l'image"]);
        exit;
    }

    $maxFileSize = 2 * 1024 * 1024;
    if (strlen($imageData) > $maxFileSize) {
        echo json_encode(["error" => "La taille de l'image dépasse la limite de 2 Mo."]);
        exit;
    }

    if (strpos($data['image'], 'data:image/png;base64,') !== 0 &&
        strpos($data['image'], 'data:image/jpeg;base64,') !== 0) {
        echo json_encode(["error" => "Type de fichier non autorisé (vérification base64)."]);
        exit;
    }

    $imageResource = imagecreatefromstring($imageData);
    if ($imageResource === false) {
        echo json_encode(["error" => "Le contenu de la base64 n'est pas une image valide."]);
        imagedestroy($imageResource);
        exit;
    }

    $filteredImageData = Image::applyFilter($imageData, $filterName);

    if ($filteredImageData) {
        if (Image::saveImage($user_id, $filteredImageData)) {
            echo json_encode(["success" => "Photo prise avec succès"]);
        } else {
            echo json_encode(["error" => "Erreur lors de l'enregistrement de l'image dans la base de données."]);
        }
        exit;
    } else {
        echo json_encode(["error" => "Erreur lors de l'application du filtre."]);
        exit;
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['action']) && $_GET['action'] === 'latest') {
    header('Content-Type: application/json');
    $latest_images = Image::getImagesByUserId($user_id, 6);
    echo json_encode($latest_images);
    exit;
}

require_once dirname(__DIR__) . '/views/camera.html';
?>