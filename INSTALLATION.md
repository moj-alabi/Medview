# Medview+ Vulnerable HMS - Installation Guide

Copyright (c) 2026 MOJ for Sheflabs. All rights reserved.

---

## System Requirements

### Minimum Requirements:
- PHP 7.4 or higher
- MySQL 5.7 or MariaDB 10.3+
- Apache 2.4 or Nginx
- 512MB RAM minimum
- 500MB disk space

### Recommended:
- PHP 8.0+
- MySQL 8.0 or MariaDB 10.6+
- 2GB RAM
- 1GB disk space

---

## Installation on LAMP Server (Linux)

### Step 1: Install LAMP Stack

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install apache2 mysql-server php php-mysql php-mbstring php-xml
sudo systemctl start apache2
sudo systemctl start mysql
```

**CentOS/RHEL:**
```bash
sudo yum install httpd mariadb-server php php-mysqlnd php-mbstring php-xml
sudo systemctl start httpd
sudo systemctl start mariadb
```

### Step 2: Configure MySQL

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Login to MySQL
sudo mysql -u root -p

# Create database and user
CREATE DATABASE medview_vulnerable;
CREATE USER 'medview_user'@'localhost' IDENTIFIED BY 'your_password_here';
GRANT ALL PRIVILEGES ON medview_vulnerable.* TO 'medview_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Deploy Application

```bash
# Navigate to web root
cd /var/www/html

# Clone or copy the application
git clone https://github.com/yourusername/Medview.git
# OR
sudo cp -r /path/to/Medview .

# Set permissions
sudo chown -R www-data:www-data Medview
sudo chmod -R 755 Medview
```

### Step 4: Configure Database Connection

Edit `vulnerable/config-vulnerable.php`:

```php
<?php
$host = "localhost";
$user = "medview_user";
$password = "your_password_here";
$database = "medview_vulnerable";

$con = mysqli_connect($host, $user, $password, $database);

