<?php
require '../config/db.php';
require '../config/security.php';

requireLogout(); // Redirect if already logged in

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate inputs
        if (empty($email) || empty($password)) {
            $errors[] = 'Email and password are required.';
        } elseif (!isValidEmail($email)) {
            $errors[] = 'Invalid email format.';
        } else {
            // Check if user exists
            $stmt = $pdo->prepare('SELECT id, name, password_hash, role FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && verifyPassword($password, $user['password_hash'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['email'] = $email;

                // Handle "Remember Me"
                if (!empty($_POST['remember_me'])) {
                    setcookie('user_token', bin2hex(random_bytes(32)), time() + (30 * 24 * 60 * 60), '/');
                }

                $redirect = $_GET['redirect'] ?? '/public/index.php';
                header('Location: ' . $redirect);
                exit();
            } else {
                $errors[] = 'Invalid email or password.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Helprr</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; }
        .login-container { background: white; padding: 3rem; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); max-width: 400px; width: 100%; }
        .login-container h1 { color: #667eea; margin-bottom: 2rem; text-align: center; }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
        .btn-login { background: #667eea; border: none; font-weight: 600; }
        .btn-login:hover { background: #764ba2; }
        .register-link { text-align: center; margin-top: 1rem; }
        .register-link a { color: #667eea; text-decoration: none; }
        .register-link a:hover { text-decoration: underline; }
        .alert { margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>🔐 Helprr Login</h1>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= sanitize($error) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="you@example.com">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                <label class="form-check-label" for="remember_me">Remember me</label>
            </div>

            <button type="submit" class="btn btn-login btn-primary w-100">Login</button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>
