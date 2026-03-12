# MeowMart Project – Setup and Run Guide

This guide is for any group member who wants to run the project on their own laptop without needing help on the side.

## What this project needs
This website uses:
- PHP
- MySQL Server
- a web browser
- the project files from the GitHub repo or zip

VS Code is only for editing the files.  
It does **not** run the website by itself.

---

## Before you start
You need these installed on your laptop:

### 1. PHP
Open terminal or PowerShell and run:

```bash
php -v
```

If PHP is installed properly, it should show a version number.

### 2. MySQL Server
You must have **MySQL Server** installed, not just MySQL Workbench.

To check:
- Press `Win + R`
- Type `services.msc`
- Press Enter
- Look for a service called something like:
  - `MySQL80`
  - `MySQL`
  - `MariaDB`

If you only have Workbench and no MySQL service, install MySQL Server first.

### 3. PDO MySQL driver enabled in PHP
Run:

```bash
php -m | findstr pdo
```

You should see:

```text
PDO
pdo_mysql
```

If `pdo_mysql` is missing, open your `php.ini` file and enable:

```ini
extension=pdo_mysql
extension=mysqli
```

Then save and restart your terminal.

---

## Step 1: Get the project files
Clone the repo or extract the zip.

Open the project folder in VS Code.

Important: open the actual project root folder, the one that contains folders and files like these:

```text
websys-main
├── index.php
├── includes
├── assets
├── config
├── shop
├── account
├── content
├── admin
├── sql
```

Do **not** open the wrong parent folder if the zip extracted into a nested folder.

---

## Step 2: Start MySQL Server
After every computer restart, MySQL may not be running automatically.

### Option A: Start it from Services
- Press `Win + R`
- Type `services.msc`
- Press Enter
- Find:
  - `MySQL80` or similar
- Right click
- Click **Start**

### Option B: Check in PowerShell
Run:

```powershell
Get-Service *mysql*
```

If the service is stopped, start it:

```powershell
Start-Service MySQL80
```

If your service has a different name, use that exact name instead.

### Optional: test whether MySQL is listening
Run:

```powershell
Test-NetConnection 127.0.0.1 -Port 3306
```

If MySQL is running properly, port `3306` should be open.

---

## Step 3: Log into MySQL
Use the MySQL client.

Example command:

```powershell
& "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -p
```

Notes:
- replace the version folder if yours is different
- enter your MySQL root password when prompted

If your password is blank, this may work:

```powershell
& "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root
```

---

## Step 4: Create the database
Inside MySQL, run:

```sql
CREATE DATABASE meowmart;
USE meowmart;
```

If the database already exists, you can skip the `CREATE DATABASE` line and just run:

```sql
USE meowmart;
```

---

## Step 5: Import the SQL file
Inside MySQL, run this using the correct full path to the SQL file in the project:

```sql
SOURCE C:/path/to/websys-main/sql/meowmart.sql;
```

Example:

```sql
SOURCE C:/Users/yourname/Downloads/websys-main/sql/meowmart.sql;
```

Important:
- use forward slashes `/`
- use the real path on your own laptop

After import, check that the tables exist:

```sql
SHOW TABLES;
```

Then exit MySQL:

```sql
exit
```

---

## Step 6: Check the database connection file
Open:

```text
includes/db.php
```

Make sure the login details match your own MySQL setup.

Example:

```php
<?php
require_once __DIR__ . '/functions.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=127.0.0.1;dbname=meowmart;charset=utf8mb4";
        $pdo = new PDO($dsn, 'root', 'YOUR_PASSWORD_HERE', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
```

### Change these if needed:
- database name: `meowmart`
- username: usually `root`
- password: whatever your MySQL password is

If your password is blank, use:

```php
''
```

for the password value.

---

## Step 7: Start the PHP local server
Open a terminal in the project root folder and run:

```bash
php -S localhost:8000
```

If it starts properly, leave that terminal open.

---

## Step 8: Open the website
In your browser, go to:

```text
http://localhost:8000
```

---

## Step 9: What to test
Test these pages first:
- Home
- About
- Contact
- Products
- Product details
- Register
- Login
- Profile
- Blog
- Cart
- Checkout
- Admin pages if needed

---

## Common errors and how to fix them

### Error: `could not find driver`
Cause:
- `pdo_mysql` is not enabled in PHP

Fix:
- enable `extension=pdo_mysql` in `php.ini`
- restart terminal
- run `php -m | findstr pdo` again

---

### Error: `Access denied for user`
Cause:
- wrong username or password in `includes/db.php`

Fix:
- check the username and password in `includes/db.php`
- make sure they match your MySQL login

---

### Error: `No connection could be made because the target machine actively refused it`
Cause:
- MySQL Server is not running

Fix:
- go to `services.msc`
- start the MySQL service
- then run the PHP server again

---

### Error: `Failed opening required ... includes/header.php`
Cause:
- wrong folder opened in VS Code
- wrong project root

Fix:
- open the actual project folder where `index.php` and `includes` are together
- then run `php -S localhost:8000` again from that folder

---

### Error after restarting laptop
Cause:
- MySQL service stopped
- PHP local server stopped

Fix:
1. start MySQL again
2. run:

```bash
php -S localhost:8000
```

3. open localhost again

---

## Every time you want to run the project again
Do these 3 things:

### 1. Start MySQL Server
Make sure the MySQL service is running.

### 2. Open terminal in the project folder
Run:

```bash
php -S localhost:8000
```

### 3. Open browser
Go to:

```text
http://localhost:8000
```

That is the normal repeat process.

---

## If something still does not work
Check these in order:
1. Are you in the correct project root folder?
2. Is MySQL Server running?
3. Was the SQL file imported?
4. Does `includes/db.php` match your MySQL username/password?
5. Is `pdo_mysql` enabled in PHP?
6. Is the PHP server running with `php -S localhost:8000`?

If all 6 are correct, the project should run.
