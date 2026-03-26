# Booky

A simple, self-hosted book-keeping app for making invoices and tracking expenses.

- Store and organize client contacts
- Create and manage invoices
- Edit invoice template
- Template invoices for PDF or print
- View metrics via dashboard

Uses SQLite by default, but can be configured to use other PDO-supported database drivers (MySQL/MariaDB, SQLSRV/MSSQL, Postgres, etc.)

## Install/Run
Run `composer install`, then serve `public/` directory via web server and redirect all to `public/index.php`. See [Caddyfile](Caddyfile) for example web server config.

## CLI
Use the CLI to manage users or initialize database schema by running `php src/Cli.php`.