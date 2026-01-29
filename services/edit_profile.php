<?php
require '../config/db.php';
require '../config/security.php';

requireRole('shop');

$provider_id_stmt = $pdo->prepare('SELECT id, user_id FROM service_providers WHERE user_id = ?');
$provider_id_stmt->execute([$_SESSION['user_id']]);
$provider = $provider_id_stmt->fetch();
$provider_id = $provider['id'];

$message = '';
$error = '';

// Fetch current profile
$profile_stmt = $pdo->prepare('SELECT sp.*, u.name, u.email, u.phone FROM service_providers sp JOIN users u ON sp.user_id = u.id WHERE sp.id = ?');
$profile_stmt->execute([$provider_id]);
$profile = $profile_stmt->fetch();

// Fetch available categories
$categories = $pdo->query('SELECT * FROM electronics_categories')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $shop_name = trim($_POST['shop_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $experience_years = (int)($_POST['experience_years'] ?? 0);
        $location = trim($_POST['location'] ?? '');
        $charge_range_min = (float)($_POST['charge_range_min'] ?? 0);
        $charge_range_max = (float)($_POST['charge_range_max'] ?? 0);
        $negotiable = isset($_POST['negotiable']) ? 1 : 0;
        $selected_categories = $_POST['categories'] ?? [];

        // Validation
        if (empty($shop_name) || empty($description) || $experience_years < 0) {
            $error = 'Please fill all required fields correctly.';
        } elseif ($charge_range_min <= 0 || $charge_range_max <= 0 || $charge_range_min > $charge_range_max) {
            $error = 'Please enter valid price range.';
        } elseif (empty($selected_categories)) {
            $error = 'Please select at least one category.';
        } else {
            // Update profile
            $category_ids = json_encode(array_map('intval', $selected_categories));
            $stmt = $pdo->prepare('
                UPDATE service_providers 
                SET shop_name = ?, description = ?, experience_years = ?, location = ?,
                    charge_range_min = ?, charge_range_max = ?, negotiable = ?, category_ids = ?
                WHERE id = ?
            ');

            if ($stmt->execute([
                $shop_name, $description, $experience_years, $location,
                $charge_range_min, $charge_range_max, $negotiable, $category_ids, $provider_id
            ])) {
                $message = 'Profile updated successfully!';
                // Refresh profile
                $profile_stmt->execute([$provider_id]);
                $profile = $profile_stmt->fetch();
            } else {
                $error = 'Failed to update profile. Please try again.';
            }
        }
    }
}

// Decode selected categories
$selected_cats = json_decode($profile['category_ids'], true) ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Shop Profile - Helprr</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/public/index.php">🔧 Helprr</a>
            <div>
                <a href="/services/manage_services.php" class="btn btn-light btn-sm">← My Services</a>
                <a href="/auth/logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="mb-4">Edit Shop Profile</h1>

                <?php if ($message): ?>
                    <div class="alert alert-success"><?= $message ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                            <div class="mb-3">
                                <label for="shop_name" class="form-label">Shop Name</label>
                                <input type="text" class="form-control" id="shop_name" name="shop_name" value="<?= sanitize($profile['shop_name']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Shop Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required><?= sanitize($profile['description']) ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="experience_years" class="form-label">Experience (Years)</label>
                                    <input type="number" class="form-control" id="experience_years" name="experience_years" value="<?= (int)$profile['experience_years'] ?>" min="0" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location" placeholder="City/Area" value="<?= sanitize($profile['location']) ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="charge_range_min" class="form-label">Min Charge (₹)</label>
                                    <input type="number" class="form-control" id="charge_range_min" name="charge_range_min" step="0.01" value="<?= number_format((float)$profile['charge_range_min'], 2) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="charge_range_max" class="form-label">Max Charge (₹)</label>
                                    <input type="number" class="form-control" id="charge_range_max" name="charge_range_max" step="0.01" value="<?= number_format((float)$profile['charge_range_max'], 2) ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Service Categories</label>
                                <div>
                                    <?php foreach ($categories as $cat): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" id="cat_<?= $cat['id'] ?>" <?= in_array($cat['id'], $selected_cats) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="cat_<?= $cat['id'] ?>">
                                                <?= sanitize($cat['icon']) ?> <?= sanitize($cat['name']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="negotiable" name="negotiable" <?= $profile['negotiable'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="negotiable">
                                    Prices are negotiable
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
