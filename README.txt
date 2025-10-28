KPI Scoreboard - Hostinger-ready package

Upload all files to your public_html root (so index.php is at /public_html/index.php)

1) Database
 - Import db.sql into your database (phpMyAdmin).
 - Database already set in config/db.php to:
    host: localhost
    user: u150718207_user
    pass: Vh:42s4L~sv
    dbname: u150718207_db
 - If your hostinger uses different host, update config/db.php.

2) Create users
 - Use generate_hash.php to create password hashes:
   https://my.virtualventuresph.com/generate_hash.php?pw=yourpassword
 - Copy the output hash into phpMyAdmin when inserting users into the 'users' table.
 - Example queries:
   INSERT INTO users (username, password, role) VALUES ('admin', '<hash_here>', 'admin');
   INSERT INTO users (username, password, role) VALUES ('employee', '<hash_here>', 'employee');

3) Access
 - Login page: https://my.virtualventuresph.com/login.php
 - Admin dashboard: https://my.virtualventuresph.com/admin/dashboard.php
 - Employee dashboard: https://my.virtualventuresph.com/employee/dashboard.php

4) Security notes
 - After creating accounts, delete generate_hash.php from the server.
 - For production consider securing pages further, using HTTPS (Hostinger provides SSL), and harder validation.

Enjoy!
