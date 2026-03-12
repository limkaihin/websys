# Setup and requirement notes

This project is structured around the usual INF1005-style requirements:

- fictitious company website
- landing page
- common navigation
- product and content sub-pages
- about page
- PHP and MySQL back end
- CRUD features
- responsive layout using HTML5, Bootstrap, and CSS
- JavaScript interactions
- password hashing, prepared statements, CSRF checks, and escaped output

## Local run

### With PHP built-in server

1. Make sure PHP is installed and `pdo_mysql` is enabled.
2. Import `sql/meowmart.sql` into MySQL.
3. Update `config/config.php`.
4. Run `php -S localhost:8000` in the project root.
5. Open `http://localhost:8000`.

### With XAMPP

1. Move the folder into `htdocs`.
2. Start Apache and MySQL.
3. Import `sql/meowmart.sql` in phpMyAdmin.
4. Open the local URL for the folder.
