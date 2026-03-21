# MeowMart

MeowMart is a responsive PHP and MySQL cat e-commerce website built for the INF1005 web systems project. The site is intended to be deployed on a Google Cloud LAMP server and demonstrates a data-driven online store with user accounts, product browsing, cart and wishlist features, checkout, order history, blog content, and admin support.

## Project features

- Responsive layout for desktop and mobile
- User registration, login, logout, and profile pages
- Product catalogue with category filtering and search
- Wishlist and shopping cart
- Checkout with demo card, PayNow, and Google Pay flows
- Voucher code support for `MEOW10`
- Referral code entry during registration and checkout
- Orders saved to the logged-in user account
- Order history and order confirmation pages
- Blog, help, about, contact, and MeowClub pages
- Admin pages for managing site content and data

## Deployment target

This project is designed to run on a **Google Cloud LAMP server** using:

- Apache
- PHP
- MySQL

## Main project folders

- `account/` - login, register, logout, and profile pages
- `admin/` - admin-facing pages
- `assets/` - CSS, JavaScript, icons, and image assets
- `blog/` - blog listing and post pages
- `config/` - project configuration
- `content/` - static content pages such as about, help, contact, and MeowClub
- `includes/` - shared PHP helpers, layout files, and utility functions
- `shop/` - products, cart, wishlist, checkout, orders, and search
- `sql/` - database schema and migration files
- `vendor/` - third-party PHP dependencies used by the project

## Database setup

Import the main schema file into MySQL:

```sql
SOURCE sql/meowmart.sql;
```

If the database already exists and only new columns or tables are needed, run:

```sql
SOURCE sql/migrate.sql;
```

## Configuration

Update `config/config.php` with the correct values for your deployment environment, especially:

- database host
- database name
- database username
- database password
- `base_url` for your Google Cloud deployment

## Demo accounts

- Admin: `admin@meowmart.test` / `password`
- Member: `member@meowmart.test` / `password`

## Notes about implemented behaviour

- Checkout requires a logged-in account so orders are attached to the correct user.
- Existing older orders that match a logged-in user's email can be linked back to that user automatically.
- Card checkout validates a 16-digit card number, `MM/YY` expiry, and a 3-digit CVV.
- Homepage category counts are pulled from the actual database instead of hardcoded marketing numbers.
- Product and blog text has been normalised to avoid broken font and encoding characters.

## Third-party libraries used

- Font Awesome
- Google Fonts
- Zebra Session

## Purpose of the project

This project demonstrates the use of HTML, CSS, Bootstrap-style responsive layout techniques, JavaScript interactions, PHP form processing, and MySQL database integration in a complete web application suitable for deployment on a cloud server.
