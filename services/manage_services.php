<?php
require '../config/db.php';
require '../config/security.php';

requireRole('shop');

$provider_id_stmt = $pdo->prepare('SELECT id FROM service_providers WHERE user_id = ?');
$provider_id_stmt->execute([$_SESSION['user_id']]);
$provider = $provider_id_stmt->fetch();
$provider_id = $provider['id'];

$message = '';
$error = '';

// Handle service creation/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $service_id = $_POST['service_id'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)$_POST['price'] ?? 0;
        $estimated_time = trim($_POST['estimated_time'] ?? '');

        if (empty($title) || empty($description) || $price <= 0) {
            $error = 'All fields are required and price must be greater than 0.';
        } else {
            if (!empty($service_id)) {
                // Update service
                $stmt = $pdo->prepare('UPDATE services_offered SET title = ?, description = ?, price = ?, estimated_time = ? WHERE id = ? AND provider_id = ?');
                if ($stmt->execute([$title, $description, $price, $estimated_time, $service_id, $provider_id])) {
                    $message = 'Service updated successfully!';
                } else {
                    $error = 'Failed to update service.';
                }
            } else {
                // Create new service
                $stmt = $pdo->prepare('INSERT INTO services_offered (provider_id, title, description, price, estimated_time) VALUES (?, ?, ?, ?, ?)');
                if ($stmt->execute([$provider_id, $title, $description, $price, $estimated_time])) {
                    $message = 'Service created successfully!';
                } else {
                    $error = 'Failed to create service.';
                }
            }
        }
    }
}

// Handle service deletion
if (isset($_GET['delete'])) {
    $service_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM services_offered WHERE id = ? AND provider_id = ?');
    if ($stmt->execute([$service_id, $provider_id])) {
        $message = 'Service deleted successfully!';
    } else {
        $error = 'Failed to delete service.';
    }
}

// Fetch all services for this provider
$services_stmt = $pdo->prepare('SELECT * FROM services_offered WHERE provider_id = ? ORDER BY created_at DESC');
$services_stmt->execute([$provider_id]);
$services = $services_stmt->fetchAll();

// Fetch provider details
$provider_stmt = $pdo->prepare('SELECT * FROM service_providers WHERE id = ?');
$provider_stmt->execute([$provider_id]);
$provider_data = $provider_stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Helprr</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/public/index.php">🔧 Helprr</a>
            <div>
                <a href="/public/index.php" class="btn btn-light btn-sm">← Back Home</a>
                <a href="/auth/logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="mb-4">Manage Your Services</h1>

                <?php if ($message): ?>
                    <div class="alert alert-success"><?= $message ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <!-- Services List -->
                <div class="card mb-5 shadow-sm">
                    <div class="card-header" style="background: #f8f9fa;">
                        <h5 class="card-title mb-0">Your Services (<?= count($services) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($services)): ?>
                            <p class="text-muted">You haven't added any services yet. Create one below!</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Service</th>
                                            <th>Price</th>
                                            <th>Est. Time</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($services as $service): ?>
                                            <tr>
                                                <td><strong><?= sanitize($service['title']) ?></strong></td>
                                                <td>₹<?= number_format((float)$service['price']) ?></td>
                                                <td><?= sanitize($service['estimated_time']) ?></td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-warning" onclick="editService(<?= $service['id'] ?>)">Edit</a>
                                                    <a href="?delete=<?= $service['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Add/Edit Service Form -->
                <div class="card shadow-sm">
                    <div class="card-header" style="background: #667eea; color: white;">
                        <h5 class="card-title mb-0">Add New Service</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            <input type="hidden" name="service_id" value="">

                            <div class="mb-3">
                                <label for="title" class="form-label">Service Title</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="e.g., Screen Replacement" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Describe the service..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Price (₹)</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" placeholder="1000" required>
                            </div>

                            <div class="mb-3">
                                <label for="estimated_time" class="form-label">Estimated Time</label>
                                <input type="text" class="form-control" id="estimated_time" name="estimated_time" placeholder="e.g., 2-3 hours">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Add Service</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5>Your Shop Profile</h5>
                        <p class="text-muted"><?= sanitize($provider_data['shop_name']) ?></p>
                        <p class="small">
                            <strong>Status:</strong> 
                            <?= $provider_data['approved'] ? '<span class="badge bg-success">Approved</span>' : '<span class="badge bg-warning">Pending Approval</span>' ?>
                        </p>
                        <a href="/services/edit_profile.php" class="btn btn-sm btn-primary w-100">Edit Profile</a>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6>Quick Stats</h6>
                        <p class="small mb-2">
                            <strong>Services:</strong> <?= count($services) ?>
                        </p>
                        <p class="small mb-2">
                            <strong>Experience:</strong> <?= (int)$provider_data['experience_years'] ?> years
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editService(serviceId) {
            // TODO: Implement edit functionality
            alert('Edit functionality coming soon!');
        }
    </script>
</body>
</html>
