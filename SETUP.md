# Helprr - Setup & Installation Guide

## Quick Start (5 Minutes)

### 1. Database Setup
```sql
-- Import using command line
mysql -u root -p < database_schema.sql

-- OR via phpMyAdmin:
-- 1. Go to http://localhost/phpmyadmin
-- 2. Click "Import"
-- 3. Select database_schema.sql
-- 4. Click "Go"
```

### 2. Configure Database
Edit `config/db.php`:
```php
define('DB_HOST', 'localhost');      // Your MySQL host
define('DB_USER', 'root');           // MySQL username
define('DB_PASS', 'your_password');  // MySQL password
define('DB_NAME', 'helprr');         // Database name
```

### 3. Set File Permissions
```bash
# Linux/Mac
chmod 755 helprr
chmod 777 helprr/uploads
chmod 755 .htaccess

# Windows: Right-click folder > Properties > Security > Edit Permissions
```

### 4. Run Application
**Option A: Using XAMPP/WAMP**
1. Place `helprr` folder in `htdocs` directory
2. Start Apache from control panel
3. Visit: `http://localhost/helprr/public/index.php`

**Option B: Using PHP Built-in Server**
```bash
cd helprr
php -S localhost:8000
# Visit: http://localhost:8000
```

---

## Complete Installation (Windows with XAMPP)

### Step 1: Download & Extract
1. Extract `helprr-master.zip` to `C:\xampp\htdocs\helprr`
2. Verify folder structure exists

### Step 2: Start XAMPP
1. Open XAMPP Control Panel
2. Click "Start" next to Apache
3. Click "Start" next to MySQL
4. Wait for status lights to turn green

### Step 3: Create Database
1. Open `http://localhost/phpmyadmin`
2. Click "New" to create new database
3. Database name: `helprr`
4. Click "Create"
5. Select `helprr` database
6. Click "Import" tab
7. Click "Choose File" → select `database_schema.sql`
8. Click "Go"

### Step 4: Update Configuration
1. Open `config/db.php` in text editor
2. Verify database credentials match your setup
3. Save file

### Step 5: Access Application
Visit: `http://localhost/helprr/public/index.php`

---

## Complete Installation (Linux/Mac with LAMP)

### Step 1: Install LAMP Stack
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install apache2 mysql-server php php-mysql
sudo systemctl start apache2
sudo systemctl start mysql

# Mac (using Homebrew)
brew install apache2 mysql php
```

### Step 2: Clone Project
```bash
cd /var/www/html
sudo git clone <repo-url> helprr
cd helprr
```

### Step 3: Set Permissions
```bash
sudo chmod 755 .
sudo chmod 777 uploads
sudo chown -R www-data:www-data .
```

### Step 4: Create Database
```bash
mysql -u root -p
> CREATE DATABASE helprr;
> USE helprr;
> source database_schema.sql;
> exit;
```

### Step 5: Enable mod_rewrite
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Step 6: Update Configuration
```bash
sudo nano config/db.php
# Update database credentials
# Press Ctrl+X, then Y, then Enter to save
```

### Step 7: Access Application
Visit: `http://localhost/helprr/public/index.php`

---

## Environment Variables (Optional)

Create `.env` file in root directory:
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=helprr
APP_ENV=development
APP_DEBUG=true
```

Update `config/db.php` to read from `.env`:
```php
require_once __DIR__ . '/../.env';
```

---

## Test Data

### Create Test User Accounts
```sql
-- Customer User
INSERT INTO users (name, email, password_hash, phone, role)
VALUES ('John Doe', 'customer@test.com', '$2y$12$...', '9876543210', 'user');

-- Shop Owner
INSERT INTO users (name, email, password_hash, phone, role)
VALUES ('Tech Repairs', 'shop@test.com', '$2y$12$...', '9876543211', 'shop');

-- Admin User
INSERT INTO users (name, email, password_hash, phone, role)
VALUES ('Admin', 'admin@test.com', '$2y$12$...', '9876543212', 'admin');
```

Or use registration form:
1. Go to Register page
2. Fill form with test data
3. Submit

---

## Directory Permissions

### Linux/Mac
```bash
# Readable by web server
chmod 755 config/
chmod 755 public/
chmod 755 auth/
chmod 755 services/
chmod 755 admin/

# Writable for uploads
chmod 777 uploads/
chmod 755 uploads/.htaccess