if(!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
```

### Step 5: Import Database

```bash
mysql -u medview_user -p medview_vulnerable < vulnerable/setup_database.sql
```

### Step 6: Access Application

Open browser and navigate to:
```
http://localhost/Medview/vulnerable/
```

Default credentials:
```
Username: ali@sheflabs.com
Password: Password123
```

---

## Installation on WAMP Server (Windows)

### Step 1: Install WAMP

1. Download WAMP from: https://www.wampserver.com/
2. Run installer (wampserver3.x.x_x64.exe)
3. Follow installation wizard
4. Install to: C:\wamp64 (default)
5. Start WAMP (green icon in system tray)

### Step 2: Configure MySQL

1. Click WAMP icon in system tray
2. Select "MySQL" > "MySQL Console"
3. Enter root password (blank by default)
4. Run SQL commands:

```sql
CREATE DATABASE medview_vulnerable;
CREATE USER 'medview_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON medview_vulnerable.* TO 'medview_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Deploy Application

1. Copy Medview folder to: `C:\wamp64\www\`
2. Your path should be: `C:\wamp64\www\Medview\`

### Step 4: Configure Database Connection

Edit `C:\wamp64\www\Medview\vulnerable\config-vulnerable.php`:

```php
<?php
$host = "localhost";
$user = "medview_user";
$password = "your_password";
$database = "medview_vulnerable";

$con = mysqli_connect($host, $user, $password, $database);

if(!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
```

### Step 5: Import Database

Method 1 - phpMyAdmin:
1. Open: http://localhost/phpmyadmin
2. Login with root (no password by default)
3. Select `medview_vulnerable` database
4. Click "Import" tab
5. Choose file: `C:\wamp64\www\Medview\vulnerable\setup_database.sql`
6. Click "Go"

Method 2 - Command Line:
```cmd
cd C:\wamp64\bin\mysql\mysql8.x.x\bin
mysql.exe -u medview_user -p medview_vulnerable < C:\wamp64\www\Medview\vulnerable\setup_database.sql
```

### Step 6: Access Application

Open browser and navigate to:
```
http://localhost/Medview/vulnerable/
```

Default credentials:
```
Username: ali@sheflabs.com
Password: Password123
```

---

## Installation on XAMPP (Windows/Mac/Linux)

### Step 1: Install XAMPP

**Windows:**
1. Download from: https://www.apachefriends.org/
2. Run installer
3. Select: Apache, MySQL, PHP, phpMyAdmin
4. Install to: C:\xampp (default)
5. Start Apache and MySQL from Control Panel

**Mac:**
1. Download Mac version
2. Open .dmg file
3. Drag XAMPP to Applications
4. Open XAMPP and start Apache, MySQL

**Linux:**
```bash
chmod +x xampp-linux-x64-installer.run
sudo ./xampp-linux-x64-installer.run
sudo /opt/lampp/lampp start
```

### Step 2: Configure MySQL

Open terminal/command prompt:

**Windows:**
```cmd
cd C:\xampp\mysql\bin
mysql.exe -u root -p
```

**Mac/Linux:**
```bash
/Applications/XAMPP/bin/mysql -u root -p
# OR
/opt/lampp/bin/mysql -u root -p
```

Run SQL:
```sql
CREATE DATABASE medview_vulnerable;
CREATE USER 'medview_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON medview_vulnerable.* TO 'medview_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Deploy Application

**Windows:**
- Copy to: `C:\xampp\htdocs\Medview\`

**Mac:**
- Copy to: `/Applications/XAMPP/htdocs/Medview/`

**Linux:**
- Copy to: `/opt/lampp/htdocs/Medview/`

### Step 4: Configure Database

Edit `config-vulnerable.php` with your credentials.

### Step 5: Import Database

Use phpMyAdmin:
```
http://localhost/phpmyadmin
```

Or command line as shown above.

### Step 6: Access Application

```
http://localhost/Medview/vulnerable/
```

---

## Post-Installation Configuration

### 1. Enable Error Reporting (Already Enabled)

The vulnerable version has errors enabled for training purposes.

### 2. Set Difficulty Level

In the application, select difficulty:
- Low: For beginners
- Medium: For intermediate
- Hard: For advanced
- Impossible: For experts

### 3. Configure Cloudflare (Optional)

For production training with real WAF:
1. Add domain to Cloudflare
2. Update nameservers
3. Configure security settings per difficulty
4. See: `vulnerable/CLOUDFLARE_SETUP.md`

---

## Troubleshooting

### Apache won't start

**Windows (WAMP/XAMPP):**
- Check port 80 is not in use
- Stop IIS if running: `net stop was /y`
- Stop Skype or change port

**Linux:**
```bash
sudo netstat -tulpn | grep :80
sudo systemctl stop apache2
sudo systemctl start apache2
```

### MySQL connection failed

1. Check MySQL is running:
```bash
# Linux
sudo systemctl status mysql

# Windows
Check WAMP/XAMPP control panel
```

2. Verify credentials in `config-vulnerable.php`
3. Test connection:
```bash
mysql -u medview_user -p medview_vulnerable
```

### Page not found (404)

1. Check application path
2. Verify Apache DocumentRoot
3. Check .htaccess files

**Linux:**
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Database import fails

1. Check SQL file path
2. Verify database exists
3. Check user permissions
4. Import manually via phpMyAdmin

### Permission denied errors

**Linux:**
```bash
sudo chown -R www-data:www-data /var/www/html/Medview
sudo chmod -R 755 /var/www/html/Medview
```

**Windows:**
- Right-click folder
- Properties > Security
- Give full control to Users group

---

## Security Notes for Training

### IMPORTANT WARNINGS:

1. This is a VULNERABLE application for TRAINING ONLY
2. NEVER deploy to production or public internet
3. Use in isolated lab environment only
4. Use strong firewall rules
5. Restrict to localhost or trusted network
6. No real patient data
7. This is intentionally insecure

### Recommended Network Setup:

**Option 1: Localhost Only**
```
Access: http://localhost/Medview/vulnerable/
Security: Only accessible from same machine
Best for: Individual learning
```

**Option 2: Local Network**
```
Access: http://192.168.x.x/Medview/vulnerable/
Security: Accessible from LAN only
Best for: Classroom training
Configure firewall to block external access
```

**Option 3: VPN Only**
```
Access: Through VPN only
Security: VPN required for access
Best for: Remote training
Use strong VPN with authentication
```

---

## Verification

After installation, verify:

1. Navigate to: `http://localhost/Medview/vulnerable/`
2. See login page
3. Login with: `ali@sheflabs.com` / `Password123`
4. See dashboard with balance: 1,000 NGN
5. Try SQL injection: `admin' OR '1'='1' --`
6. Capture first flag
7. Earn 500 NGN reward

If all above works, installation is successful.

---

## Updating

To update the application:

```bash
# Backup database first
mysqldump -u medview_user -p medview_vulnerable > backup.sql

# Pull updates
cd /var/www/html/Medview
git pull origin main

# Re-import database if schema changed
mysql -u medview_user -p medview_vulnerable < vulnerable/setup_database.sql
```

---

## Uninstallation

### Linux:
```bash
# Remove application
sudo rm -rf /var/www/html/Medview

# Drop database
mysql -u root -p
DROP DATABASE medview_vulnerable;
DROP USER 'medview_user'@'localhost';
EXIT;
```

### Windows:
1. Delete folder from htdocs/www
2. Use phpMyAdmin to drop database
3. Delete user in MySQL

---

## Support

For issues:
1. Check this guide first
2. Review troubleshooting section
3. Check Apache/MySQL logs
4. Verify PHP extensions installed

---

Copyright (c) 2026 MOJ for Sheflabs. All rights reserved.

For educational and training purposes only.
Unauthorized use on production systems is strictly prohibited.
