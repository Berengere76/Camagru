<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/user.php';
require_once dirname(__DIR__) . '/models/image.php';
require_once dirname(__DIR__) . '/models/comment.php';
require_once dirname(__DIR__) . '/models/like.php';

session_start();

if (!isset($_SESSION["username"])) {
    $current_user_id = null;
    header("Location: login.php");
    exit;
} else {
    $current_user_id = $_SESSION["user_id"];
}

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["imageid"]) && isset($_GET["ajax"])) {
    header("Content-Type: application/json");

    $image = Image::getImageById($_GET["imageid"]);
    if (!$image) {
        http_response_code(404);
        echo json_encode(["error" => "Image non trouvée"]);
        exit;
    }

    $comments = Comment::getCommentsByImageId($_GET["imageid"]);
    if ($comments === false) {
        http_response_code(500);
        echo json_encode(["error" => "Erreur lors du chargement des commentaires"]);
        exit;
    }

    echo json_encode([
        "image" => $image,
        "comments" => $comments,
        "current_user_id" => $current_user_id
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header("Content-Type: application/json");

    if (isset($_POST["sendcomment"])) {
        if (empty($_POST["comment-input"])) {
            echo json_encode(["error" => "Le commentaire ne peut pas être vide"]);
            exit;
        }

        $user_id = $_SESSION["user_id"];
        $image_id = $_POST["imageid"];
        $comment = htmlspecialchars($_POST["comment-input"]);

        $comment = Comment::postComment($user_id, $image_id, $comment);
        if (!$comment) {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de l'envoi du commentaire"]);
            exit;
        }
        echo json_encode(["success" => "Commentaire envoyé avec succès"]);
        exit;
    }

    if (isset($_POST["deletecomment"]) && isset($_POST["commentid"])) {
        $user_id = $_SESSION["user_id"];
        $comment_id = $_POST["commentid"];

        $deleted = Comment::deleteComment($user_id, $comment_id);
        if ($deleted) {
            echo json_encode(["success" => "Commentaire supprimé avec succès"]);
            exit;
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de la suppression du commentaire"]);
            exit;
        }
    }
}

require_once dirname(__DIR__) . '/views/image.html';
?>