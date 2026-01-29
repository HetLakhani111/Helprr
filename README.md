# 🔧 Helprr - Hyperlocal Electronics Repair & Service Marketplace

## Project Overview

**Helprr** is a full-stack web application that connects customers with certified electronics repair technicians and service providers in their local area. Built with **pure PHP**, **MySQL**, and **Bootstrap 5**, it demonstrates professional development practices including authentication, CRUD operations, role-based access control, and security best practices.

### Key Features
- ✅ User authentication & authorization (Role-based: User, Shop, Admin)
- ✅ Comprehensive CRUD operations
- ✅ Shop discovery with dynamic filtering
- ✅ Service management system
- ✅ Review & rating system
- ✅ Admin approval workflow
- ✅ Secure file uploads
- ✅ CSRF protection & XSS prevention
- ✅ Responsive design with Bootstrap 5
- ✅ SEO-friendly URL routing

---

## 🛠️ Tech Stack

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **Backend** | PHP 8.0+ | Server-side logic |
| **Database** | MySQL 5.7+ | Data persistence |
| **Frontend** | HTML5, CSS3, Bootstrap 5 | UI/UX |
| **JavaScript** | Vanilla JS | Client-side interactivity |
| **Security** | PDO, Password Hashing, Sessions | Secure operations |

---

## 📋 Installation & Setup

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache with `mod_rewrite` enabled
- Composer (optional, for future dependencies)

### Step 1: Clone/Download Project
```bash
cd your-projects-folder
git clone <repo-url> helprr
cd helprr
```

### Step 2: Create Database
1. Open **phpMyAdmin** or MySQL command line
2. Import the database schema:
   ```bash
   mysql -u root -p < database_schema.sql
   ```
3. Or manually copy and execute contents of `database_schema.sql`

### Step 3: Configure Database Connection
Edit `config/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'helprr');
```

### Step 4: Set Permissions
```bash
chmod 755 /path/to/helprr
chmod 777 /path/to/helprr/uploads
chmod 644 .htaccess
```

### Step 5: Start Apache Server
- Using XAMPP/WAMP: Start Apache from control panel
- Or use PHP built-in server:
  ```bash
  php -S localhost:8000
  ```

### Step 6: Access Application
- Homepage: `http://localhost/helprr/public/index.php`
- Register: `http://localhost/helprr/auth/register.php`
- Admin: `http://localhost/helprr/admin/dashboard.php` (after creating admin user)

---

## 📁 Project Structure

```
/helprr
├── /config
│   ├── db.php              # Database connection
│   └── security.php        # Security functions & CSRF tokens
├── /public
│   ├── index.php           # Homepage & search
│   ├── view_shop.php       # Shop detail page & reviews
│   └── search.php          # (Optional) Dedicated search page
├── /auth
│   ├── login.php           # Login form
│   ├── register.php        # Registration form
│   └── logout.php          # Logout handler
├── /services
│   ├── manage_services.php # Shop owner's service management
│   ├── create_service.php  # (Optional) Service creation
│   └── edit_profile.php    # (Optional) Shop profile editor
├── /admin
│   ├── dashboard.php       # Admin panel
│   ├── approve_shops.php   # (Optional) Shop approval page
│   └── manage_users.php    # (Optional) User management
├── /uploads
│   ├── certificates/       # Certification files
│   ├── images/             # Shop images
│   └── documents/          # Other documents
├── /assets
│   ├── css/
│   │   └── styles.css      # Global styles
│   └── js/
│       └── main.js         # Client-side scripts
├── database_schema.sql     # Database schema & sample data
├── .htaccess               # URL rewriting rules
├── README.md               # This file
└── SETUP.md                # Setup instructions
```

---

## 🗃️ Database Schema

### Users Table
- `id` - Primary key
- `name`, `email`, `password_hash` - Authentication
- `phone` - Contact info
- `role` - ENUM: user, shop, admin
- `profile_image` - User avatar
- `created_at`, `updated_at` - Timestamps

### Service Providers Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `shop_name`, `description` - Shop details
- `experience_years` - Professional experience
- `location`, `latitude`, `longitude` - Geo-location
- `negotiable` - Price negotiation flag
- `charge_range_min`, `charge_range_max` - Pricing info
- `certification_file` - Uploaded certifications
- `category_ids` - JSON array of category IDs
- `approved` - Admin approval status

