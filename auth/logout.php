<?php
require '../config/db.php';
require '../config/security.php';

// Destroy session and logout user
if (isset($_SESSION['user_id'])) {
    // Delete session record from database (optional)
    $stmt = $pdo->prepare('DELETE FROM sessions WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
}

session_destroy();
setcookie('user_token', '', time() - 3600, '/');

// Redirect to homepage
header('Location: /public/index.php?message=logged_out');
exit();
?>
