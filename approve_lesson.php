<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lesson_id'])) {
    $lesson_id = $_POST['lesson_id'];

    $stmt = $pdo->prepare("UPDATE lessons SET is_approved = 1 WHERE id = ?");
    $stmt->execute([$lesson_id]);
}

header("Location: dashboard_admin.php");
exit;
