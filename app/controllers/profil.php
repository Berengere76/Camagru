<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/user.php';
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}
$user = User::getUserById($_SESSION["user_id"]);
require_once dirname(__DIR__) . '/views/profil.php';
?>