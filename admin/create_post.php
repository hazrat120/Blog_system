<?php
include '../includes/config.php';
// Restrict to admin
if ($_SESSION['role'] !== 'admin') {
  header("Location: ../index.php");
  exit();
}

// Fetch categories
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

if (isset($_POST['create_post'])) {
  $title = htmlspecialchars($_POST['title']);
  $content = htmlspecialchars($_POST['content']);
  $category_id = $_POST['category_id'];
  $author_id = $_SESSION['user_id'];

  $stmt = $pdo->prepare("INSERT INTO posts (title, content, author_id, category_id) VALUES (?, ?, ?, ?)");
  $stmt->execute([$title, $content, $author_id, $category_id]);
  header("Location: ../index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Create Post</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <h2>Create Post</h2>
    <form method="post">
      <div class="mb-3">
        <input type="text" name="title" class="form-control" placeholder="Title" required>
      </div>
      <div class="mb-3">
        <textarea name="content" class="form-control" rows="5" placeholder="Content" required></textarea>
      </div>
      <div class="mb-3">
        <select name="category_id" class="form-control" required>
          <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" name="create_post" class="btn btn-primary">Publish</button>
    </form>
  </div>
</body>
</html>