# MeowMart — MySQL Database Setup & Linking Guide

## Prerequisites
- MySQL 5.7+ or MariaDB 10.3+ installed
- PHP 7.4+ with PDO and pdo_mysql extensions enabled
- Apache with mod_rewrite enabled

---

## Step 1 — Create the Database (Fresh Import)

Open a terminal / command prompt and run:

```bash
mysql -u root -p < sql/meowmart.sql
```

Enter your MySQL root password when prompted.

**What this creates:**

| Table | Purpose |
|-------|---------|
| `users` | Member accounts (bcrypt passwords) |
| `products` | Product catalogue (15 seeded items) |
| `blog_posts` | Blog articles (3 seeded posts) |
| `orders` | Customer orders |
| `order_items` | Line items per order |
| `contact_messages` | Contact form submissions |
| `session_data` | Database-backed sessions (Zebra_Session) |

---

## Step 2 — Link the Application to the Database

Copy the sample config and fill in your credentials:

```bash
cp config/config.sample.php config/config.php
```

Then open `config/config.php` and edit these lines:

```php
'db_host' => '127.0.0.1',   // usually 127.0.0.1 or localhost
'db_name' => 'meowmart',    // the database name (created by meowmart.sql)
'db_user' => 'root',        // your MySQL username
'db_pass' => 'YOUR_PASSWORD_HERE',  // your MySQL password
'base_url' => '/meowmart',  // path from web root, e.g. '/meowmart' or ''
```

---

## Step 3 — Set the Correct base_url

`base_url` tells PHP where the site lives relative to the web root.

| Server setup | base_url value |
|---|---|
| Site at `http://localhost/meowmart/` | `'/meowmart'` |
| Site at `http://localhost/` (root) | `''` |
| Site at `http://192.168.1.10/meowmart/` | `'/meowmart'` |
| Google Cloud: `http://34.x.x.x/meowmart/` | `'/meowmart'` |

---

## Step 4 — Enable Required Apache Modules

```bash
sudo a2enmod rewrite headers deflate expires
sudo systemctl restart apache2
```

---

## Step 5 — Set Folder Permissions (Linux/Google Cloud)

```bash
sudo chown -R www-data:www-data /var/www/html/meowmart
sudo chmod -R 755 /var/www/html/meowmart
sudo chmod 644 /var/www/html/meowmart/config/config.php
```

---

## Step 6 — Enable PHP Extensions (if missing)

If you see errors like "Call to undefined function mb_strlen()" or PDO errors:

```bash
# On Ubuntu / Debian
sudo apt install php-mbstring php-pdo php-mysql php-xml -y
sudo systemctl restart apache2

# On Windows (XAMPP/WAMP) — edit php.ini and uncomment:
extension=mbstring
extension=pdo_mysql
extension=mysqli
```

---

## Step 7 — Test the Connection

Visit your site in a browser:
```
http://localhost/meowmart/
```

Log in with the demo credentials:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@meowmart.test | password |
| Member | member@meowmart.test | password |

---

## If You Already Have a Database (Existing Server)

Run the migration script instead — it uses `CREATE TABLE IF NOT EXISTS`
so it will **not** delete any existing data:

```bash
mysql -u root -p meowmart < sql/migrate.sql
```

---

## Windows (XAMPP) Quick Setup

1. Start Apache and MySQL in XAMPP Control Panel
2. Open `http://localhost/phpmyadmin`
3. Click **New** → create database named `meowmart` → Collation: `utf8mb4_unicode_ci`
4. Click the `meowmart` database → **Import** tab → Choose `sql/meowmart.sql` → **Go**
5. Copy `config/config.sample.php` → `config/config.php`
6. Set `'db_pass' => ''` (XAMPP default has no root password)
7. Set `'base_url' => '/meowmart'`
8. Visit `http://localhost/meowmart/`

---

## Troubleshooting

| Error | Cause | Fix |
|-------|-------|-----|
| `Table 'meowmart.orders' doesn't exist` | Old DB without new tables | Run `mysql -u root -p meowmart < sql/migrate.sql` |
| `SQLSTATE[HY000] [1045] Access denied` | Wrong DB password in config | Edit `config/config.php`, fix `db_pass` |
| `SQLSTATE[HY000] [2002] Connection refused` | MySQL not running | Start MySQL service |
| `Call to undefined function mb_strlen()` | mbstring extension disabled | `sudo apt install php-mbstring -y && sudo service apache2 restart` |
| `Call to undefined function pdo_mysql()` | PDO MySQL not installed | `sudo apt install php-mysql -y && sudo service apache2 restart` |
| CSS / images not loading | Wrong base_url | Set `'base_url' => '/meowmart'` in config.php |
| Blank white page | PHP error (error display off) | Check `/var/log/apache2/error.log` |
| `.htaccess` not working | mod_rewrite disabled | `sudo a2enmod rewrite && sudo service apache2 restart` |

