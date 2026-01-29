<?php
require '../config/db.php';
require '../config/security.php';

// Get shop ID
$shop_id = $_GET['id'] ?? 0;

// Fetch shop details
$stmt = $pdo->prepare('
    SELECT sp.*, u.name, u.email, u.phone, u.profile_image,
           COALESCE(AVG(r.rating), 0) as avg_rating,
           COUNT(r.id) as review_count
    FROM service_providers sp
    JOIN users u ON sp.user_id = u.id
    LEFT JOIN reviews r ON sp.id = r.provider_id
    WHERE sp.id = ? AND sp.approved = 1
    GROUP BY sp.id
');
$stmt->execute([$shop_id]);
$shop = $stmt->fetch();

if (!$shop) {
    header('Location: /public/index.php?error=shop_not_found');
    exit();
}

// Fetch services offered
$services_stmt = $pdo->prepare('SELECT * FROM services_offered WHERE provider_id = ?');
$services_stmt->execute([$shop_id]);
$services = $services_stmt->fetchAll();

// Fetch reviews
$reviews_stmt = $pdo->prepare('
    SELECT r.*, u.name, u.profile_image
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.provider_id = ?
    ORDER BY r.created_at DESC
    LIMIT 10
');
$reviews_stmt->execute([$shop_id]);
$reviews = $reviews_stmt->fetchAll();

// Handle review submission
$review_error = '';
$review_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isLoggedIn()) {
        $review_error = 'Please login to submit a review.';
    } elseif (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $review_error = 'Invalid request.';
    } else {
        $rating = (int)$_POST['rating'] ?? 0;
        $comment = trim($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            $review_error = 'Rating must be between 1 and 5.';
        } elseif (empty($comment)) {
            $review_error = 'Please write a comment.';
        } else {
            // Check if user already reviewed this provider
            $existing_stmt = $pdo->prepare('SELECT id FROM reviews WHERE user_id = ? AND provider_id = ?');
            $existing_stmt->execute([$_SESSION['user_id'], $shop_id]);
            
            if ($existing_stmt->fetch()) {
                $review_error = 'You have already reviewed this service provider.';
            } else {
                $review_stmt = $pdo->prepare('INSERT INTO reviews (user_id, provider_id, rating, comment) VALUES (?, ?, ?, ?)');
                if ($review_stmt->execute([$_SESSION['user_id'], $shop_id, $rating, $comment])) {
                    $review_success = 'Review submitted successfully!';
                    header('Refresh:2');
                } else {
                    $review_error = 'Failed to submit review. Please try again.';
                }
            }
        }
    }
}

