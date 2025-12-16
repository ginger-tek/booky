# Booky

A simple, self-hosted book-keeping app for making invoices and tracking expenses.

- Store and organize client contacts
- Create client invoices
- Manage invoices
- Save invoices to PDF/print
- Design invoice template

Uses SQLite by default, but can be configured to use other PDO-supported database drivers (MySQL/MariaDB, SQLSRV/MSSQL, Postgres, etc.)

Run `composer install`, then serve `public/` dir via web server and redirect all to `public/index.php`. See Caddyfile for example web server config