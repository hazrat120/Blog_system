<?php include 'includes/config.php'; ?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h2>Register</h2>
    <form method="post">
      <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
      </div>
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <button type="submit" name="register" class="btn btn-primary">Register</button>
    </form>

    <?php
    if (isset($_POST['register'])) {
      $username = htmlspecialchars($_POST['username']);
      $email = htmlspecialchars($_POST['email']);
      $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

      try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        echo "<div class='alert alert-success'>Registration successful! <a href='login.php'>Login here</a></div>";
      } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
      }
    }
    ?>
  </div>
</body>
</html>