// Decode categories
$categories = json_decode($shop['category_ids'], true) ?? [];
$category_names = [];
if (!empty($categories)) {
    $cat_stmt = $pdo->prepare('SELECT id, name FROM electronics_categories WHERE id IN (' . implode(',', $categories) . ')');
    $cat_stmt->execute();
    $category_names = $cat_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($shop['shop_name']) ?> - Helprr</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/public/index.php">🔧 Helprr</a>
            <a href="/public/index.php" class="btn btn-light btn-sm">← Back to Search</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row">
            <!-- Shop Details -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4" style="border: none; border-radius: 12px;">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-4">
                            <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-size: 3rem; margin-right: 2rem;">
                                🔧
                            </div>
                            <div>
                                <h1><?= sanitize($shop['shop_name']) ?></h1>
                                <p class="text-muted mb-2">
                                    <strong>Contact:</strong> <?= sanitize($shop['phone']) ?> | <?= sanitize($shop['email']) ?>
                                </p>
                                <p class="text-warning mb-2">
                                    ★ <?= number_format((float)$shop['avg_rating'], 1) ?>/5 
                                    <span class="badge bg-light text-dark"><?= (int)$shop['review_count'] ?> reviews</span>
                                </p>
                                <p>
                                    <strong>Experience:</strong> <?= (int)$shop['experience_years'] ?> years
                                </p>
                            </div>
                        </div>

                        <h5>About</h5>
                        <p><?= nl2br(sanitize($shop['description'])) ?></p>

                        <h5 class="mt-4">Categories</h5>
                        <div class="mb-3">
                            <?php foreach ($category_names as $id => $name): ?>
                                <span class="badge bg-primary"><?= sanitize($name) ?></span>
                            <?php endforeach; ?>
                        </div>

                        <h5 class="mt-4">Pricing</h5>
                        <p>
                            <strong>Rate Range:</strong> ₹<?= number_format((float)$shop['charge_range_min']) ?> - ₹<?= number_format((float)$shop['charge_range_max']) ?>
                            <?php if ($shop['negotiable']): ?>
                                <span class="badge bg-success">Negotiable</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- Services -->
                <div class="card shadow-sm mb-4" style="border: none; border-radius: 12px;">
                    <div class="card-header" style="background: #f8f9fa; border: none;">
                        <h5 class="card-title mb-0">Services Offered</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($services)): ?>
                            <p class="text-muted">No services listed yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Service</th>
                                            <th>Description</th>
                                            <th>Price</th>
                                            <th>Est. Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($services as $service): ?>
                                            <tr>
                                                <td><strong><?= sanitize($service['title']) ?></strong></td>
                                                <td><?= sanitize(substr($service['description'], 0, 50)) ?>...</td>
                                                <td>₹<?= number_format((float)$service['price']) ?></td>
                                                <td><?= sanitize($service['estimated_time']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="card shadow-sm" style="border: none; border-radius: 12px;">
                    <div class="card-header" style="background: #f8f9fa; border: none;">
                        <h5 class="card-title mb-0">Customer Reviews (<?= count($reviews) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($review_success): ?>
                            <div class="alert alert-success"><?= $review_success ?></div>
                        <?php endif; ?>

                        <?php if ($review_error): ?>
                            <div class="alert alert-danger"><?= $review_error ?></div>
                        <?php endif; ?>

                        <?php if (isLoggedIn() && getUserRole() !== 'shop'): ?>
                            <div class="card mb-4" style="background: #f8f9fa; border: 1px solid #dee2e6;">
                                <div class="card-body">
                                    <h6>Write a Review</h6>
                                    <form method="POST" action="">
                                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                        <input type="hidden" name="submit_review" value="1">

                                        <div class="mb-3">
                                            <label for="rating" class="form-label">Rating</label>
                                            <select class="form-select" id="rating" name="rating" required>
                                                <option value="">Select rating...</option>
                                                <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                                                <option value="4">⭐⭐⭐⭐ Good</option>
                                                <option value="3">⭐⭐⭐ Average</option>
                                                <option value="2">⭐⭐ Poor</option>
                                                <option value="1">⭐ Very Poor</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="comment" class="form-label">Your Review</label>
                                            <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Share your experience..." required></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Submit Review</button>
                                    </form>
                                </div>
                            </div>
                        <?php elseif (!isLoggedIn()): ?>
                            <div class="alert alert-info">
                                <a href="/auth/login.php">Login</a> to write a review.
                            </div>
                        <?php endif; ?>

                        <!-- Display Reviews -->
                        <div>
                            <?php if (empty($reviews)): ?>
                                <p class="text-muted">No reviews yet. Be the first to review!</p>
                            <?php else: ?>
                                <?php foreach ($reviews as $review): ?>
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong><?= sanitize($review['name']) ?></strong>
                                                <div class="text-warning" style="font-size: 0.9rem;">
                                                    <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?>
                                                </div>
                                            </div>
                                            <small class="text-muted"><?= date('d M Y', strtotime($review['created_at'])) ?></small>
                                        </div>
                                        <p class="mt-2"><?= nl2br(sanitize($review['comment'])) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4" style="border: none; border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="card-body">
                        <h5 class="card-title">Contact Service Provider</h5>
                        <p class="card-text">Get in touch for service inquiries and pricing negotiation.</p>
                        <button class="btn btn-light w-100 mb-2" data-bs-toggle="modal" data-bs-target="#contactModal">Send Message</button>
                        <a href="tel:<?= sanitize($shop['phone']) ?>" class="btn btn-outline-light w-100">Call Now</a>
                    </div>
                </div>

                <div class="card shadow-sm" style="border: none; border-radius: 12px;">
                    <div class="card-body">
                        <h6>Quick Info</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>Avg Rating:</strong><br/>
                                ★ <?= number_format((float)$shop['avg_rating'], 1) ?>/5
                            </li>
                            <li class="mb-2">
                                <strong>Experience:</strong><br/>
                                <?= (int)$shop['experience_years'] ?> years
                            </li>
                            <li>
                                <strong>Negotiable:</strong><br/>
                                <?= $shop['negotiable'] ? '✓ Yes' : '✗ No' ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Contact <?= sanitize($shop['shop_name']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if (!isLoggedIn()): ?>
                        <p class="alert alert-info">Please <a href="/auth/login.php">login</a> to send a message.</p>
                    <?php else: ?>
                        <form>
                            <div class="mb-3">
                                <label for="message" class="form-label">Your Message</label>
                                <textarea class="form-control" id="message" rows="5" placeholder="Describe your repair needs..."></textarea>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <?php if (isLoggedIn()): ?>
                        <button type="button" class="btn btn-primary">Send Message</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p>&copy; 2026 Helprr - Hyperlocal Electronics Repair Marketplace. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
