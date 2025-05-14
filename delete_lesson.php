<?php
session_start();
require 'config/db.php';

if ($_SESSION['role'] != 'admin') exit;

$lesson_id = $_POST['lesson_id'];

$stmt = $pdo->prepare("DELETE FROM lessons WHERE id = ?");
$stmt->execute([$lesson_id]);

header("Location: dashboard_admin.php");
exit;
