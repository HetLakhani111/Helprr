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
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $role = $_POST['role'] ?? 'user';

        // Validate inputs
        if (empty($name) || empty($email) || empty($password)) {
            $errors[] = 'Name, email, and password are required.';
        } elseif (strlen($name) < 3) {
            $errors[] = 'Name must be at least 3 characters.';
        } elseif (!isValidEmail($email)) {
            $errors[] = 'Invalid email format.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        } elseif ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match.';
        } elseif (!in_array($role, ['user', 'shop'])) {
            $errors[] = 'Invalid role selected.';
        } else {
            // Check if email already exists
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email already registered.';
            } else {
                // Insert new user
                $password_hash = hashPassword($password);
                $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, phone, role) VALUES (?, ?, ?, ?, ?)');
                
                if ($stmt->execute([$name, $email, $password_hash, $phone, $role])) {
                    $user_id = $pdo->lastInsertId();

                    // If registering as shop, create service provider profile
                    if ($role === 'shop') {
                        $provider_stmt = $pdo->prepare('INSERT INTO service_providers (user_id, shop_name) VALUES (?, ?)');
                        $provider_stmt->execute([$user_id, $name]);
                    }

                    $success = 'Registration successful! <a href="login.php">Login here</a>.';
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
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
    <title>Register - Helprr</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; padding: 2rem 0; }
        .register-container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); max-width: 500px; width: 100%; }
        .register-container h1 { color: #667eea; margin-bottom: 1.5rem; text-align: center; }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
        .btn-register { background: #667eea; border: none; font-weight: 600; }
        .btn-register:hover { background: #764ba2; }
        .login-link { text-align: center; margin-top: 1rem; }
        .login-link a { color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>📝 Helprr Register</h1>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= sanitize($error) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required placeholder="John Doe">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="you@example.com">
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone (Optional)</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+91 9876543210">
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Register As</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="user">Customer</option>
                    <option value="shop">Service Provider / Technician</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••" minlength="8">
                <small class="text-muted">Minimum 8 characters</small>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-register btn-primary w-100">Register</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>
