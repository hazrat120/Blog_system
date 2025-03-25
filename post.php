<?php include 'includes/config.php';
if (!isset($_GET['id'])) {
  header("Location: index.php");
  exit();
}

$post_id = $_GET['id'];
$stmt = $pdo->prepare("
  SELECT posts.*, users.username, categories.name AS category_name 
  FROM posts 
  JOIN users ON posts.author_id = users.id 
  JOIN categories ON posts.category_id = categories.id 
  WHERE posts.id = ?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

// Fetch approved comments
$stmt = $pdo->prepare("
  SELECT comments.*, users.username 
  FROM comments 
  JOIN users ON comments.user_id = users.id 
  WHERE post_id = ? AND approved = 1
");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

  <div class="container mt-5">
    <h1><?= $post['title'] ?></h1>
    <p>By <?= $post['username'] ?> in <?= $post['category_name'] ?></p>
    <p><?= nl2br($post['content']) ?></p>

    <h3>Comments</h3>
    <?php foreach ($comments as $comment): ?>
      <div class="card mb-3">
        <div class="card-body">
          <p><?= $comment['content'] ?></p>
          <small>By <?= $comment['username'] ?></small>
        </div>
      </div>
    <?php endforeach; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
      <form method="post">
        <div class="mb-3">
          <textarea name="comment" class="form-control" placeholder="Add a comment" required></textarea>
        </div>
        <button type="submit" name="submit_comment" class="btn btn-primary">Post Comment</button>
      </form>
      <?php
      if (isset($_POST['submit_comment'])) {
        $content = htmlspecialchars($_POST['comment']);
        $user_id = $_SESSION['user_id'];
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $user_id, $content]);
        header("Refresh:0");
      }
      ?>
    <?php else: ?>
      <p><a href="login.php">Login</a> to comment.</p>
    <?php endif; ?>
  </div>
</body>
</html>