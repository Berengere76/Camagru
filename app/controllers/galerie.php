<?php

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/user.php';

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

global $pdo;
$stmt = $pdo->prepare("SELECT images.image_url, users.username, images.created_at
                       FROM images 
                       JOIN users ON images.user_id = users.id 
                       ORDER BY images.created_at DESC;");
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once dirname(__DIR__) . '/views/galerie.php';
?>
