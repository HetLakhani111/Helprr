# 🎯 HELPRR SETUP - VISUAL GUIDE

## 📊 Setup Flow Chart

```
START
  ↓
[1] Import Database
  ├─ Run: mysql -u root -p < database_schema.sql
  ├─ OR use phpMyAdmin
  └─ Result: 7 tables created ✓
  ↓
[2] Edit Configuration
  ├─ Open: config/db.php
  ├─ Update: DB_USER, DB_PASS
  └─ Save file ✓
  ↓
[3] Set Permissions
  ├─ Windows: Right-click uploads → Properties → Security
  ├─ Mac/Linux: chmod 777 uploads/
  └─ Result: Folder is writable ✓
  ↓
[4] Start Server
  ├─ Command: php -S localhost:8000
  ├─ Result: "Development Server running"
  └─ Listen: http://localhost:8000 ✓
  ↓
[5] Open Browser
  ├─ URL: http://localhost:8000/public/index.php
  ├─ See: Helprr homepage with 🔧
  └─ Success! ✓
  ↓
[6] Test Features
  ├─ Register account
  ├─ Login
  ├─ Browse services
  └─ Create test data ✓
  ↓
END - Application Running!
```

---

## 🚀 5-MINUTE SETUP SUMMARY

| Step | Action | Time | Status |
|------|--------|------|--------|
| 1 | Import database_schema.sql | 30 sec | ⏳ |
| 2 | Edit config/db.php credentials | 1 min | ⏳ |
| 3 | Set uploads folder permissions | 1 min | ⏳ |
| 4 | Run: `php -S localhost:8000` | 30 sec | ⏳ |
| 5 | Visit: http://localhost:8000/public/index.php | 1 min | ⏳ |
| **TOTAL** | | **5 min** | ✅ |

---

## 💻 COPY-PASTE COMMANDS

### Windows PowerShell

```powershell
# 1. Navigate to project
cd "E:\OpenCode\Projects\Helprr\Helprr-1"

# 2. Import database (when prompted, enter MySQL password)
mysql -u root -p < database_schema.sql

# 3. Start PHP server
php -S localhost:8000
```

Then open browser: `http://localhost:8000/public/index.php`

### Mac Terminal

```bash
# 1. Navigate to project
cd ~/path/to/helprr

# 2. Import database
mysql -u root -p < database_schema.sql

# 3. Set permissions
chmod 777 uploads/

# 4. Start PHP server
php -S localhost:8000
```

Then open browser: `http://localhost:8000/public/index.php`

### Linux Terminal

```bash
# 1. Navigate to project
cd ~/path/to/helprr

# 2. Import database
mysql -u root -p < database_schema.sql

# 3. Set permissions
sudo chmod 777 uploads/

# 4. Start PHP server
php -S localhost:8000
```

Then open browser: `http://localhost:8000/public/index.php`

---

## 🎯 DETAILED STEP-BY-STEP

### STEP 1️⃣: Import Database

**What it does**: Creates all 7 database tables

**Method 1: Command Line (Easiest)**
```bash
mysql -u root -p < database_schema.sql
# Enter password when prompted
# Wait for completion (no output = success)
```

**Method 2: phpMyAdmin (GUI)**
- Open: `http://localhost/phpmyadmin`
- Select "Import" tab
- Click "Choose File"
- Select: `database_schema.sql`
- Click "Go"

**Verify it worked:**
```bash
mysql -u root -p
> SHOW DATABASES;
> USE helprr;
> SHOW TABLES;
# Should show: users, service_providers, services_offered, reviews, etc.
> exit;
```

---

### STEP 2️⃣: Edit Configuration

**What it does**: Tells PHP how to connect to MySQL

**File to edit:** `E:\OpenCode\Projects\Helprr\Helprr-1\config\db.php`

**Before (default):**
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'helprr');
```

**After (example with password):**
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'mypassword123');   // Add your password here
define('DB_NAME', 'helprr');
```

**How to edit:**
1. Open with VS Code or Notepad
2. Find the DB_PASS line
3. Replace empty '' with your MySQL password
4. Save file (Ctrl+S)

---

### STEP 3️⃣: Set Permissions

**What it does**: Allows the app to save uploaded files

**Windows:**
1. Navigate to: `E:\OpenCode\Projects\Helprr\Helprr-1\uploads`
2. Right-click folder → Properties
3. Click Security tab
4. Click Edit
5. Select your username
6. Check: ✓ Full Control
7. Click Apply → OK

**Alternative - PowerShell (Run as Admin):**
```powershell
icacls "E:\OpenCode\Projects\Helprr\Helprr-1\uploads" /grant:r "%username%:F"
```

**Mac/Linux:**
```bash
chmod 777 ~/path/to/helprr/uploads
```

---

### STEP 4️⃣: Start Server

**What it does**: Launches PHP development server

**Command:**
```bash
php -S localhost:8000
```

**Expected output:**
```
Development Server running.
Listening on http://localhost:8000
Press Ctrl+C to quit.
```

