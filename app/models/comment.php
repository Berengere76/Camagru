<?php
require_once dirname(__DIR__) . '/config/database.php';

class Comment {

    public static function postComment($user_id, $image_id, $comment) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, image_id, comment) VALUES (?, ?, ?)");
        return $stmt->execute([$user_id, $image_id, $comment]);
    }

    public static function deleteComment ($user_id, $comment_id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM comments WHERE user_id = ? AND id = ?");
        return $stmt->execute([$user_id, $comment_id]);
    }

    public static function getCommentsByImageId($image_id) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT c.id, c.comment, u.username, c.user_id AS comment_user_id
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.image_id = ?
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$image_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>