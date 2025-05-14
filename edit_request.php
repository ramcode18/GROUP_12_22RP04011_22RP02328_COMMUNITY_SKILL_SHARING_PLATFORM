<?php
session_start();
require 'config/db.php';

if ($_SESSION['role'] !== 'user') exit;

$request_id = $_POST['request_id'] ?? $_GET['request_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_request'])) {
    $topic = $_POST['topic'];
    $description = $_POST['description'];
    $stmt = $pdo->prepare("UPDATE lesson_requests SET topic = ?, description = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$topic, $description, $request_id]);
    header("Location: dashboard_user.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM lesson_requests WHERE id = ?");
$stmt->execute([$request_id]);
$request = $stmt->fetch();
?>

<h2>Edit Lesson Request</h2>
<form method="POST">
    <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
    <input type="text" name="topic" value="<?= htmlspecialchars($request['topic']) ?>" required><br>
    <textarea name="description" required><?= htmlspecialchars($request['description']) ?></textarea><br>
    <button type="submit" name="update_request">Update</button>
</form>
