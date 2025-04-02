<?php
require_once dirname(__DIR__) . '/models/user.php';
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}
require_once dirname(__DIR__) . '/views/camera.html';
?>