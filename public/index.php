<?php
require '../config/db.php';
require '../config/security.php';

// Get categories
$categories = $pdo->query('SELECT * FROM electronics_categories')->fetchAll();

// Get search parameters
$search_query = $_GET['search'] ?? '';
$category_id = $_GET['category'] ?? '';
$rating_filter = $_GET['rating'] ?? '';

// Build search query
$sql = 'SELECT sp.*, u.name, u.profile_image, 
                COALESCE(AVG(r.rating), 0) as avg_rating,
                COUNT(r.id) as review_count
        FROM service_providers sp
        JOIN users u ON sp.user_id = u.id
        LEFT JOIN reviews r ON sp.id = r.provider_id
        WHERE sp.approved = 1';

$params = [];

if (!empty($search_query)) {
    $sql .= ' AND (sp.shop_name LIKE ? OR sp.description LIKE ?)';
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

if (!empty($category_id)) {
    $sql .= ' AND JSON_CONTAINS(sp.category_ids, ?)';
    $params[] = json_encode((int)$category_id);
}

$sql .= ' GROUP BY sp.id ORDER BY avg_rating DESC LIMIT 50';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$providers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helprr - Find Electronics Repair Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="/public/index.php">🔧 Helprr</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <span class="nav-link">Welcome, <?= sanitize($_SESSION['username']) ?></span>
                        </li>
                        <?php if (getUserRole() === 'shop'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/services/manage_services.php">My Services</a>
                            </li>
                        <?php endif; ?>
                        <?php if (getUserRole() === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/dashboard.php">Admin Panel</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/auth/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/auth/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/auth/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4rem 0; text-align: center;">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Find Expert Electronics Repair Services Near You</h1>
            <p class="lead mb-4">Connect with certified technicians for laptop, TV, AC, and more repairs</p>
            
            <form method="GET" action="" class="search-form">
                <div class="row g-2">
                    <div class="col-md-8">
                        <input type="text" class="form-control form-control-lg" name="search" placeholder="Search by shop name or service..." value="<?= sanitize($search_query) ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-light btn-lg w-100">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Category Tabs -->
    <section class="py-4" style="background: #f8f9fa;">
        <div class="container">
            <h5 class="mb-3">Browse by Category</h5>
            <div class="d-flex flex-wrap gap-2">
                <a href="?category=" class="btn btn-outline-primary">All Categories</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="?category=<?= $cat['id'] ?>" class="btn btn-outline-primary">
                        <?= sanitize($cat['icon']) ?> <?= sanitize($cat['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <section class="py-5">
        <div class="container">
            <h3 class="mb-4">Available Service Providers</h3>

            <?php if (empty($providers)): ?>
                <div class="alert alert-info">
                    <p>No service providers found. <a href="?category=">Browse all categories</a></p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($providers as $provider): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm" style="border: none; border-radius: 12px;">
                                <div style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                    🔧
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?= sanitize($provider['shop_name']) ?></h5>
                                    <p class="card-text text-muted" style="font-size: 0.9rem;">
                                        <?= sanitize(substr($provider['description'], 0, 80)) ?>...
                                    </p>
                                    <div class="mb-2">
                                        <strong>Experience:</strong> <?= (int)$provider['experience_years'] ?> years
                                    </div>
                                    <div class="mb-2">
                                        <strong>Rate:</strong> ₹<?= number_format((float)$provider['charge_range_min']) ?> - ₹<?= number_format((float)$provider['charge_range_max']) ?>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Rating:</strong>
                                        <span class="text-warning">
                                            ★ <?= number_format((float)$provider['avg_rating'], 1) ?>/5
                                        </span>
                                        <span class="badge bg-light text-dark"><?= (int)$provider['review_count'] ?> reviews</span>
                                    </div>
                                    <?php if ($provider['negotiable']): ?>
                                        <span class="badge bg-success mb-3">Negotiable</span>
                                    <?php endif; ?>
                                    <a href="/public/view_shop.php?id=<?= $provider['id'] ?>" class="btn btn-primary w-100">View Profile</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p>&copy; 2026 Helprr - Hyperlocal Electronics Repair Marketplace. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
