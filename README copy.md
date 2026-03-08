# MeowMart — PHP/MySQL Project

Singapore's favourite cat store. Full PHP/MySQL e-commerce site converted from a single `index.html`.

## Setup

### 1. Database
```bash
mysql -u root -p < sql/meowmart.sql
```

### 2. Config
```bash
cp config/config.sample.php config/config.php
```
Edit `config/config.php` with your database credentials.

### 3. Serve
Drop the project folder into your web server root (e.g. `htdocs/meowmart`) or use PHP's built-in server:
```bash
php -S localhost:8000
```

## Demo Credentials

| Role   | Email                    | Password  |
|--------|--------------------------|-----------|
| Admin  | admin@meowmart.test      | Admin123! |
| Member | member@meowmart.test     | User123!  |

## Project Structure

```
meowmart/
├── admin/                  Admin panel
│   ├── dashboard.php
│   ├── sidebar.php
│   ├── products.php
│   ├── product_form.php
│   ├── blog_posts.php
│   └── blog_form.php
├── assets/
│   ├── css/style.css       All styles (extracted from original index.html)
│   └── js/main.js          All scripts (extracted from original index.html)
├── config/
│   ├── config.php          Live config (git-ignored)
│   └── config.sample.php   Template
├── includes/
│   ├── functions.php       Helpers: h(), base_url(), flash, CSRF, auth
│   ├── db.php              PDO singleton
│   ├── header.php          HTML head + announcement bar
│   ├── navbar.php          Navigation bar
│   └── footer.php          Footer + JS link
├── sql/
│   └── meowmart.sql        Schema + demo data
├── index.php               Homepage (exact original sections)
├── products.php            Shop all / filter by category
├── product.php             Single product + add to cart
├── cart.php                Cart with update/remove
├── checkout.php            Checkout form (demo)
├── register.php            MeowClub signup
├── login.php               Login
├── logout.php              Session destroy
├── profile.php             Edit profile + cat name
├── blog.php                Blog listing
├── blog_post.php           Single blog post
├── about.php               About MeowMart
├── contact.php             Contact form
└── README.md
```

## Notes

- The homepage (`index.php`) uses **the exact HTML from the original `index.html`** — no visual changes.
- CSS and JS are extracted verbatim into `assets/css/style.css` and `assets/js/main.js`.
- Checkout is demo-only — no real payment is processed.
- Contact form submission is demo-only — wire up `mail()` or an SMTP library for production.
