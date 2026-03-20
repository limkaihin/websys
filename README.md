# MeowMart

A responsive PHP + MySQL cat e-commerce website built in a simpler lab-style structure.

## Run locally in VS Code (Windows)

1. Extract the zip.
2. Open the project folder in VS Code.
3. Import `sql/meowmart.sql` into MySQL.
4. Update your MySQL username and password in `config/config.php`.
5. Start the local server:

### Option A — bundled PHP in this project

```powershell
./run-local.ps1
```

### Option B — your own PHP installation

```powershell
php -S 127.0.0.1:8000
```

Then open `http://127.0.0.1:8000`

## Database import

```bash
mysql -u root -p < sql/meowmart.sql
```

## Demo accounts

- Admin: `admin@meowmart.test` / `password`
- Member: `member@meowmart.test` / `password`

## Main folders

- `includes/` → shared PHP layout and helper files
- `assets/` → CSS, JavaScript, and product images
- `shop/` → products, cart, wishlist, checkout, orders
- `account/` → register, login, profile, logout
- `content/` → about, contact, help, blog, meowclub
- `admin/` → admin pages
- `sql/` → database setup files
