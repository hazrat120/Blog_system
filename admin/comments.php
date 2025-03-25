<?php
include '../includes/config.php';
if ($_SESSION['role'] !== 'admin') {
  header("Location: ../index.php");
  exit();
}

// Fetch unapproved comments
$stmt = $pdo->query("
  SELECT comments.*, users.username, posts.title 
  FROM comments 
  JOIN users ON comments.user_id = users.id 
  JOIN posts ON comments.post_id = posts.id 
  WHERE approved = 0
");
$comments = $stmt->fetchAll();

// Approve/Delete actions
if (isset($_GET['action']) && isset($_GET['id'])) {
  $id = $_GET['id'];
  if ($_GET['action'] === 'approve') {
    $stmt = $pdo->prepare("UPDATE comments SET approved = 1 WHERE id = ?");
    $stmt->execute([$id]);
  } elseif ($_GET['action'] === 'delete') {
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$id]);
  }
  header("Location: comments.php");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Comments</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <h2>Pending Comments</h2>
    <?php foreach ($comments as $comment): ?>
      <div class="card mb-3">
        <div class="card-body">
          <p><?= $comment['content'] ?></p>
          <small>By <?= $comment['username'] ?> on post: <?= $comment['title'] ?></small>
          <div class="mt-2">
            <a href="comments.php?action=approve&id=<?= $comment['id'] ?>" class="btn btn-success btn-sm">Approve</a>
            <a href="comments.php?action=delete&id=<?= $comment['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</body>
</html>