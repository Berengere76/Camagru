<?php
require_once dirname(__DIR__) . '/config/database.php';

class Image {

    public static function saveImage($user_id, $image_url) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO images (user_id, image_url) VALUES (?, ?)");
        return $stmt->execute([$user_id, $image_url]);
    }

    public static function getAllImagesWithUser() {
        global $pdo;
        $stmt = $pdo->query("SELECT images.id, images.image_url, users.username, images.created_at 
                             FROM images 
                             INNER JOIN users ON images.user_id = users.id 
                             ORDER BY images.created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getImagesByUserId($user_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id, image_url, created_at FROM images WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deleteImage($user_id, $image_url) {
        global $pdo;
 
        $filePath = dirname(__DIR__) . '/' . $image_url;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    
        $stmt = $pdo->prepare("DELETE FROM images WHERE user_id = ? AND image_url = ?");
        return $stmt->execute([$user_id, $image_url]);
    }

    public static function getImageById($image_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT images.image_url, images.created_at, users.username
                               FROM images 
                               INNER JOIN users ON images.user_id = users.id 
                               WHERE images.id = ?");
        $stmt->execute([$image_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }       
}

?>