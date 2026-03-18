# MeowMart — INF1005 Web Systems & Technologies Group Project

Singapore's favourite cat products e-commerce site. Built with HTML5, Bootstrap, CSS, PHP 8, MySQL, and five open source libraries.

---

## Open Source Libraries Integrated

| Library | Version | Purpose |
|---------|---------|---------|
| Chart.js | 4.4.3 | Admin dashboard — revenue, orders & category charts |
| PHPMailer | 6.9.1 | SMTP email — registration welcome, contact enquiries |
| HTML5 Boilerplate | v9 reference | Meta tags, `.htaccess` security/cache headers |
| Font Awesome Free | 6.5.1 | All icons across navbar, pages and admin panel |
| Zebra_Session | 4.2.0 | MySQL-backed session storage (replaces file sessions) |

---

## Quick Start

```bash
# 1. Import database
mysql -u root -p < sql/meowmart.sql

# 2. Configure app
cp config/config.sample.php config/config.php
# edit config.php with your DB credentials

# 3. Enable Apache modules
sudo a2enmod rewrite headers deflate expires
sudo service apache2 restart
```

Full setup guide → `docs/SETUP_AND_REQUIREMENTS.md`

## Demo Credentials
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@meowmart.test | password |
| Member | member@meowmart.test | password |

