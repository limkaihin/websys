# MeowMart – Setup Guide

## 1. Import the database (Google Cloud SSH)
```bash
sudo mysql -u root -p < sql/meowmart.sql
```

## 2. Create the config file
```bash
sudo mkdir -p /var/www/private
sudo nano /var/www/private/db-config.ini
```
Paste this:
```ini
servername = "127.0.0.1"
username   = "root"
password   = "YOUR_MYSQL_PASSWORD"
dbname     = "meowmart"
```

## 3. Upload files to your web server
Place all files in `/var/www/html/` or your Apache web root.

## 4. File Structure
```
/
├── inc/
│   ├── head.inc.php       ← fonts + CSS link
│   ├── nav.inc.php        ← navbar (session-aware)
│   └── footer.inc.php     ← footer + JS link
├── account/
│   ├── login.php
│   ├── process_login.php
│   ├── register.php
│   ├── process_register.php
│   └── logout.php
├── shop/
│   └── products.php
├── assets/
│   ├── css/style.css
│   └── js/main.js
├── sql/
│   └── meowmart.sql
├── index.php
├── about.php
└── contact.php
```

## Demo Credentials
Register any account via `/account/register.php`
