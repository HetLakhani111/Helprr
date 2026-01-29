#!/bin/bash

# Helprr - Interactive Linux/Mac Setup Script
# This script automates the setup process

echo ""
echo "╔════════════════════════════════════════════════════════════════════════╗"
echo "║                                                                        ║"
echo "║                  🔧 HELPRR - SETUP WIZARD                            ║"
echo "║          Hyperlocal Electronics Repair & Service Marketplace          ║"
echo "║                                                                        ║"
echo "╚════════════════════════════════════════════════════════════════════════╝"
echo ""

# Get the project directory
PROJECT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
echo "Project Directory: $PROJECT_DIR"
echo ""

# Check PHP
echo "[1/5] Checking PHP Installation..."
if command -v php &> /dev/null; then
    echo "✓ PHP is installed"
    php --version | head -n 1
else
    echo "✗ PHP not found! Please install PHP 8.0+"
    echo "  macOS: brew install php"
    echo "  Linux: sudo apt-get install php"
    exit 1
fi
echo ""

# Check MySQL
echo "[2/5] Checking MySQL Installation..."
if command -v mysql &> /dev/null; then
    echo "✓ MySQL is installed"
    mysql --version
else
    echo "✗ MySQL not found! Please install MySQL 5.7+"
    echo "  macOS: brew install mysql"
    echo "  Linux: sudo apt-get install mysql-server"
    exit 1
fi
echo ""

# Check database config
echo "[3/5] Checking Database Configuration..."
if [ -f "$PROJECT_DIR/config/db.php" ]; then
    echo "✓ config/db.php exists"
    echo "  Please verify credentials are correct:"
    echo ""
    grep "define('DB_" "$PROJECT_DIR/config/db.php"
    echo ""
else
    echo "✗ config/db.php not found!"
    exit 1
fi
echo ""

# Check uploads folder
echo "[4/5] Checking Uploads Folder..."
if [ -d "$PROJECT_DIR/uploads" ]; then
    echo "✓ uploads folder exists"
    chmod 777 "$PROJECT_DIR/uploads"
    echo "✓ Set write permissions on uploads folder"
else
    echo "✗ uploads folder not found!"
    exit 1
fi
echo ""

# Ask to import database
echo "[5/5] Database Import"
echo ""
echo "Have you imported the database? (database_schema.sql)"
echo "If NOT, run this command:"
echo ""
echo "  mysql -u root -p < database_schema.sql"
echo ""
read -p "Have you imported the database? (y/n): " DB_IMPORTED

if [ "$DB_IMPORTED" != "y" ] && [ "$DB_IMPORTED" != "Y" ]; then
    echo ""
    echo "Please import the database first, then run this script again."
    exit 0
fi
echo ""

# Start PHP Server
echo ""
echo "════════════════════════════════════════════════════════════════════════"
echo "                      SETUP COMPLETE! ✓"
echo "════════════════════════════════════════════════════════════════════════"
echo ""
echo "Starting PHP Development Server on http://localhost:8000"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

cd "$PROJECT_DIR"
php -S localhost:8000

echo ""
echo "Server stopped."
