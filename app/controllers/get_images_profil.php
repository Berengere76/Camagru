<?php
require_once dirname(__DIR__) . '/config/database.php';
session_start();

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT image_url, created_at FROM images WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION["user_id"]]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($images);
} catch (Exception $e) {
    echo json_encode(['error' => 'Erreur lors de la récupération des images']);
}
?>
