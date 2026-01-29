# 🚀 HELPRR - QUICK START GUIDE

## 30-Second Setup

### 1. Import Database (30 sec)
```bash
mysql -u root -p < database_schema.sql
```

### 2. Edit Config (15 sec)
Open `config/db.php` and update:
```php
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

### 3. Run Server (10 sec)
```bash
php -S localhost:8000
```

### 4. Visit App
```
http://localhost:8000/public/index.php
```

---

## 📋 FILE CHECKLIST

```
✅ config/db.php              - Database connection
✅ config/security.php        - Security functions
✅ config/constants.php       - App configuration
✅ public/index.php           - Homepage & search
✅ public/view_shop.php       - Shop detail & reviews
✅ auth/login.php             - Login form
✅ auth/register.php          - Register form
✅ auth/logout.php            - Logout
✅ services/manage_services.php - Service CRUD
✅ services/edit_profile.php  - Edit shop profile
✅ admin/dashboard.php        - Admin panel
✅ assets/css/styles.css      - Styling
✅ assets/js/main.js          - Scripts
✅ database_schema.sql        - Database
✅ .htaccess                  - URL rewriting
✅ README.md                  - Documentation
✅ SETUP.md                   - Setup guide
```

---

## 🧪 TEST ACCOUNTS

### Create via Registration:
1. Register as "Customer"
2. Register as "Service Provider"
3. Create admin manually (see SETUP.md)

### Test These Flows:
- [ ] Register as customer
- [ ] Register as shop owner
- [ ] Login/logout
- [ ] Search by category
- [ ] Search by text
- [ ] View shop details
- [ ] Submit review
- [ ] Add service
- [ ] Edit service
- [ ] Delete service
- [ ] Admin approval

---

## ⚙️ COMMON COMMANDS

### MySQL
```bash
# Create database
mysql -u root -p
> CREATE DATABASE helprr;
> USE helprr;
> source database_schema.sql;

# Or import directly
mysql -u root -p helprr < database_schema.sql
```

### PHP Server
```bash
# Run from helprr directory
php -S localhost:8000

# Or specific port
php -S localhost:9000
```

### File Permissions (Linux/Mac)
```bash
chmod 777 uploads/
chmod 755 config/
chmod 755 public/
```

---

## 🔍 TROUBLESHOOTING

| Issue | Solution |
|-------|----------|
| Database connection error | Check config/db.php credentials |
| Session errors | Clear browser cookies |
| 404 errors | Enable mod_rewrite or use index.php directly |
| Upload errors | Check uploads folder permissions |
| CSRF token error | Refresh page and try again |
| Blank page | Check error log, enable display_errors |

---

## 📚 DOCUMENTATION

- **README.md** - Full project overview
- **SETUP.md** - Detailed installation
- **PROJECT_STATUS.md** - Implementation checklist
- **IMPLEMENTATION_COMPLETE.md** - Summary of features

---

## 🎯 KEY FEATURES

✅ Complete authentication system  
✅ Role-based access control  
✅ Dynamic search & filtering  
✅ Service management (CRUD)  
✅ Review & rating system  
✅ Admin approval workflow  
✅ Responsive design  
✅ Security: SQL injection, XSS, CSRF protection  
✅ Well-documented code  

---

## 🎓 EXAM POINTS

### Can Explain:
1. How password hashing works
2. Why SQL injection prevention matters
3. How CSRF tokens protect forms
4. How role-based access works
5. How dynamic search is implemented
6. How ratings are calculated
7. How file uploads are secured
8. How sessions persist across pages

### Code to Show:
- Prepared statements
- Password hashing
- CSRF token generation
- Role checking
- Database relationships

---

## ✨ NEXT STEPS

1. ✅ Import database
2. ✅ Update config/db.php
3. ✅ Set file permissions
4. ✅ Test all features
5. ⏳ Add email notifications (optional)
6. ⏳ Integrate payment (optional)
7. ⏳ Add geolocation (optional)

---

**Ready to go! Happy coding! 🎉**

For more details, see README.md or SETUP.md
