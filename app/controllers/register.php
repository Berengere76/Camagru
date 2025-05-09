<?php
require_once dirname(__DIR__) . '/models/user.php';
session_start();

function sendVerificationEmail($email, $token)
{
    $to = $email;
    $subject = "Verification de votre compte Camagru";
    $verificationLink = "http://localhost:8000/verify.php?token=" . $token;
    $message = "Bonjour,\n\nMerci de vous être inscrit sur Camagru.\n\nVeuillez cliquer sur le lien ci-dessous pour vérifier votre adresse email :\n\n" . $verificationLink . "\n\nCordialement,\nL'équipe Camagru";
    $headers = "From: Camagru <lebasberengere@gmail.com>\r\n";

    if (mail($to, $subject, $message, $headers)) {
        return true;
    } else {
        error_log("Erreur lors de l'envoi de l'email avec la fonction mail()");
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["register"])) {
        $username = trim($_POST["username"]);
        $username = htmlspecialchars($username);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];

        if (empty($username) || empty($email) || empty($password)) {
            $_SESSION["errors"] = "Tous les champs sont obligatoires";
            header("Location: register.php");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["errors"] = "Adresse email invalide";
            header("Location: register.php");
            exit;
        }

        $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
        if (!preg_match($passwordRegex, $password)) {
            $_SESSION["errors"] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
            header("Location: register.php");
            exit;
        }

        try {
            $verificationToken = User::register($username, $email, $password);
            if ($verificationToken) {
                if (sendVerificationEmail($email, $verificationToken)) {
                    $_SESSION["success"] = "Inscription réussie. Un email de vérification vous a été envoyé.";
                    header("Location: login.php");
                    exit;
                } else {
                    User::deleteByEmail($email);
                    $_SESSION["errors"] = "Erreur lors de l'envoi de l'email de vérification. Veuillez réessayer.";
                    header("Location: register.php");
                    exit;
                }
            } else {
                $_SESSION["errors"] = "Erreur lors de l'enregistrement.";
                header("Location: register.php");
                exit;
            }
        } catch (Exception $e) {
            $_SESSION["errors"] = $e->getMessage();
            header("Location: register.php");
            exit;
        }
    }
}

require_once dirname(__DIR__) . '/views/register.html';

?>