### Services Offered Table
- `id` - Primary key
- `provider_id` - FK to service_providers
- `title`, `description` - Service details
- `price` - Service cost
- `estimated_time` - Completion estimate

### Reviews Table
- `id` - Primary key
- `user_id`, `provider_id` - Foreign keys
- `rating` - 1-5 star rating
- `comment` - User review text
- `created_at` - Review timestamp

### Other Tables
- `electronics_categories` - Repair categories (Laptop, TV, AC, etc.)
- `uploaded_files` - File upload tracking
- `sessions` - Session management

---

## 🔐 Security Features

### 1. **Authentication**
```php
// Password hashing using BCRYPT
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Password verification
password_verify($user_input, $stored_hash);
```

### 2. **SQL Injection Prevention**
```php
// Using prepared statements
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
```

### 3. **XSS Protection**
```php
// Sanitizing output
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
```

### 4. **CSRF Protection**
```php
// Generate token in forms
<input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

// Verify in request handlers
if (!verifyCSRFToken($_POST['csrf_token'])) { exit; }
```

### 5. **Session Management**
- HTTP-only cookies
- Secure session configuration
- IP address tracking
- Activity logging

### 6. **File Upload Validation**
- MIME type checking
- File size limits
- Extension whitelisting
- Sandbox storage directory

---

## 👥 User Roles & Workflows

### Customer Flow
1. Register as "Customer"
2. Login
3. Search for services by category or location
4. View shop details and reviews
5. Submit review after service
6. Request service via contact form

### Shop Owner Flow
1. Register as "Service Provider/Technician"
2. Complete shop profile
3. Submit for admin approval
4. Add services with pricing
5. View customer inquiries
6. Manage ratings and reviews

### Admin Flow
1. Access admin dashboard
2. Approve pending shop registrations
3. Monitor system statistics
4. Manage flagged content
5. View activity logs

---

## 🚀 Key Functionalities Implemented

### 1. Authentication System
- Secure registration with email validation
- Login with password verification
- "Remember Me" functionality with cookies
- Session-based role checking
- Logout with session cleanup

### 2. CRUD Operations
All implemented with prepared statements:
- **Users**: Create (register), Read (profile)
- **Shops**: Create (register), Read (profile), Update (edit), Delete (admin)
- **Services**: Create (add), Read (list), Update (edit), Delete (remove)
- **Reviews**: Create (submit), Read (display)

### 3. Search & Filtering
```php
// Dynamic category filtering
if ($category_id) {
    $sql .= ' AND JSON_CONTAINS(category_ids, ?)';
    $params[] = json_encode((int)$category_id);
}

// Text search
if ($search_query) {
    $sql .= ' AND (shop_name LIKE ? OR description LIKE ?)';
}
```

### 4. Rating & Review System
- 1-5 star rating scale
- Average rating calculation
- Review count display
- Duplicate review prevention
- Chronological sorting

### 5. File Upload Management
- Certificate uploads for providers
- Profile image uploads
- Validation & sanitization
- Organized directory structure

### 6. Admin Approval Workflow
- Pending provider list
- One-click approval
- Rejection with removal
- Status tracking

---

## 📊 Database Queries Examples

### Get shops with ratings
```sql
SELECT sp.*, AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
FROM service_providers sp
LEFT JOIN reviews r ON sp.id = r.provider_id
WHERE sp.approved = 1
GROUP BY sp.id
ORDER BY avg_rating DESC;
```

### Search by category
```sql
SELECT * FROM service_providers
WHERE JSON_CONTAINS(category_ids, JSON_ARRAY(?))
AND approved = 1;
```

### Get shop statistics
```sql
SELECT 
    COUNT(DISTINCT user_id) as total_users,
    COUNT(DISTINCT provider_id) as total_providers,
    COUNT(DISTINCT IF(approved=1, provider_id, NULL)) as approved_providers,
    COUNT(r.id) as total_reviews
FROM service_providers sp
LEFT JOIN reviews r ON sp.id = r.provider_id;
```

---

## 🎨 Frontend Architecture

### Bootstrap 5 Components Used
- Navbar with dropdowns
- Cards for listings
- Modal dialogs for contact
- Forms with validation
- Tables for data display
- Badges for status indicators
- Alerts for feedback messages

### Responsive Breakpoints
- Mobile: < 576px
- Tablet: 576px - 992px
- Desktop: > 992px

