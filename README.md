# MeowMart – INF1005 Web Systems Group Project

## Quick Setup (LAMP Server)

### 1. Import the database
```bash
mysql -u root -p < sql/meowmart.sql
```

### 2. Configure
```bash
cp config/config.sample.php config/config.php
# Edit config/config.php — set db credentials and base_url
# base_url example: '' (root) or '/meowmart' (subfolder)
```

### 3. Deploy
Upload the entire `meowmart/` folder to your web root (e.g. `/var/www/html/meowmart`).

---

## Demo Login Credentials

| Role   | Email                  | Password   |
|--------|------------------------|------------|
| Admin  | admin@meowmart.test    | Admin123!  |
| Member | member@meowmart.test   | User123!   |

---

## Features

### Pages
- Home (hero, featured products from DB, category grid, membership, blog)
- Shop — search, category filter, sort (A-Z / price / newest), result count
- Product detail — description, stable star rating, add-to-cart, related products
- Cart — update qty, remove items, order summary
- Checkout — form validation, saves order to DB
- **Order Confirmation** — success page with delivery status tracker
- **My Orders** — Shopee-style tabs (All / To Ship / Shipping / Completed)
- Wishlist
- Blog list + individual post
- About Us
- Contact (saves message to DB)
- Login / Register / Profile (with Orders tab + password change)
- Admin: Dashboard, Orders, Products CRUD, Blog CRUD, Messages inbox

### Technical
- PHP 8 + MySQL with PDO prepared statements (SQL injection safe)
- CSRF token on every POST form (XSS safe)
- `password_hash()` / `password_verify()` (bcrypt)
- `htmlspecialchars()` on all output
- Role-based access: `require_login()`, `require_admin()`
- Bootstrap 5 + custom responsive CSS
- Mobile hamburger nav with ARIA labels
- Sticky site header (announcement + navbar together)
- WCAG: skip link, aria-label, aria-current, role attributes, semantic HTML
- W3C-valid HTML5 structure

### Database Tables
- `users` — members and admins
- `products` — product catalogue
- `blog_posts` — blog articles
- `orders` — customer orders
- `order_items` — line items per order
- `contact_messages` — contact form submissions

---

## File Structure
```
meowmart/
├── account/          login, register, logout, profile
├── admin/            dashboard, orders, products, blog, messages
├── assets/
│   ├── css/style.css
│   └── js/main.js
├── config/           db config
├── content/          about, blog, contact
├── includes/         header, footer, navbar, functions, db
├── shop/             products, product, cart, checkout,
│                     order_confirmation, orders, wishlist
├── sql/meowmart.sql  full schema + seed data
└── index.php         homepage
```
