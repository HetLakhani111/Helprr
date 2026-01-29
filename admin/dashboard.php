<?php
require '../config/db.php';
require '../config/security.php';

requireRole('admin');

// Get statistics
$users_count = $pdo->query('SELECT COUNT(*) as count FROM users')->fetch()['count'];
$shops_count = $pdo->query('SELECT COUNT(*) as count FROM service_providers')->fetch()['count'];
$approved_shops = $pdo->query('SELECT COUNT(*) as count FROM service_providers WHERE approved = 1')->fetch()['count'];
$pending_shops = $shops_count - $approved_shops;
$reviews_count = $pdo->query('SELECT COUNT(*) as count FROM reviews')->fetch()['count'];

// Get pending shops for approval
$pending_stmt = $pdo->prepare('
    SELECT sp.id, sp.shop_name, u.name, u.email, sp.created_at
    FROM service_providers sp
    JOIN users u ON sp.user_id = u.id
    WHERE sp.approved = 0
    ORDER BY sp.created_at DESC
    LIMIT 10
');
$pending_stmt->execute();
$pending_shops_list = $pending_stmt->fetchAll();

// Handle approval
if (isset($_GET['approve'])) {
    $shop_id = (int)$_GET['approve'];
    $stmt = $pdo->prepare('UPDATE service_providers SET approved = 1 WHERE id = ?');
    if ($stmt->execute([$shop_id])) {
        $message = 'Shop approved successfully!';
        header('Refresh:1');
    }
}

// Handle rejection/deletion
if (isset($_GET['reject'])) {
    $shop_id = (int)$_GET['reject'];
    $stmt = $pdo->prepare('DELETE FROM service_providers WHERE id = ?');
    if ($stmt->execute([$shop_id])) {
        $message = 'Shop rejected.';
        header('Refresh:1');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Helprr</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/public/index.php">🔧 Helprr - Admin Panel</a>
            <a href="/auth/logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="mb-4">Admin Dashboard</h1>

        <!-- Statistics Cards -->
        <div class="row mb-5">
            <div class="col-md-3">
                <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total Users</h6>
                        <h2><?= $users_count ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h6 class="card-title">Approved Shops</h6>
                        <h2><?= $approved_shops ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <h6 class="card-title">Pending Approval</h6>
                        <h2><?= $pending_shops ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total Reviews</h6>
                        <h2><?= $reviews_count ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Shops for Approval -->
        <div class="card shadow-sm mb-5">
            <div class="card-header" style="background: #f8f9fa;">
                <h5 class="card-title mb-0">Pending Shop Approvals</h5>
            </div>
            <div class="card-body">
                <?php if (empty($pending_shops_list)): ?>
                    <p class="text-muted">No pending approvals.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Shop Name</th>
                                    <th>Owner</th>
                                    <th>Email</th>
                                    <th>Applied</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_shops_list as $shop): ?>
                                    <tr>
                                        <td><strong><?= sanitize($shop['shop_name']) ?></strong></td>
                                        <td><?= sanitize($shop['name']) ?></td>
                                        <td><?= sanitize($shop['email']) ?></td>
                                        <td><?= date('d M Y', strtotime($shop['created_at'])) ?></td>
                                        <td>
                                            <a href="?approve=<?= $shop['id'] ?>" class="btn btn-sm btn-success">Approve</a>
                                            <a href="?reject=<?= $shop['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject?')">Reject</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Users Management -->
        <div class="card shadow-sm">
            <div class="card-header" style="background: #f8f9fa;">
                <h5 class="card-title mb-0">System Information</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2"><strong>Total Registered Users:</strong> <?= $users_count ?></li>
                    <li class="mb-2"><strong>Service Providers:</strong> <?= $shops_count ?></li>
                    <li class="mb-2"><strong>Approved Providers:</strong> <?= $approved_shops ?></li>
                    <li class="mb-2"><strong>Customer Reviews:</strong> <?= $reviews_count ?></li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