**Keep this terminal open!** Don't close it while testing.

---

### STEP 5️⃣: Open in Browser

**What it does**: Loads the website

**URL:** `http://localhost:8000/public/index.php`

**You should see:**
- ✓ Purple gradient at top
- ✓ 🔧 Helprr logo
- ✓ Search bar
- ✓ Category buttons
- ✓ Navigation links

**If you see this, setup is complete! ✅**

---

## 🧪 QUICK TEST

Once site is running, test these features:

### Test 1: Registration
```
1. Click "Register" button
2. Select "Customer" role
3. Fill form:
   Name: Test User
   Email: test@example.com
   Password: Test@123
4. Click Register
5. See success message
```

### Test 2: Login
```
1. Click "Login" button
2. Enter email: test@example.com
3. Enter password: Test@123
4. Click Login
5. See homepage (logged in)
```

### Test 3: Logout
```
1. Click "Logout" in navbar
2. See login page
3. Redirected to home
```

### Test 4: Search
```
1. Select category (e.g., "Laptop")
2. See filtered results
3. Use search bar
4. See search results
```

---

## ⚡ AUTOMATED SETUP (Optional)

**Windows users**: Run the setup script
```bash
cd E:\OpenCode\Projects\Helprr\Helprr-1
setup.bat
```

**Mac/Linux users**: Run the setup script
```bash
cd ~/path/to/helprr
chmod +x setup.sh
./setup.sh
```

These scripts will:
- ✓ Check PHP installation
- ✓ Check MySQL installation
- ✓ Verify config file
- ✓ Check uploads folder
- ✓ Start PHP server automatically

---

## 🚨 TROUBLESHOOTING

### Problem: "mysql command not found"

**Windows Solution:**
```bash
# Use full path
"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -p < database_schema.sql
```

**Alternative:** Add MySQL to PATH
1. Windows Settings
2. Environment Variables
3. Add MySQL bin folder to PATH
4. Restart terminal

---

### Problem: "Database Connection Error"

**Solutions:**
1. Check MySQL is running (Services → MySQL)
2. Verify credentials in config/db.php
3. Test MySQL connection:
   ```bash
   mysql -u root -p
   SHOW DATABASES;
   exit;
   ```
4. Re-import database if needed

---

### Problem: "Cannot upload files"

**Solution:**
- Check uploads folder permissions
- Windows: See STEP 3 above
- Mac/Linux: `chmod 777 uploads/`

---

### Problem: "Page not found" (404)

**Solutions:**
1. Check exact URL: `http://localhost:8000/public/index.php`
2. Verify PHP server is running
3. Check terminal shows "Development Server running"
4. Try different port: `php -S localhost:9000`

---

### Problem: "Headers already sent"

**Solutions:**
1. Check no whitespace before `<?php`
2. Check no content after `?>`
3. Verify file encoding is UTF-8

---

## 📚 FILE LOCATIONS

| File | Purpose | Location |
|------|---------|----------|
| Database schema | Import this first | `database_schema.sql` |
| DB Config | Edit this | `config/db.php` |
| Homepage | Main page | `public/index.php` |
| Login | Login page | `auth/login.php` |
| Register | Register page | `auth/register.php` |
| Uploads | Writable folder | `uploads/` |
| Styling | CSS file | `assets/css/styles.css` |
| Scripts | JavaScript | `assets/js/main.js` |

---

## ✅ SETUP CHECKLIST

Before you're done:

- [ ] PHP installed (php --version works)
- [ ] MySQL installed (mysql --version works)
- [ ] database_schema.sql imported
- [ ] config/db.php edited with credentials
- [ ] uploads folder has write permissions
- [ ] PHP server running on port 8000
- [ ] Homepage loads at http://localhost:8000/public/index.php
- [ ] Can register new account
- [ ] Can login
- [ ] Can logout

**All checked? 🎉 You're ready to go!**

---

## 🎓 NEXT STEPS

1. **Explore the Code**
   - Open files in VS Code
   - Read comments
   - Understand structure

2. **Create Test Data**
   - Register accounts
   - Add services
   - Submit reviews

3. **Customize**
   - Change colors
   - Modify forms
   - Add features

4. **Deploy** (Later)
   - See README.md for deployment

---

## 🔗 USEFUL LINKS

- **PHP Official**: https://www.php.net/
- **MySQL Official**: https://www.mysql.com/
- **Helprr Docs**: See README.md
- **Bootstrap Docs**: https://getbootstrap.com/

---

## 💡 QUICK REFERENCE

```bash
# Start everything
cd E:\OpenCode\Projects\Helprr\Helprr-1
php -S localhost:8000

# Access website
http://localhost:8000/public/index.php

# Stop server
Ctrl+C

# Reset database
mysql -u root -p < database_schema.sql

# Check PHP version
php --version

# Check MySQL version
mysql --version
```

---

**Now you're ready to run Helprr! 🚀**

**Happy coding!** 💻
