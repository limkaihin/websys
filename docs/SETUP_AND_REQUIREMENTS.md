# MeowMart — Full Setup Guide

## Open Source Projects Integrated

| # | Project | Use in MeowMart | Files |
|---|---------|-----------------|-------|
| 1 | **Chart.js v4.4** | Admin dashboard charts (revenue, orders, categories) | `admin/dashboard.php` |
| 2 | **PHPMailer v6.9** | Registration confirmation + contact form emails | `vendor/phpmailer/`, `includes/mail.php` |
| 3 | **HTML5 Boilerplate** | Meta tags, .htaccess, security headers, caching | `includes/header.php`, `.htaccess` |
| 4 | **Font Awesome Free 6.5** | All icons across navbar, pages, admin panel | `includes/header.php` (CDN) |
| 5 | **Zebra_Session v4.2** | Database-backed PHP session storage | `vendor/stefangabos/`, `includes/functions.php` |

---

## Prerequisites

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache with `mod_rewrite`, `mod_headers`, `mod_deflate` enabled
- LAMP stack (Linux/Apache/MySQL/PHP) on Google Cloud or local

---

## Step-by-Step Setup

### Step 1 — Upload the project files

Upload the entire `meowmart/` folder to your web server.  
For Google Cloud LAMP, place it at:
```
/var/www/html/meowmart/
```
So the site is accessible at `http://YOUR_SERVER_IP/meowmart/`

---

### Step 2 — Import the MySQL database

**Option A — Fresh install (recommended for a new server):**
```bash
mysql -u root -p < /var/www/html/meowmart/sql/meowmart.sql
```
This creates the `meowmart` database and all tables, then seeds demo data.

**Option B — Existing database (adds only the new tables):**
```bash
mysql -u root -p meowmart < /var/www/html/meowmart/sql/migrate.sql
```
Safe to run on an existing DB — uses `CREATE TABLE IF NOT EXISTS`.

**Tables created:**

| Table | Purpose |
|-------|---------|
| `users` | Member accounts |
| `products` | Product catalogue |
| `blog_posts` | Blog articles |
| `orders` | Customer orders |
| `order_items` | Line items per order |
| `contact_messages` | Contact form submissions |
| `session_data` | Zebra_Session DB sessions |

---

### Step 3 — Configure the application

```bash
cp /var/www/html/meowmart/config/config.sample.php \
   /var/www/html/meowmart/config/config.php
```

Then edit `config.php`:

```php
return [
    // Database
    'db_host' => '127.0.0.1',
    'db_name' => 'meowmart',
    'db_user' => 'root',
    'db_pass' => 'YOUR_MYSQL_PASSWORD',

    // Site URL — set this to your subfolder if needed
    'base_url' => '/meowmart',   // or '' if site is at root

    // Email (PHPMailer) — set mail_enabled to true and fill in SMTP details
    'mail_enabled'    => false,
    'mail_host'       => 'smtp.gmail.com',
    'mail_port'       => 587,
    'mail_username'   => 'your@gmail.com',
    'mail_password'   => 'your_app_password',  // Gmail: use App Password
    'mail_from'       => 'no-reply@meowmart.com.sg',
    'mail_from_name'  => 'MeowMart',
    'mail_encryption' => 'tls',

    // Session storage (Zebra_Session)
    // Set to true to store sessions in MySQL instead of files
    'session_db'            => false,
    'session_lifetime'      => 3600,
    'session_security_code' => 'change_this_to_a_long_random_string',
    'session_table'         => 'session_data',
];
```

---

### Step 4 — Set folder permissions

```bash
chmod 755 /var/www/html/meowmart
chmod 644 /var/www/html/meowmart/config/config.php
```

---

### Step 5 — Enable Apache modules

```bash
sudo a2enmod rewrite headers deflate expires
sudo systemctl restart apache2
```

---

### Step 6 — Verify the site

Open your browser and go to:
```
http://YOUR_SERVER_IP/meowmart/
```

Log in with the demo credentials:

| Role   | Email                  | Password   |
|--------|------------------------|------------|
| Admin  | admin@meowmart.test    | password   |
| Member | member@meowmart.test   | password   |

> **Note:** The SQL seeds use bcrypt hash of `"password"` for both accounts.  
> Change these immediately on a live server.

---

## Enabling PHPMailer (Email)

1. Set `'mail_enabled' => true` in `config.php`
2. Fill in your SMTP host, port, username and password
3. For Gmail, create an **App Password** (not your regular password):  
   Google Account → Security → 2-Step Verification → App Passwords
4. Test by registering a new account — you should receive a welcome email

---

## Enabling Zebra_Session (DB Sessions)

1. Set `'session_db' => true` in `config.php`
2. Change `'session_security_code'` to a long random string
3. The `session_data` table is already in `meowmart.sql` — no extra steps needed
4. Sessions will now be stored in the `session_data` table instead of files

---

## Troubleshooting

| Error | Fix |
|-------|-----|
| `Table 'meowmart.orders' doesn't exist` | Run `mysql -u root -p meowmart < sql/migrate.sql` |
| Blank page / white screen | Check PHP error log: `tail -f /var/log/apache2/error.log` |
| `Missing config/config.php` | Copy `config.sample.php` to `config.php` and fill in DB details |
| Images/CSS not loading | Set correct `base_url` in `config.php` (e.g. `'/meowmart'`) |
| `.htaccess` not working | Enable `mod_rewrite`: `sudo a2enmod rewrite && sudo service apache2 restart` |
| PHPMailer not sending | Check SMTP credentials; for Gmail use App Password not account password |
| Font Awesome icons not showing | Check internet connection — icons load from CDN |

