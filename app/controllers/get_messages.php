<?php
session_start();

header('Content-Type: application/json');

$response = [
    "errors" => $_SESSION["errors"] ?? null,
    "success" => $_SESSION["success"] ?? null,
];

unset($_SESSION["errors"], $_SESSION["success"]);

echo json_encode($response);
exit;
