# Shared Hosting Deployment Guide

## Requirements

- PHP 8.2 or higher
- MySQL / MariaDB
- At least 128MB PHP memory limit (needed for dompdf PDF generation)

---

## Step 1 — Upload Files

Upload the entire project to your server. The recommended structure:

```
/home/youraccount/          ← everything except public/ goes here
    app/
    vendor/
    writable/
    .env
    spark
    composer.json
    ...

/home/youraccount/public_html/   ← only the contents of public/ go here
    index.php
    .htaccess
    assets/
    ...
```

> **Important:** Do NOT put the entire project inside `public_html/`. Only the contents of the `public/` folder go there. The rest must be one level above `public_html/`.

---

## Step 2 — Fix the index.php Paths

After moving files, open `public_html/index.php` and update the two path constants to point to the correct location:

```php
$pathsConfig = FCPATH . '/../app/Config/Paths.php';  // adjust if needed
```

Specifically find these lines and set correct absolute or relative paths:

```php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
// Make sure $pathsConfig and the system path resolve correctly
```

If your host uses cPanel, the full path is usually `/home/yourusername/`.

---

## Step 3 — Create and Configure .env

Copy the `env` file and rename it `.env`:

```
env  →  .env
```

Edit `.env` and set:

```ini
CI_ENVIRONMENT = production

app.baseURL = 'https://yourdomain.com/'

database.default.hostname = localhost
database.default.database = your_db_name
database.default.username = your_db_user
database.default.password = your_db_password
database.default.DBDriver = MySQLi
```

---

## Step 4 — Set Folder Permissions

The `writable/` folder must be writable by the web server:

```
writable/  →  755 or 777
```

Set via cPanel File Manager or FTP client.

---

## Step 5 — Create the Database

In cPanel → MySQL Databases:
1. Create a new database
2. Create a database user
3. Assign user to the database with ALL PRIVILEGES
4. Update `.env` with these credentials

---

## Step 6 — Run Migrations (No SSH Required)

Since SSH is not available on shared hosting, use the built-in web migration endpoint.

**Before using, change the secret key in `app/Controllers/Setup.php` line ~14:**

```php
if ($key !== 'my-secret-key') {   // ← change 'my-secret-key' to something strong
```

Then visit this URL in your browser:

```
https://yourdomain.com/setup-migrate/YOUR-SECRET-KEY
```

This will run all pending migrations and display which ones were applied.

> **Security Warning:** After migrations complete, either delete this endpoint or change the secret key to something nobody can guess. Anyone who hits this URL can run migrations against your database.

---

## Step 7 — Run Initial Seeder (First Time Only)

To seed the database with initial data (e.g., default admin user), visit:

```
https://yourdomain.com/setup/YOUR-SECRET-KEY
```

This runs migrations AND the UserSeeder. Only do this once on a fresh database.

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| White screen / 500 error | Check `writable/logs/` for error logs |
| "Whoops" page | Set `CI_ENVIRONMENT = development` temporarily to see errors |
| 404 on all pages | `.htaccess` not uploading or mod_rewrite not enabled — contact host |
| PDF generation fails | Increase PHP `memory_limit` to 256MB in cPanel PHP settings |
| Session errors | Make sure `writable/session/` is writable |

---

## After Deployment

- Set `CI_ENVIRONMENT = production` in `.env`
- Remove or lock down the `/setup` and `/setup-migrate` routes
- Test login, PDF generation, and key features
