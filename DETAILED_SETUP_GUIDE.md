# 🚀 HELPRR - COMPLETE SETUP GUIDE

## ⚙️ Prerequisites

Before starting, make sure you have installed:

- **PHP 8.0+** - [Download](https://www.php.net/downloads)
- **MySQL 5.7+** - [Download](https://dev.mysql.com/downloads/mysql/)
- **Text Editor** - VS Code, Notepad++, or similar

Check if installed:
```bash
php --version
mysql --version
```

---

## 📋 STEP-BY-STEP SETUP (5 minutes)

### **STEP 1: Import Database (30 seconds)**

**Windows:**
```bash
# Option A: Using MySQL Command Prompt (Easiest)
mysql -u root -p < "E:\OpenCode\Projects\Helprr\Helprr-1\database_schema.sql"
# When prompted, enter your MySQL password

# Option B: If command not recognized, use full path
"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -p < "E:\OpenCode\Projects\Helprr\Helprr-1\database_schema.sql"
```

**Mac/Linux:**
```bash
mysql -u root -p < ~/path/to/helprr/database_schema.sql
# When prompted, enter your MySQL password
```

**Alternative: Using phpMyAdmin (GUI method)**
1. Open http://localhost/phpmyadmin
2. Click "Import" tab
3. Click "Choose File"
4. Select `database_schema.sql` from your Helprr folder
5. Click "Go"

✅ **What this does**: Creates all 7 tables with sample data

---

### **STEP 2: Edit Database Configuration (1 minute)**

Open this file: `E:\OpenCode\Projects\Helprr\Helprr-1\config\db.php`

**Current content:**
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'helprr');
```

**What to change:**
```php
define('DB_HOST', 'localhost');        // Leave as-is (usually correct)
define('DB_USER', 'root');              // Change if your MySQL user is different
define('DB_PASS', '');                  // Add password if you set one (e.g., 'password123')
define('DB_NAME', 'helprr');            // Leave as-is
```

**Save the file** (Ctrl+S)

**Example if you have a password:**
```php
define('DB_PASS', 'mypassword123');    // Replace with actual password
```

✅ **What this does**: Connects PHP to your MySQL database

---

### **STEP 3: Set File Permissions (1 minute)**

The `uploads` folder needs write permission for file uploads.

**Windows (Easy way):**
1. Navigate to: `E:\OpenCode\Projects\Helprr\Helprr-1\uploads`
2. Right-click the `uploads` folder
3. Select **Properties**
4. Click **Security** tab
5. Click **Edit**
6. Select your user name
7. Check **Full Control** or **Modify**
8. Click **Apply** → **OK**

**Windows (Command line - PowerShell):**
```powershell
# Run as Administrator
icacls "E:\OpenCode\Projects\Helprr\Helprr-1\uploads" /grant:r "%username%:F"
```

**Mac/Linux:**
```bash
chmod 777 ~/path/to/helprr/uploads
```

✅ **What this does**: Allows the application to save uploaded files

---

### **STEP 4: Run the Development Server (1 minute)**

Open PowerShell/Command Prompt and navigate to Helprr folder:

```bash
# Navigate to the project folder
cd E:\OpenCode\Projects\Helprr\Helprr-1

# Start PHP development server
php -S localhost:8000
```

**You should see:**
```
Development Server running.
Listening on http://localhost:8000
```

✅ **What this does**: Starts a local web server on port 8000

---

### **STEP 5: Open in Browser (30 seconds)**

Visit this URL in your web browser:

```
http://localhost:8000/public/index.php
```

**You should see:**
- 🔧 Helprr logo
- Search bar with categories
- Empty results (no shops yet - that's normal!)

✅ **What this does**: Loads the Helprr homepage

---

## 🧪 TEST THE SETUP

### Create Test Accounts

**1. Register as Customer**
1. Click "Register" button
2. Select "Customer" role
3. Fill form:
   - Name: John Doe
   - Email: customer@test.com
   - Password: Test@1234
4. Click Register
5. Click Login with your credentials

**2. Register as Service Provider**
1. Click Register again
2. Select "Service Provider/Technician" role
3. Fill form:
   - Name: Tech Repairs
   - Email: shop@test.com
   - Password: Shop@1234
4. Register

**3. Test Search**
1. From homepage, select a category (e.g., "Laptop")
2. Search for "repair"
3. (No results yet - need to add services)

✅ **What this tests**: Authentication system is working

---

## 📁 Understanding the Project Structure

```
E:\OpenCode\Projects\Helprr\Helprr-1\
│
├── config/
│   ├── db.php              ← EDIT THIS (Database connection)
│   ├── security.php        (Security functions)
│   └── constants.php       (Settings)
│
├── public/
│   ├── index.php           ← HOME PAGE
│   └── view_shop.php       (Shop detail)
│
├── auth/
│   ├── login.php           (Login page)
│   ├── register.php        (Register page)
│   └── logout.php          (Logout)
│
├── services/
│   ├── manage_services.php (Add/edit services)
│   └── edit_profile.php    (Edit shop profile)
│
├── admin/
│   └── dashboard.php       (Admin panel)
│
├── uploads/                ← WRITABLE FOLDER (for files)
│
├── assets/
│   ├── css/styles.css      (Styling)
│   └── js/main.js          (JavaScript)
│
├── database_schema.sql     ← IMPORT THIS
│
└── README.md               (Documentation)
```

---

## 🔍 TROUBLESHOOTING

### ❌ Error: "Database Connection Error"

**Problem**: Can't connect to MySQL

**Solution**:
1. Verify MySQL is running
   - Windows: Check Services (services.msc) for "MySQL"
   - Mac/Linux: `sudo systemctl status mysql`
2. Check credentials in `config/db.php`
3. Verify database was imported:
   ```bash
   mysql -u root -p
   > SHOW DATABASES;  # Should show "helprr"
   > exit;
   ```

---

### ❌ Error: "Headers already sent"

**Problem**: PHP error about headers

**Solution**:
1. Make sure no whitespace before `<?php` tag
2. Make sure no content after `?>` tag (remove it if present)
3. Check file encoding is UTF-8 without BOM

---

### ❌ Error: "Cannot GET /public/index.php"

**Problem**: Page not found

**Solution**:
1. Verify URL is: `http://localhost:8000/public/index.php`
2. Check PHP server is running (should show "Development Server running")
3. Try without the port: `http://localhost/public/index.php` (if using XAMPP)

---

### ❌ Error: "Permission denied" for uploads folder

**Problem**: Can't upload files

**Solution**:
- See STEP 3 above for permission fixes
- Windows: Right-click → Properties → Security
- Mac/Linux: `chmod 777 uploads/`

---

### ❌ Error: "mysql command not found"

**Problem**: MySQL not in system PATH

**Solution - Windows:**
```bash
# Use full path
"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -p
```

**Solution - Mac:**
```bash
/usr/local/mysql/bin/mysql -u root -p
```

---

## 🎯 DIFFERENT SETUP OPTIONS

### Option A: Using XAMPP (Recommended for Windows)

1. **Install XAMPP** from [apachefriends.org](https://www.apachefriends.org/)
2. **Start Apache & MySQL** from XAMPP Control Panel
3. **Copy Helprr folder** to `C:\xampp\htdocs\helprr`
4. **Import database** using phpMyAdmin (http://localhost/phpmyadmin)
5. **Edit** `config/db.php` with your credentials
6. **Visit**: `http://localhost/helprr/public/index.php`

✅ No need to run PHP server - Apache handles it

---

### Option B: Using PHP Built-in Server (What we're doing)

```bash
cd E:\OpenCode\Projects\Helprr\Helprr-1
php -S localhost:8000
# Visit: http://localhost:8000/public/index.php
```

✅ Simplest, no installation needed

---

### Option C: Using Docker (Advanced)

If you have Docker installed:

```bash
cd E:\OpenCode\Projects\Helprr\Helprr-1
docker-compose up -d
# Visit: http://localhost:8000
```

---

## ✅ VERIFICATION CHECKLIST

Before declaring setup complete:

- [ ] PHP version is 8.0+
- [ ] MySQL is running
- [ ] Database "helprr" exists
- [ ] 7 tables created (check phpMyAdmin)
- [ ] `config/db.php` is edited
- [ ] `uploads` folder is writable
- [ ] PHP server started with `php -S localhost:8000`
- [ ] Homepage loads at `http://localhost:8000/public/index.php`
- [ ] Can click "Register"
- [ ] Can log in with test account

---

## 🚀 RUNNING THE APPLICATION

### To Start (Every Time)

```bash
# 1. Make sure MySQL is running
# 2. Open PowerShell/Command Prompt
# 3. Navigate to Helprr folder
cd E:\OpenCode\Projects\Helprr\Helprr-1

# 4. Start PHP server
php -S localhost:8000

# 5. Open browser to
http://localhost:8000/public/index.php
```

### To Stop

- **PHP Server**: Press `Ctrl+C` in terminal
- **MySQL**: Use Control Panel or `net stop MySQL80` (Windows)

---

## 📊 TEST SCENARIOS

After setup works, try these to verify features:

### Scenario 1: Register & Login
- [ ] Register as customer
- [ ] Login with credentials
- [ ] See dashboard

### Scenario 2: Search & Browse
- [ ] Click category buttons
- [ ] Use search bar
- [ ] See results (if any shops exist)

### Scenario 3: Create Service (as Shop Owner)
- [ ] Register as "Service Provider"
- [ ] Login
- [ ] Go to "My Services"
- [ ] Add new service
- [ ] See service appears

### Scenario 4: Submit Review
- [ ] Login as customer
- [ ] View shop details
- [ ] Submit review with rating
- [ ] See review appears

### Scenario 5: Admin Functions
- [ ] Create admin account manually (see docs)
- [ ] Visit `/admin/dashboard.php`
- [ ] See pending shops to approve

---

## 📞 QUICK COMMANDS REFERENCE

```bash
# Check PHP version
php --version

# Check MySQL version
mysql --version

# Start PHP server
php -S localhost:8000

# Import database
mysql -u root -p < database_schema.sql

# Open MySQL console
mysql -u root -p

# List databases
SHOW DATABASES;

# Use helprr database
USE helprr;

# List tables
SHOW TABLES;

# Check table structure
DESCRIBE users;

# Stop PHP server
Ctrl+C
```

---

## 🎓 NEXT STEPS

After setup is working:

1. **Explore the Code**
   - Open files in VS Code
   - Read inline comments
   - Understand how features work

2. **Customize the Application**
   - Change colors in `assets/css/styles.css`
   - Add new fields to database
   - Modify forms

3. **Deploy to Production**
   - See `README.md` for deployment guide
   - Set up HTTPS
   - Configure proper database

4. **Add Features**
   - Email notifications
   - Payment integration
   - Geolocation search

---

## 📚 HELPFUL LINKS

- **PHP Docs**: https://www.php.net/docs.php
- **MySQL Docs**: https://dev.mysql.com/doc/
- **Bootstrap Docs**: https://getbootstrap.com/docs/5.0/
- **Helprr README**: See `README.md` in project

---

## ✨ YOU'RE READY!

Once you see the Helprr homepage with the 🔧 logo:

✅ **Setup is complete!**
✅ **Start exploring features**
✅ **Create test accounts**
✅ **Test all functionality**

---

## 💡 PRO TIPS

1. **Keep PHP server running** while testing
2. **Keep MySQL running** all the time
3. **Use Developer Tools** (F12) to debug
4. **Check browser console** for errors
5. **Check PHP error logs** if something breaks
6. **Read documentation** before asking questions

---

**Questions?** Check the documentation files or review the code comments!

**Happy coding! 🚀**
