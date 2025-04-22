<?php
require_once dirname(__DIR__) . '/config/database.php';

class Like {

	public static function likeImage($user_id, $image_id) {
		global $pdo;
		$stmt = $pdo->prepare("INSERT INTO likes (user_id, image_id) VALUES (?, ?)");
		return $stmt->execute([$user_id, $image_id]);
	}
}

?>