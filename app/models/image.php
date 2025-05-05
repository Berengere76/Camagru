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

    public static function getImagesByUserId($user_id, $limit = null) {
        global $pdo;
        $sql = "SELECT id, image_url, created_at FROM images WHERE user_id = ? ORDER BY created_at DESC";
        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        }
        $stmt->execute();
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

    public static function applyFilterAndSave($userId, $baseImagePath, $filterName) {
        $uploadDir = dirname(__DIR__) . "/uploads/";
        $filterPath = dirname(__DIR__) . "/images/filters/" . $filterName;
        $finalImageName = uniqid() . "_filtered.png";
        $finalImageUrl = "uploads/" . $finalImageName;
        $finalFilePath = $uploadDir . $finalImageName;

        if (!file_exists($baseImagePath) || !file_exists($filterPath)) {
            error_log("Erreur: Image de base ou filtre introuvable.");
            return false;
        }

        $baseImage = imagecreatefrompng($baseImagePath);
        $filterImage = imagecreatefrompng($filterPath);

        if (!$baseImage || !$filterImage) {
            error_log("Erreur: Impossible d'ouvrir les images.");
            return false;
        }

        $baseWidth = imagesx($baseImage);
        $baseHeight = imagesy($baseImage);
        $filterWidth = imagesx($filterImage);
        $filterHeight = imagesy($filterImage);

        if ($filterName === 'chapeau_rigolo.png') {
            $newFilterWidth = $baseWidth / 2.5;
            $newFilterHeight = ($newFilterWidth / $filterWidth) * $filterHeight;
            $destX = ($baseWidth - $newFilterWidth) / 2;
            $destY = $baseHeight / 50;
            imagecopyresampled($baseImage, $filterImage, $destX, $destY, 0, 0, $newFilterWidth, $newFilterHeight, $filterWidth, $filterHeight);
        } else if ($filterName === 'lunettes_soleil.png') {
            $newFilterWidth = $baseWidth / 2;
            $newFilterHeight = ($newFilterWidth / $filterWidth) * $filterHeight;
            $destX = ($baseWidth - $newFilterWidth) / 2;
            $destY = $baseHeight / 3;
            imagecopyresampled($baseImage, $filterImage, $destX, $destY, 0, 0, $newFilterWidth, $newFilterHeight, $filterWidth, $filterHeight);
        } else if ($filterName === 'cadre1.png') {
            imagecopyresampled($baseImage, $filterImage, 0, 0, 0, 0, $baseWidth, $baseHeight, $filterWidth, $filterHeight);
        } else {
            $newFilterWidth = $baseWidth / 3;
            $newFilterHeight = ($newFilterWidth / $filterWidth) * $filterHeight;
            $destX = ($baseWidth - $newFilterWidth) / 2;
            $destY = ($baseHeight - $newFilterHeight) / 2;
            imagecopyresampled($baseImage, $filterImage, $destX, $destY, 0, 0, $newFilterWidth, $newFilterHeight, $filterWidth, $filterHeight);
        }

        if (imagepng($baseImage, $finalFilePath)) {
            imagedestroy($baseImage);
            imagedestroy($filterImage);
            return self::saveImage($userId, $finalImageUrl) ? $finalImageUrl : false;
        } else {
            error_log("Erreur: Impossible d'enregistrer l'image finale.");
            imagedestroy($baseImage);
            imagedestroy($filterImage);
            return false;
        }
    }
}

?>