<?php
require_once dirname(__DIR__) . '/config/database.php';

class Like {

    public static function likeImage($user_id, $image_id) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, image_id) VALUES (?, ?)");
        return $stmt->execute([$user_id, $image_id]);
    }

    public static function unlikeImage($user_id, $image_id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND image_id = ?");
        return $stmt->execute([$user_id, $image_id]);
    }

    public static function isLikedByUser($user_id, $image_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE user_id = ? AND image_id = ?");
        $stmt->execute([$user_id, $image_id]);
        return $stmt->fetchColumn() > 0;
    }

    public static function getLikeCount($image_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE image_id = ?");
        $stmt->execute([$image_id]);
        return $stmt->fetchColumn();
    }
}
?>