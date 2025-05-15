<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/user.php';
require_once dirname(__DIR__) . '/models/image.php';

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['info_profil'])) {
    header("Content-Type: application/json");

    $user = User::getUserById($_SESSION["user_id"]);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["error" => "Utilisateur non trouvé"]);
        exit;
    }

    echo json_encode($user);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['imageid'])) {
    header("Content-Type: application/json");

    $images = Image::getImagesByUserId($_SESSION["user_id"]);

    if (!$images) {
        http_response_code(404);
        echo json_encode(["error" => "Aucune image trouvée"]);
        exit;
    }

    echo json_encode($images);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (is_array($data)) {
        $_POST = $data;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    header("Content-Type: application/json");

    $imageId = $_POST['image_id'] ?? null;
    $userId = $_SESSION["user_id"];

    if (!$imageId) {
        echo json_encode(["error" => "ID d'image non spécifié"]);
        exit;
    }

    if (Image::deleteImage($userId, $imageId)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Échec de la suppression"]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updatePassword'])) {
    $user_id = $_SESSION["user_id"];
    $password = htmlspecialchars($_POST['password']);
    $new_password = htmlspecialchars($_POST['new_password']);

    $user = User::getUserById($user_id);

    $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
    if (!preg_match($passwordRegex, $new_password)) {
        $_SESSION["errors"] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
        header("Location: profil.php");
        exit;
    }

    if (empty($password) || empty($new_password)) {
        $_SESSION["errors"] = "Tous les champs sont obligatoires";
        header("Location: profil.php");
        exit;
    }

    if (password_verify($password, $user['password'])) {
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        try {
            User::updatePassword($user_id, $hashed_new_password);
            $_SESSION["success"] = "Modification réussie";
        } catch (Exception $e) {
            $_SESSION["errors"] = $e->getMessage();
        }
    } else {
        $_SESSION["errors"] = "Le mot de passe actuel est incorrect";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateUsername'])) {
    $user_id = $_SESSION["user_id"];
    $username = htmlspecialchars($_POST['username']);
    $username = trim($username);

    $user = User::getUserById($user_id);

    if (empty($username)) {
        $_SESSION["errors"] = "Le nom d'utilisateur ne peut pas être vide";
        header("Location: profil.php");
        exit;
    }

    try {
        User::updateUsername($user_id, $username);
        $_SESSION["username"] = $username;
        $_SESSION["success"] = "Modification réussie";
    } catch (Exception $e) {
        $_SESSION["errors"] = $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateEmail'])) {
    $user_id = $_SESSION["user_id"];
    $email = htmlspecialchars($_POST['email']);
    $email = trim($email);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["errors"] = "Adresse email invalide";
        header("Location: profil.php");
        exit;
    }

    if (empty($email)) {
        $_SESSION["errors"] = "Tous les champs sont obligatoires";
        header("Location: profil.php");
        exit;
    }

    try {
        User::updateEmail($user_id, $email);
        $_SESSION["email"] = $email;
        $_SESSION["success"] = "Modification réussie";
    } catch (Exception $e) {
        $_SESSION["errors"] = $e->getMessage();
    }
}

require_once dirname(__DIR__) . '/views/profil.html';
?>