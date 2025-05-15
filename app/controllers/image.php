<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/user.php';
require_once dirname(__DIR__) . '/models/image.php';
require_once dirname(__DIR__) . '/models/comment.php';
require_once dirname(__DIR__) . '/models/like.php';

session_start();

function sendMail($to, $subject, $message)
{
    $headers = "From: Camagru <lebasberengere@gmail.com>\r\n";
    if (mail($to, $subject, $message, $headers)) {
        return true;
    } else {
        error_log("Erreur lors de l'envoi de l'email avec la fonction mail()");
        return false;
    }
}

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
        echo json_encode(["error" => "Image non trouvée"]);
        exit;
    }

    $comments = Comment::getCommentsByImageId($_GET["imageid"]);
    if ($comments === false) {
        http_response_code(500);
        echo json_encode(["error" => "Erreur lors du chargement des commentaires"]);
        exit;
    }

    $likeCount = Like::getLikeCount($_GET["imageid"]);
    $isLiked = $current_user_id ? Like::isLikedByUser($current_user_id, $_GET["imageid"]) : false;

    echo json_encode([
        "image" => $image,
        "comments" => $comments,
        "current_user_id" => $current_user_id,
        "like_count" => $likeCount,
        "is_liked" => $isLiked
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
        $user = User::getUserById($user_id);

        $comment = Comment::postComment($user_id, $image_id, $comment);
        if (!$comment) {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de l'envoi du commentaire"]);
            exit;
        }
        $userImage_id = User::getUserbyImageId($image_id);
        if ($userImage_id["id"] != $user_id) {
            if ($userImage_id["com_mail"] == 1)
            {
                sendMail($userImage_id["email"], "Nouveau commentaire sur votre image", "Vous avez reçu un nouveau commentaire sur votre image.");
            }
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

    if (isset($_POST["likeimage"]) && isset($_POST["imageid"])) {
        $image_id = $_POST["imageid"];

        if (!Like::isLikedByUser($current_user_id, $image_id)) {
            $liked = Like::likeImage($current_user_id, $image_id);
            if ($liked) {
                echo json_encode(["success" => "Image likée avec succès", "like_count" => Like::getLikeCount($image_id), "is_liked" => true]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Erreur lors du like de l'image"]);
            }
        } else {
            echo json_encode(["error" => "Vous avez déjà liké cette image"]);
        }
        exit;
    }

    if (isset($_POST["unlikeimage"]) && isset($_POST["imageid"])) {
        $image_id = $_POST["imageid"];

        if (Like::isLikedByUser($current_user_id, $image_id)) {
            $unliked = Like::unlikeImage($current_user_id, $image_id);
            if ($unliked) {
                echo json_encode(["success" => "Image unlikée avec succès", "like_count" => Like::getLikeCount($image_id), "is_liked" => false]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Erreur lors de l'unlike de l'image"]);
            }
        } else {
            echo json_encode(["error" => "Vous n'avez pas liké cette image"]);
        }
        exit;
    }

}

require_once dirname(__DIR__) . '/views/image.html';
?>