<?php
require_once dirname(__DIR__) . '/config/database.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT images.image_url, users.username, images.created_at FROM images INNER JOIN users ON images.user_id = users.id ORDER BY images.created_at DESC");
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($images);
} catch (Exception $e) {
    echo json_encode(['error' => 'Erreur lors de la récupération des images']);
}
?>
