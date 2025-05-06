<?php
require_once dirname(__DIR__) . '/config/database.php';

class Image
{
    public static function saveImage($userId, $imageUrl)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("INSERT INTO images (user_id, image_url) VALUES (?, ?)");
            $stmt->execute([$userId, $imageUrl]);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors de l'enregistrement de l'image dans la base de données : " . $e->getMessage());
            return false;
        }
    }

    public static function getAllImagesWithUser()
    {
        global $pdo;
        try {
            $stmt = $pdo->query("SELECT images.id, images.image_url, users.username, images.created_at
                                 FROM images
                                 INNER JOIN users ON images.user_id = users.id
                                 ORDER BY images.created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de toutes les images : " . $e->getMessage());
            return [];
        }
    }

    public static function getImagesByUserId($userId, $limit = null)
    {
        global $pdo;
        try {
            $sql = "SELECT id, image_url, created_at FROM images WHERE user_id = ? ORDER BY created_at DESC";
            if ($limit !== null) {
                $sql .= " LIMIT ?";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(1, $userId, PDO::PARAM_INT);
                $stmt->bindValue(2, $limit, $limit !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            } else {
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des images par utilisateur : " . $e->getMessage());
            return [];
        }
    }

    public static function deleteImage($userId, $imageUrl)
    {
        global $pdo;
        $filePath = dirname(__DIR__) . '/' . $imageUrl;
        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                error_log("Erreur lors de la suppression du fichier : " . $filePath);
                return false;
            }
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM images WHERE user_id = ? AND image_url = ?");
            $stmt->execute([$userId, $imageUrl]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de l'image de la base de données : " . $e->getMessage());
            return false;
        }
    }

    public static function getImageById($imageId)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT images.image_url, images.created_at, users.username
                                   FROM images
                                   INNER JOIN users ON images.user_id = users.id
                                   WHERE images.id = ?");
            $stmt->execute([$imageId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de l'image par ID : " . $e->getMessage());
            return false;
        }
    }

    public static function applyFilterAndSave($userId, $baseImagePath, $filterName)
    {
        $uploadDir = dirname(__DIR__) . "/uploads/";
        $filterPath = dirname(__DIR__) . "/images/filters/" . $filterName;
        $finalImageName = uniqid() . "_filtered.png";
        $finalImageUrl = "uploads/" . $finalImageName;
        $finalFilePath = $uploadDir . $finalImageName;

        if (!file_exists($baseImagePath)) {
            error_log("Erreur: Image de base introuvable : " . $baseImagePath);
            return false;
        }
        if (!file_exists($filterPath)) {
            error_log("Erreur: Filtre introuvable : " . $filterPath);
            return false;
        }

        $baseImage = imagecreatefrompng($baseImagePath);
        $filterImage = imagecreatefrompng($filterPath);

        if (!$baseImage) {
            error_log("Erreur: Impossible de créer l'image de base à partir de : " . $baseImagePath);
            return false;
        }
        if (!$filterImage) {
            error_log("Erreur: Impossible de créer l'image du filtre à partir de : " . $filterPath);
            imagedestroy($baseImage);
            return false;
        }

        $baseWidth = imagesx($baseImage);
        $baseHeight = imagesy($baseImage);
        $filterWidth = imagesx($filterImage);
        $filterHeight = imagesy($filterImage);

        $destX = 0;
        $destY = 0;
        $newFilterWidth = $filterWidth;
        $newFilterHeight = $filterHeight;

        if ($filterName === 'chapeau_rigolo.png') {
            $newFilterWidth = (int)($baseWidth / 2.5);
            $newFilterHeight = (int)(($newFilterWidth / $filterWidth) * $filterHeight);
            $destX = (int)(($baseWidth - $newFilterWidth) / 2);
            $destY = (int)($baseHeight / 50);
        } elseif ($filterName === 'lunettes_soleil.png') {
            $newFilterWidth = (int)($baseWidth / 2);
            $newFilterHeight = (int)(($newFilterWidth / $filterWidth) * $filterHeight);
            $destX = (int)(($baseWidth - $newFilterWidth) / 2);
            $destY = (int)($baseHeight / 3);
        } elseif ($filterName !== 'cadre1.png') {
            $newFilterWidth = (int)($baseWidth / 3);
            $newFilterHeight = (int)(($newFilterWidth / $filterWidth) * $filterHeight);
            $destX = (int)(($baseWidth - $newFilterWidth) / 2);
            $destY = (int)(($baseHeight - $newFilterHeight) / 2);
        }

        if (!imagecopyresampled($baseImage, $filterImage, $destX, $destY, 0, 0, $newFilterWidth, $newFilterHeight, $filterWidth, $filterHeight)) {
            error_log("Erreur: imagecopyresampled a échoué.");
            imagedestroy($baseImage);
            imagedestroy($filterImage);
            return false;
        }

        if (imagepng($baseImage, $finalFilePath)) {
            imagedestroy($baseImage);
            imagedestroy($filterImage);
            if (self::saveImage($userId, $finalImageUrl)) {
                return $finalImageUrl;
            } else {
                return false;
            }
        } else {
            error_log("Erreur: Impossible d'enregistrer l'image finale : " . $finalFilePath);
            imagedestroy($baseImage);
            imagedestroy($filterImage);
            return false;
        }
    }
}
