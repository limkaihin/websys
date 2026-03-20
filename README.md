# MeowMart

MeowMart is a responsive PHP + MySQL cat e-commerce website built in a simple lab-style structure. It supports browsing products, searching and filtering, user accounts, wishlist, cart, checkout, blog posts, member pages, and a basic admin area.

## What this project does

### Customer-facing features
- Home page with hero section, featured products, membership section, and latest blog posts
- Product listing page with:
  - category filter pills
  - product search
  - sorting by name, price, and newest
- Product detail pages
- Wishlist
- Shopping cart
- Checkout flow
- Order confirmation and order history
- Account registration and login
- User profile page
- Blog listing and blog post pages
- About, Contact, Help, and MeowClub content pages

### Demo features included in the code
- Referral code field during account registration
- Voucher code field at checkout (`MEOW10`)
- Demo payment options at checkout:
  - Credit / Debit Card
  - PayNow
  - Google Pay
- Persistent cart and wishlist for logged-in users
- Flash messages for actions such as login, registration, wishlist updates, and checkout

### Admin-facing features
- Admin dashboard
- Product management pages
- Blog post management pages
- Order management page
- User listing page
- Contact/message listing page

## Tech stack
- PHP
- MySQL / MariaDB
- HTML5
- CSS
- Bootstrap 5
- JavaScript
- Font Awesome

## Project structure

```text
admin/          Admin pages
account/        Login, register, logout, profile
assets/         CSS, JavaScript, images, icons
blog/           Compatibility routes for blog pages
config/         Configuration files
content/        About, Contact, Blog, Help, MeowClub pages
inc/            Compatibility include files
includes/       Shared helpers, DB connection, header, footer, navbar
php/            Bundled PHP for Windows local running
shop/           Products, product details, cart, wishlist, checkout, orders
sql/            Database SQL files
vendor/         Third-party PHP libraries used by the project
index.php       Home page
run-local.ps1   Start local server with bundled PHP (PowerShell)
run-local.bat   Start local server with bundled PHP (Command Prompt)
```

## Requirements
- Windows with VS Code, XAMPP, Laragon, WAMP, or another PHP + MySQL setup
- PHP 8.x
- MySQL or MariaDB
- A web browser

## Local setup

### 1. Extract the project
Extract the project zip and open the project folder in VS Code.

### 2. Create the database
Create a database named `meowmart`.

Example SQL:

```sql
DROP DATABASE IF EXISTS meowmart;
CREATE DATABASE meowmart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Import the SQL file
Import:

```text
sql/meowmart.sql
```

You can do this in MySQL Workbench, phpMyAdmin, or the MySQL command line.

### 4. Update database config
Edit:

```text
config/config.php
```

Make sure these values match your local MySQL setup:

```php
'db_host' => '127.0.0.1',
'db_name' => 'meowmart',
'db_user' => 'root',
'db_pass' => '',
```

If you are running the site from the project root with `php -S`, leave:

```php
'base_url' => '',
```

### 5. Start the site

#### Option A: bundled PHP in this project
PowerShell:

```powershell
./run-local.ps1
```

Command Prompt:

```bat
run-local.bat
```

#### Option B: your own PHP installation
From the project root:

```bash
php -S 127.0.0.1:8000
```

Then open:

```text
http://127.0.0.1:8000
```

## Demo accounts

### Member account
- Email: `member@meowmart.test`
- Password: `password`

### Admin account
- Email: `admin@meowmart.test`
- Password: `password`

## Main routes
- `/index.php` - home page
- `/shop/products.php` - all products
- `/shop/product.php?id=...` - product details
- `/shop/search.php` - search page
- `/shop/cart.php` - cart
- `/shop/wishlist.php` - wishlist
- `/shop/checkout.php` - checkout
- `/shop/orders.php` - order history
- `/account/register.php` - register
- `/account/login.php` - login
- `/account/profile.php` - profile
- `/content/blog.php` - blog page
- `/content/about.php` - about page
- `/content/contact.php` - contact page
- `/content/help.php` - help page
- `/content/meowclub.php` - MeowClub page
- `/admin/dashboard.php` - admin dashboard

## Notes about the current build
- This project is designed to run as a local school/lab submission, not as a production storefront.
- Some features are demo-style by design, especially payment flows and promotional features.
- `shop/categories.php` is not used as a standalone categories page in this build.
- Email sending is off by default unless SMTP is configured in `config/config.php`.
- The `php/` folder is included for easier local running on Windows.

## Troubleshooting

### The site cannot connect to the database
Check `config/config.php` and make sure the database name, username, and password are correct.

### Images, CSS, or links look broken
Make sure you are running the site from the project root and that `base_url` in `config/config.php` matches your setup.

### Login works but data looks missing
Make sure you imported `sql/meowmart.sql` into the correct `meowmart` database.

### Bundled PHP does not run
Restore the `php/` folder or use your own PHP installation with:

```bash
php -S 127.0.0.1:8000
```

## Author note
This project was prepared as a web systems / PHP-MySQL style school project with a structure simplified to stay closer to lab-style coding patterns while still demonstrating a complete working site.
