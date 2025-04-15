<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/user.php';
require_once dirname(__DIR__) . '/models/image.php';

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["imageid"]) && isset($_GET["ajax"])) {
    header("Content-Type: application/json");
    $image = Image::getImageById($_GET["imageid"]);
    if (!$image) {
        http_response_code(404);
        echo json_encode(["error" => "Image non trouvée"]);
        exit;
    }

    echo json_encode($image);
    exit;
}

require_once dirname(__DIR__) . '/views/image.html';
?>
