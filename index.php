<?php include 'includes/config.php'; ?>

<?php include 'includes/header.php'; ?>

  <div class="container mt-5">
    <h1 class="">Latest Posts</h1>
    <?php
    $stmt = $pdo->query("
      SELECT posts.*, users.username, categories.name AS category_name 
      FROM posts 
      JOIN users ON posts.author_id = users.id 
      JOIN categories ON posts.category_id = categories.id 
      ORDER BY created_at DESC
    ");
    while ($post = $stmt->fetch()):
    ?>
      <div class="card mb-3">
        <div class="card-body">
          <h2><?= $post['title'] ?></h2>
          <p>By <?= $post['username'] ?> in <?= $post['category_name'] ?></p>
          <p><?= substr($post['content'], 0, 100) ?>...</p>
          <a href="post.php?id=<?= $post['id'] ?>" class="btn btn-primary">Read More</a>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</body>
</html>