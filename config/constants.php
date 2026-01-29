<?php
/**
 * Helprr - Configuration File
 * Edit this file to customize application settings
 */

// Application Settings
define('APP_NAME', 'Helprr');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // 'development' or 'production'
define('APP_DEBUG', true); // Set to false in production

// Site URLs
define('SITE_URL', 'http://localhost/helprr');
define('PUBLIC_URL', SITE_URL . '/public');
define('ADMIN_URL', SITE_URL . '/admin');

// File Upload Settings
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'application/pdf']);

// Email Configuration (for future use)
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your-email@gmail.com');
define('MAIL_PASSWORD', 'your-password');
define('MAIL_FROM', 'noreply@helprr.local');
define('MAIL_FROM_NAME', 'Helprr Support');

// Pagination
define('ITEMS_PER_PAGE', 10);

// Session Settings
define('SESSION_TIMEOUT', 1440); // 24 hours in minutes
define('REMEMBER_ME_DURATION', 30 * 24 * 60 * 60); // 30 days in seconds

// API Rate Limiting (future feature)
define('RATE_LIMIT_LOGIN', 5); // Max login attempts
define('RATE_LIMIT_WINDOW', 15 * 60); // 15 minutes

// Pagination for reviews
define('REVIEWS_PER_PAGE', 10);

// Default values
define('DEFAULT_RATING', 0);
define('MAX_RATING', 5);
define('MIN_RATING', 1);

?>
