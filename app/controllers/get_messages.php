<?php
session_start();

header('Content-Type: application/json');

$response = [
    "errors" => $_SESSION["errors"] ?? null
];

unset($_SESSION["errors"]);

echo json_encode($response);
exit;