### Color Scheme
- Primary: #667eea (Purple)
- Secondary: #764ba2 (Dark Purple)
- Success: #28a745 (Green)
- Danger: #dc3545 (Red)
- Warning: #ffc107 (Yellow)

---

## 🧪 Testing the Application

### Test User Accounts
```
Regular User:
Email: customer@example.com
Password: TestUser123

Shop Owner:
Email: shop@example.com
Password: ShopTest123

Admin:
Email: admin@example.com
Password: AdminPass123
```

### Test Scenarios
1. **Registration**: Create new account with validation
2. **Login/Logout**: Test session management
3. **Search**: Filter by category and text search
4. **CRUD**: Add/Edit/Delete services
5. **Review**: Submit rating and comment
6. **Admin Panel**: Approve pending shops

---

## 📝 API Reference (Future Enhancement)

Future REST API endpoints:
```
POST   /api/auth/login        # Login
POST   /api/auth/register     # Register
POST   /api/auth/logout       # Logout

GET    /api/shops             # List shops
GET    /api/shops/{id}        # Shop details
POST   /api/shops             # Create shop
PUT    /api/shops/{id}        # Update shop
DELETE /api/shops/{id}        # Delete shop

GET    /api/reviews           # List reviews
POST   /api/reviews           # Submit review

GET    /api/search?q=...      # Search functionality
```

---

## 🐛 Debugging & Troubleshooting

### Common Issues

**Issue**: "Database Connection Error"
```php
// Solution: Check db.php configuration and MySQL service status
// Verify: DB_HOST, DB_USER, DB_PASS, DB_NAME values
```

**Issue**: "Headers already sent"
```php
// Solution: Ensure no whitespace before <?php and after ?>
// Check: BOM (Byte Order Mark) in file encoding
```

**Issue**: "Uploads directory permission denied"
```bash
# Solution:
chmod 777 /path/to/helprr/uploads
```

**Issue**: ".htaccess not working"
```
// Solution: Enable mod_rewrite
// Run: a2enmod rewrite && systemctl restart apache2
```

### Enable Error Logging
In `config/security.php`:
```php
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/helprr/logs/errors.log');
```

---

## 📚 Code Examples

### Creating a Service (CRUD Create)
```php
$stmt = $pdo->prepare('
    INSERT INTO services_offered 
    (provider_id, title, description, price, estimated_time) 
    VALUES (?, ?, ?, ?, ?)
');

$stmt->execute([
    $provider_id,
    $title,
    $description,
    $price,
    $estimated_time
]);
```

### Fetching Reviews with Ratings
```php
$stmt = $pdo->prepare('
    SELECT r.*, u.name, u.profile_image
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.provider_id = ?
    ORDER BY r.created_at DESC
');
$stmt->execute([$shop_id]);
$reviews = $stmt->fetchAll();
```

### Dynamic Search with Filtering
```php
$sql = 'SELECT * FROM service_providers WHERE approved = 1';
$params = [];

if ($category) {
    $sql .= ' AND JSON_CONTAINS(category_ids, ?)';
    $params[] = json_encode($category);
}

if ($search) {
    $sql .= ' AND (shop_name LIKE ? OR description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
```

---

## 🔄 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Jan 2026 | Initial release with core features |

---

## 📄 License

This project is created for educational purposes. All rights reserved © 2026 Helprr.

---

## 👨‍💻 Developer Notes

### Important Security Reminders
1. Always use prepared statements
2. Never store plain-text passwords
3. Validate ALL user inputs
4. Escape output with `htmlspecialchars()`
5. Use HTTPS in production
6. Implement rate limiting on login
7. Keep dependencies updated

### Performance Optimization Tips
1. Add database indexes on frequently queried columns
2. Implement caching for category listings
3. Use pagination for large result sets
4. Compress images before upload
5. Minify CSS and JS files

### Future Enhancement Ideas
1. Geolocation-based search (Google Maps API)
2. Real-time notifications
3. Payment integration (Razorpay/Stripe)
4. WhatsApp integration
5. Email notifications
6. Advanced analytics dashboard
7. Service booking calendar
8. Dispute resolution system

---

## 📞 Support & Contact

For issues or questions:
- Open an issue on GitHub
- Contact: support@helprr.local
- Documentation: See SETUP.md

---

**Built with ❤️ using PHP, MySQL, and Bootstrap**
