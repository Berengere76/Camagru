<?php
require_once dirname(__DIR__) . '/models/user.php';
session_start();
session_unset();
session_destroy();
header("Location : login.php");
exit;
?>