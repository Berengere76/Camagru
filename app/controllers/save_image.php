<?php

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/user.php';

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['image'])) {
    echo json_encode(["error" => "Aucune image reçue"]);
    exit;
}

$imageData = str_replace('data:image/png;base64,', '', $data['image']);
$imageData = base64_decode($imageData);

if (!$imageData) {
    echo json_encode(["error" => "Erreur lors du décodage de l'image"]);
    exit;
}

$uploadDir = dirname(__DIR__) . "/uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$imageName = uniqid() . ".png";
$imagePath = $uploadDir . $imageName;
if (!file_put_contents($imagePath, $imageData)) {
    echo json_encode(["error" => "Erreur lors de l'enregistrement de l'image"]);
    exit;
}

$userId = $_SESSION["user_id"];
$imageUrl = "uploads/" . $imageName;

try {
    $stmt = $pdo->prepare("INSERT INTO images (user_id, image_url) VALUES (:user_id, :image_url)");
    $stmt->execute([
        ':user_id' => $userId,
        ':image_url' => $imageUrl
    ]);
    echo json_encode(["success" => "Image enregistrée", "image_url" => $imageUrl]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur SQL : " . $e->getMessage()]);
}

?>
