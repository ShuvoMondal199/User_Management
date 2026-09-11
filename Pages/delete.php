<?php

require_once __DIR__ . "/../config/db.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: Auth/login.php");
    exit();
}

$id = (int) $_GET["id"];

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: ../index.php");
    exit();
}

echo "Error deleting user: " . $conn->error;
