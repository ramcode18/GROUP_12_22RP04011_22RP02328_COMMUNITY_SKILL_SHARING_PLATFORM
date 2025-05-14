<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) exit;

$lesson_id = $_POST['lesson_id'];
$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("INSERT INTO likes (lesson_id, user_id) VALUES (?, ?)");
    $stmt->execute([$lesson_id, $user_id]);
} catch (PDOException $e) {
    // Ignore duplicate like attempt due to unique constraint
}

header("Location: dashboard_user.php");
exit;
