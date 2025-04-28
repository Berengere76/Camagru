<?php

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/user.php';
require_once dirname(__DIR__) . '/models/image.php';
require_once dirname(__DIR__) . '/models/comment.php';
require_once dirname(__DIR__) . '/models/like.php';

session_start();

$images = Image::getAllImagesWithUser();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    $imagesWithCounts = [];
    foreach ($images as $image) {
        $likeCount = Like::getLikeCount($image['id']);
        $commentCount = Comment::getCommentCount($image['id']);
        $imagesWithCounts[] = array_merge($image, ['like_count' => $likeCount, 'comment_count' => $commentCount]);
    }
    echo json_encode($imagesWithCounts);
    exit;
}

require_once dirname(__DIR__) . '/views/galerie.html';
?>
