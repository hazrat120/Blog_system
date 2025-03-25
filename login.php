<?php
include 'includes/config.php';
include 'includes/header.php';

// Redirect logged-in users to post.php
if (isset($_SESSION['user_id'])) {
    header("Location: post.php");
    exit();
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<div class="container mt-5">
    <h2>Login</h2>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        
        <div class="mb-3 form-check">
            <input type="checkbox" name="remember_me" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Remember Me</label>
        </div>
        
        <button type="submit" name="login" class="btn btn-primary">Login</button>
        <a href="forgot_password.php" class="btn btn-link">Forgot Password?</a>
    </form>

    <?php
    if (isset($_POST['login'])) {
        // Verify CSRF token
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die("CSRF token validation failed");
        }

        // Validate inputs
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        // Basic validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo '<div class="alert alert-danger">Invalid email format</div>';
            exit();
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Regenerate session ID to prevent fixation
                session_regenerate_id(true);

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = time();

                // Remember me functionality
                if (!empty($_POST['remember_me'])) {
                    $selector = bin2hex(random_bytes(12));
                    $validator = bin2hex(random_bytes(32));
                    $hashed_validator = password_hash($validator, PASSWORD_DEFAULT);
                    $expires = date('Y-m-d\TH:i:s', time() + 86400 * 30); // 30 days

                    // Store in database
                    $stmt = $pdo->prepare("INSERT INTO auth_tokens (selector, hashed_validator, user_id, expires) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$selector, $hashed_validator, $user['id'], $expires]);

                    // Set cookie
                    setcookie('remember', $selector . ':' . $validator, [
                        'expires' => time() + 86400 * 30,
                        'path' => '/',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]);
                }

                header("Location: post.php");
                exit();
            } else {
                // Generic error message
                echo '<div class="alert alert-danger">Invalid email or password</div>';
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            echo '<div class="alert alert-danger">Login failed. Please try again later.</div>';
        }
    }
    ?>
</div>

<?php include 'includes/footer.php'; ?>