<?php
require_once dirname(__DIR__) . '/config/database.php';

class Image
{
    public static function saveImage($userId, $imageData)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("INSERT INTO images (user_id, image_data) VALUES (?, ?)");
            $stmt->bindParam(1, $userId, PDO::PARAM_INT);
            $stmt->bindParam(2, $imageData, PDO::PARAM_LOB);
            $stmt->execute();
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
            $stmt = $pdo->query("SELECT images.id, images.image_data, users.username, images.created_at
                                 FROM images
                                 INNER JOIN users ON images.user_id = users.id
                                 ORDER BY images.created_at DESC");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function ($row) {
                return [
                    'id' => $row['id'],
                    'image_url' => 'data:image/png;base64,' . base64_encode($row['image_data']),
                    'username' => $row['username'],
                    'created_at' => $row['created_at']
                ];
            }, $results);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de toutes les images : " . $e->getMessage());
            return [];
        }
    }

    public static function getImagesByUserId($userId, $limit = null)
    {
        global $pdo;
        try {
            $sql = "SELECT id, image_data, created_at FROM images WHERE user_id = ? ORDER BY created_at DESC";
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
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function ($row) {
                return ['id' => $row['id'], 'image_url' => 'data:image/png;base64,' . base64_encode($row['image_data']), 'created_at' => $row['created_at']];
            }, $results);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des images par utilisateur : " . $e->getMessage());
            return [];
        }
    }

    public static function deleteImage($userId, $imageId)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("DELETE FROM images WHERE user_id = ? AND id = ?");
            $stmt->execute([$userId, $imageId]);
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
            $stmt = $pdo->prepare("SELECT images.image_data, images.created_at, users.username
                                   FROM images
                                   INNER JOIN users ON images.user_id = users.id
                                   WHERE images.id = ?");
            $stmt->execute([$imageId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return ['image_url' => 'data:image/png;base64,' . base64_encode($result['image_data']), 'created_at' => $result['created_at'], 'username' => $result['username']];
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de l'image par ID : " . $e->getMessage());
            return false;
        }
    }

    public static function applyFilter($imageData, $filterName)
    {
        $filterPath = dirname(__DIR__) . "/images/filters/" . $filterName;

        $baseImage = imagecreatefromstring($imageData);
        $filterImage = imagecreatefrompng($filterPath);

        if (!$baseImage) {
            error_log("Erreur: Impossible de créer l'image de base.");
            return false;
        }
        if (!$filterImage) {
            error_log("Erreur: Impossible de créer l'image du filtre : " . $filterPath);
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
        } elseif ($filterName === 'cadre1.png') {
            $newFilterWidth = $baseWidth;
            $newFilterHeight = $baseHeight;
            $destX = 0;
            $destY = 0;
        }

        if (!imagecopyresampled($baseImage, $filterImage, $destX, $destY, 0, 0, $newFilterWidth, $newFilterHeight, $filterWidth, $filterHeight)) {
            error_log("Erreur: imagecopyresampled a échoué.");
            imagedestroy($baseImage);
            imagedestroy($filterImage);
            return false;
        }

        ob_start();
        imagepng($baseImage);
        $finalImageData = ob_get_contents();
        ob_end_clean();

        imagedestroy($baseImage);
        imagedestroy($filterImage);

        return $finalImageData;
    }
}
