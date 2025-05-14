<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lesson_id'])) {
    $lesson_id = $_POST['lesson_id'];

    // Fetch the lesson
    $stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = ? AND admin_id = ?");
    $stmt->execute([$lesson_id, $admin_id]);
    $lesson = $stmt->fetch();

    if (!$lesson) {
        echo "<p>Lesson not found or unauthorized.</p>";
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_lesson'])) {
    // Handle update
    $lesson_id = $_POST['lesson_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $pdo->prepare("UPDATE lessons SET title = ?, content = ? WHERE id = ? AND admin_id = ?");
    $stmt->execute([$title, $content, $lesson_id, $admin_id]);

    header("Location: dashboard_admin.php");
    exit;
} else {
    echo "<p>Invalid access.</p>";
    exit;
}
?>

<h2>Edit Lesson</h2>
<form method="POST">
    <input type="hidden" name="lesson_id" value="<?= $lesson['id'] ?>">
    <input type="text" name="title" value="<?= htmlspecialchars($lesson['title']) ?>" required><br>
    <textarea name="content" required><?= htmlspecialchars($lesson['content']) ?></textarea><br>
    <button type="submit" name="update_lesson">Update</button>
</form>
