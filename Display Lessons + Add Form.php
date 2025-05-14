<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];

// Handle add lesson
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_lesson'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $pdo->prepare("INSERT INTO lessons (admin_id, title, content) VALUES (?, ?, ?)");
    $stmt->execute([$admin_id, $title, $content]);
    echo "<p>Lesson added successfully.</p>";
}

// Fetch all lessons created by this admin
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE admin_id = ?");
$stmt->execute([$admin_id]);
$lessons = $stmt->fetchAll();
?>

<h2>Admin Dashboard</h2>
<a href="logout.php">Logout</a>

<h3>Add Lesson</h3>
<form method="POST">
    <input type="text" name="title" placeholder="Lesson Title" required><br>
    <textarea name="content" placeholder="Lesson Content" required></textarea><br>
    <button type="submit" name="add_lesson">Add Lesson</button>
</form>

<h3>Your Lessons</h3>
<?php foreach ($lessons as $lesson): ?>
    <div style="border:1px solid #ccc; padding:10px; margin:10px;">
        <h4><?= htmlspecialchars($lesson['title']) ?></h4>
        <p><?= nl2br(htmlspecialchars($lesson['content'])) ?></p>
        <form method="POST" action="edit_lesson.php">
            <input type="hidden" name="lesson_id" value="<?= $lesson['id'] ?>">
            <button type="submit">Edit</button>
        </form>
        <form method="POST" action="delete_lesson.php" onsubmit="return confirm('Are you sure?');">
            <input type="hidden" name="lesson_id" value="<?= $lesson['id'] ?>">
            <button type="submit">Delete</button>
        </form>
    </div>
<?php endforeach; ?>
