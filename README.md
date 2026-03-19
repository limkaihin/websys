# MeowMart Integrated Build

This zip is a merged version of the two source projects you uploaded:

- `meowmart-v3-submission.zip` → newer, more complete codebase
- `websys.zip` → older structure with extra routes like categories, search, and bundled Windows PHP

## What was merged

The integrated build keeps the newer MeowMart architecture and adds the most useful missing parts from the older websys build:

- added `shop/categories.php`
- added `shop/search.php`
- added legacy route wrappers for `about.php`, `contact.php`, `blog/index.php`, `blog/post.php`, `shop/category.php`
- added compatibility handlers for old form/action endpoints
- added the bundled Windows `php/` folder from `websys.zip`
- kept the newer admin panel, profile page, wishlist, orders, contact-message saving, PHPMailer, Zebra Session, and richer SQL schema
- fixed a PHPMailer syntax typo in the submitted newer build

## Quick local run in VS Code (Windows)

### Option A — use the bundled PHP folder included in this zip

1. Extract the zip.
2. Open the extracted `meowmart_integrated` folder in VS Code.
3. Import `sql/meowmart.sql` into your MySQL server.
4. Edit `config/config.php` with your MySQL username/password.
5. In PowerShell, run:

```powershell
./run-local.ps1
```

Then open `http://127.0.0.1:8000`

### Option B — use your own PHP installation

```powershell
php -S 127.0.0.1:8000
```

## Database

Import:

```bash
mysql -u root -p < sql/meowmart.sql
```

Demo accounts:

- Admin: `admin@meowmart.test` / `password`
- Member: `member@meowmart.test` / `password`

## Important folders

- `includes/` → newer shared layout + helpers
- `content/` → newer About / Contact / Blog pages
- `shop/` → merged shop routes
- `admin/` → newer admin panel
- `php/` → bundled Windows PHP runtime copied from `websys.zip`

See `MERGE_NOTES.md` for a cleaner difference summary.
