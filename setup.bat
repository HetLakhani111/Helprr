@echo off
REM Helprr - Interactive Windows Setup Script
REM This script automates the setup process

title Helprr Setup Script
color 0A

echo.
echo ╔════════════════════════════════════════════════════════════════════════╗
echo ║                                                                        ║
echo ║                  🔧 HELPRR - SETUP WIZARD                            ║
echo ║          Hyperlocal Electronics Repair & Service Marketplace          ║
echo ║                                                                        ║
echo ╚════════════════════════════════════════════════════════════════════════╝
echo.

REM Get the project directory
cd /d "%~dp0"
set PROJECT_DIR=%cd%
echo Project Directory: %PROJECT_DIR%
echo.

REM Check PHP
echo [1/5] Checking PHP Installation...
php --version >nul 2>&1
if %errorlevel% equ 0 (
    echo ✓ PHP is installed
    php --version
) else (
    echo ✗ PHP not found! Please install PHP 8.0+
    echo   Download from: https://www.php.net/downloads
    pause
    exit /b 1
)
echo.

REM Check MySQL
echo [2/5] Checking MySQL Installation...
mysql --version >nul 2>&1
if %errorlevel% equ 0 (
    echo ✓ MySQL is installed
    mysql --version
) else (
    echo ✗ MySQL not found! Please install MySQL 5.7+
    echo   Download from: https://dev.mysql.com/downloads/mysql/
    pause
    exit /b 1
)
echo.

REM Check database config
echo [3/5] Checking Database Configuration...
if exist "%PROJECT_DIR%\config\db.php" (
    echo ✓ config/db.php exists
    echo   Please verify credentials are correct:
    echo.
    type "%PROJECT_DIR%\config\db.php" | findstr "DB_"
    echo.
) else (
    echo ✗ config/db.php not found!
    pause
    exit /b 1
)
echo.

REM Check uploads folder
echo [4/5] Checking Uploads Folder...
if exist "%PROJECT_DIR%\uploads" (
    echo ✓ uploads folder exists
    echo   Note: Please ensure folder has write permissions
    echo   Right-click folder > Properties > Security > Edit Permissions
) else (
    echo ✗ uploads folder not found!
    pause
    exit /b 1
)
echo.

REM Ask to import database
echo [5/5] Database Import
echo.
echo Have you imported the database? (database_schema.sql)
echo If NOT, run this command:
echo.
echo   mysql -u root -p^< database_schema.sql
echo.
set /p DB_IMPORTED=Have you imported the database? (y/n): 
if /i not "%DB_IMPORTED%"=="y" (
    echo.
    echo Please import the database first, then run this script again.
    pause
    exit /b 0
)
echo.

REM Start PHP Server
echo.
echo ════════════════════════════════════════════════════════════════════════
echo                      SETUP COMPLETE! ✓
echo ════════════════════════════════════════════════════════════════════════
echo.
echo Starting PHP Development Server on http://localhost:8000
echo.
echo Press Ctrl+C to stop the server
echo.
pause

php -S localhost:8000

echo.
echo Server stopped.
pause