# Logs (if created)
mkdir -p logs
chmod 777 logs/
```

### Windows
1. Right-click `uploads` folder
2. Properties > Security
3. Edit > Select your user
4. Check "Modify" and "Write"
5. Apply

---

## PHP Configuration

### php.ini Settings
```ini
; Maximum file upload size
upload_max_filesize = 50M
post_max_size = 50M

; Session timeout
session.gc_maxlifetime = 1440

; Error reporting (development)
display_errors = On
error_reporting = E_ALL

; Error reporting (production)
display_errors = Off
log_errors = On
error_log = /path/to/logs/php-errors.log
```

---

## Apache Configuration

### Enable mod_rewrite (if .htaccess not working)

**Linux:**
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**Windows (XAMPP):**
1. Open `C:\xampp\apache\conf\httpd.conf`
2. Find: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Remove the `#` at beginning
4. Restart Apache

---

## Database Connection Test

Create `test_connection.php`:
```php
<?php
require 'config/db.php';

try {
    $result = $pdo->query('SELECT 1');
    echo "✓ Database connection successful!";
} catch (Exception $e) {
    echo "✗ Connection failed: " . $e->getMessage();
}
?>
```

Visit: `http://localhost/helprr/test_connection.php`

---

## Troubleshooting

### Issue: "Cannot GET /helprr/public/index.php"
**Solution**: Enable `mod_rewrite`
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Issue: "MySQL connection refused"
**Solution**: Check MySQL service
```bash
# Start MySQL
sudo systemctl start mysql

# Or restart
sudo systemctl restart mysql

# Check status
sudo systemctl status mysql
```

### Issue: "Permission denied" for uploads
**Solution**: Fix directory permissions
```bash
sudo chmod 777 uploads/
sudo chown -R www-data:www-data uploads/
```

### Issue: "CSRF token mismatch"
**Solution**: Clear browser cookies and try again
```
Press Ctrl+Shift+Delete > Clear browsing data
```

### Issue: "Password hashing not working"
**Solution**: Verify PHP bcrypt support
```php
<?php
echo function_exists('password_hash') ? 'Supported' : 'Not supported';
?>
```

### Issue: "Session not persisting"
**Solution**: Check session storage
```bash
# Ensure /tmp/php-sessions exists and is writable
sudo mkdir -p /tmp/php-sessions
sudo chmod 777 /tmp/php-sessions
```

---

## Security Checklist

Before deployment to production:

- [ ] Change default database credentials
- [ ] Update `APP_DEBUG` to `false`
- [ ] Enable HTTPS (.htaccess redirect)
- [ ] Set `display_errors` to `Off`
- [ ] Implement rate limiting on login
- [ ] Backup database regularly
- [ ] Monitor error logs
- [ ] Update all packages/libraries
- [ ] Configure firewalls
- [ ] Set up automated backups

---

## Production Deployment

### HTTPS Setup (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com
```

### Update .htaccess for HTTPS
```apache
# Redirect HTTP to HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Database Backup
```bash
mysqldump -u root -p helprr > backup_$(date +%Y%m%d).sql
```

### Automated Backup (Cron)
```bash
# Add to crontab
crontab -e

# Backup every night at 2 AM
0 2 * * * mysqldump -u root -p'password' helprr > /backups/helprr_$(date +\%Y\%m\%d).sql
```

---

## Development Tools

### Recommended IDE
- VS Code with PHP Intelephense extension
- PHPStorm (paid, more features)
- NetBeans (free)

### Debugging Tools
- VS Code Debugger with XDebug
- Browser DevTools (F12)
- phpMyAdmin for database inspection

### Testing Tools
- Postman (API testing)
- Selenium (automated testing)
- PHPUnit (unit testing)

---

## Getting Help

1. Check `README.md` for detailed documentation
2. Review error logs in `/logs` directory
3. Check browser console (F12) for JavaScript errors
4. Test database connection with `test_connection.php`
5. Verify file permissions with `ls -la`
6. Check Apache error log: `/var/log/apache2/error.log`

---

## Next Steps

After setup:
1. Create test user accounts via registration
2. Explore all features as different user roles
3. Review code in each module
4. Customize branding (colors, logo)
5. Set up custom domain
6. Configure email notifications
7. Implement payment gateway
8. Set up monitoring & alerts

---

**Happy coding! 🚀**
