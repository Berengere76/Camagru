<?php
require_once dirname(__DIR__) . '/models/user.php';

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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if (isset($_POST['resetmdp'])) {
		$email = htmlspecialchars($_POST['email']);
		$email = trim($email);

		if (empty($email)) {
			$_SESSION['errors'] = "Veuillez entrer une adresse email.";
			header("Location: reset_password.php");
			exit;
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$_SESSION['errors'] = "Adresse email invalide";
			header("Location: reset_password.php");
			exit;
		}
		
		$user = User::getUserByEmail($email);

		if (!$user) {
			$_SESSION['errors'] = "Aucun compte associé à cette adresse email.";
			header("Location: login.php");
			exit;
		}

		if ($user["is_verified"] == 0) {
			$_SESSION['errors'] = "Votre compte n'est pas encore vérifié. Veuillez vérifier votre email.";
			header("Location: login.php");
			exit;
		}

		$verificationToken = User::generateResetToken($email);
		$resetLink = "http://localhost:8000/controllers/reset_password1.php?token=" . $verificationToken;
		sendMail($email, "Réinitialisation du mot de passe", "Cliquez sur ce lien pour réinitialiser votre mot de passe: " . $resetLink);
		$_SESSION['success'] = "Un lien de réinitialisation a été envoyé à votre adresse email.";
		header("Location: login.php");
		exit;
	}
}

require_once dirname(__DIR__) . '/views/reset_password.html';

?>