KPI Scoreboard — XAMPP setup and notes

Overview
- Small PHP/MySQL application to manage KPI scoreboards with admin and employee dashboards.
- Place project files in your web server root (e.g., for XAMPP: C:\xampp\htdocs\kpi_scoreboard).

Quick setup (local XAMPP)
1) PHP & extensions
 - PHP 7.4+ recommended. Ensure mysqli extension is enabled.

2) Database
 - Import db.sql into your MySQL server (phpMyAdmin or mysql CLI).
 - Configure database credentials in config/db.php:
    - host
    - username
    - password
    - dbname

3) Create users
 - Use the project's generate_hash.php to create password hashes if provided. Example:
   https://your-host-or-local/generate_hash.php?pw=yourpassword
 - Insert users into the users table (via phpMyAdmin or SQL):
   INSERT INTO users (username, password, role) VALUES ('admin', '<hash_here>', 'admin');
   INSERT INTO users (username, password, role) VALUES ('employee', '<hash_here>', 'employee');
 - After creating accounts, remove or restrict access to generate_hash.php.

Deployment notes (Hostinger / shared hosts)
- Upload files to public_html so index.php is accessible at your domain root.
- Update config/db.php with Hostinger credentials (host, username, password, dbname).
- Enable HTTPS (Hostinger provides free SSL). Always use HTTPS in production.

Security recommendations
- Remove generate_hash.php from production once done.
- Do not store cleartext credentials in README or version control.
- Secure config/db.php (restrict file permissions).
- Consider adding stronger authentication, input validation, CSRF protection, and session hardening for production.
- Keep PHP and server software up-to-date.

URLs (examples)
- Login page: https://your-domain/login.php
- Admin dashboard: https://your-domain/admin/dashboard.php
- Employee dashboard: https://your-domain/employee/dashboard.php

Troubleshooting
- Blank pages or PHP errors: enable display_errors for local dev or check server error_log.
- Database connection errors: confirm config/db.php values and that MySQL is running.
- Permission issues on Hostinger: ensure files are readable by the webserver user and sensitive files are not world-writable.

Useful files
- config/db.php — database connection settings (edit this).
- db.sql — database schema and seed data.
- generate_hash.php — helper to create password hashes (delete after use).
- admin/, employee/ — dashboard pages for each role.

Contact / Credits
- Project: KPI Scoreboard
- For quick support, inspect the PHP error log and verify config/db.php settings.

Enjoy!
