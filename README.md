# Pankaj Da Business Management System

A modern, ERP-style business management system built on **CodeIgniter 4 (PHP 8.2+)** for visa, import-export, vegetable trading, farm projects, and accounts.

## Architecture

Strict layered architecture: **Controller → Service → Repository → Model → DB**.

```
app/
├── Controllers/        Thin HTTP handlers (Api/, Admin/, Web/)
├── Services/           Business logic (transaction-aware, audited)
├── Repositories/       Persistence access (un_id-aware)
├── Models/             Table definitions + validation
├── Entities/           Domain objects (optional)
├── Filters/            JWT, RBAC, CORS, rate-limit, web-auth
├── Libraries/          JWT, UUID, ActivityLogger
├── Helpers/            uuid_helper, response_helper
├── Traits/             UnIdTrait, ApiResponseTrait
├── Database/
│   ├── Migrations/     11 migrations covering all 14 modules
│   └── Seeds/          10 seeders for full demo data
└── Views/              Bootstrap 5 admin + public site
```

### Key conventions

- All tables carry `id` (internal), `un_id` (public UUID), `created_at`, `updated_at`, `deleted_at`.
- All foreign references use `*_un_id` columns — **never numeric `id`**.
- `un_id` is auto-generated via the `UnIdTrait` mixed into `BaseModel`.
- Soft delete enabled by default in `BaseModel`.
- Services own transactions and audit logs.
- API responses always follow `{ success, message, data, meta, errors }`.

## Tech stack

- CodeIgniter 4.4.8 (pinned for stability)
- PHP 8.2+
- MySQL 8+
- firebase/php-jwt 6.11.x
- ramsey/uuid for un_id generation
- Bootstrap 5.3 + Bootstrap Icons + Chart.js for UI

## Quick start (macOS with Homebrew)

These are the actual commands that work on macOS — copy-paste in order, wait for each to finish.

### 1. Install Composer (skip if already have it)

```bash
brew install composer
```

### 2. Install PHP dependencies

```bash
cd "/Users/mezbah/Documents/Claude/Projects/pankaj da"
composer install
```

If you get a security advisory about `firebase/php-jwt`, the project's `composer.json` already has `"audit.block-insecure": false` so it should proceed. If composer still complains, run:

```bash
composer install --no-audit
```

### 3. Install + start MySQL

```bash
brew install mysql
brew services start mysql
```

Verify:
```bash
mysql -u root -e "SELECT VERSION();"
```

If that prompts for a password, run `mysql_secure_installation` first to set one (or just hit enter to leave blank for local dev), then update `.env`:
```ini
database.default.password = your_password
```

### 4. Create the database

```bash
mysql -u root -e "CREATE DATABASE pankajda_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 5. Run migrations + seed demo data

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

You should see 11 migrations apply, followed by 10 seeders running. The dashboard will now have ~12 customers, 8 visas, 8 containers, 25 sales, 12 expenses, 4 farm projects.

### 6. Start the dev server

```bash
php spark serve
```

Open http://localhost:8080.

## Default users (after seeding)

| Email                         | Password      | Role        |
|-------------------------------|---------------|-------------|
| admin@pankajda.example        | admin@1234    | super_admin |
| manager@pankajda.example      | manager@1234  | manager     |
| accountant@pankajda.example   | account@1234  | accountant  |

## Routes overview

### Public site
`/`, `/about`, `/services`, `/companies`, `/contact`, `/login`

### Admin panel (session-protected, `/admin/*`)
- `/admin/dashboard` — KPIs, charts, recent activity
- `/admin/companies` — list / create / edit / show / delete

### REST API (`/api/v1/*`, JWT-protected except auth)
- `POST /auth/login` — returns access + refresh tokens
- `POST /auth/register`, `POST /auth/refresh`, `POST /auth/logout`, `GET /auth/me`
- `GET|POST|PUT|DELETE /companies[/{un_id}]`
- `/visas`, `/containers`, `/customers`, `/sales` (return 501 — implementations land in next session)
- `GET /dashboard/stats` — JSON KPI summary

## Quick API smoke test

```bash
# Login
curl -s -X POST http://localhost:8080/api/v1/auth/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"admin@pankajda.example","password":"admin@1234"}'

# Use the access_token from above:
TOKEN="paste-access-token-here"

# List companies
curl -s http://localhost:8080/api/v1/companies \
  -H "Authorization: Bearer $TOKEN"

# Create a company
curl -s -X POST http://localhost:8080/api/v1/companies \
  -H "Authorization: Bearer $TOKEN" \
  -H 'Content-Type: application/json' \
  -d '{"company_name":"Test Co","company_type":"Trading","status":"active"}'
```

## Common setup errors and fixes

| Error                                                       | Cause                                  | Fix                                                                                        |
|-------------------------------------------------------------|----------------------------------------|--------------------------------------------------------------------------------------------|
| `vendor/autoload.php: No such file or directory`            | composer hasn't run yet                | Run `composer install` first                                                                |
| `command not found: mysql`                                  | MySQL not installed                    | `brew install mysql && brew services start mysql`                                          |
| `firebase/php-jwt … affected by security advisories`        | composer audit blocking install         | `composer.json` already has `audit.block-insecure: false`. Or run `composer install --no-audit` |
| `Access denied for user 'root'@'localhost'`                 | MySQL has a password, .env doesn't     | Set `database.default.password` in `.env`                                                   |
| `Class "CodeIgniter\Filters\Cors" not found`                | CI 4.4 doesn't have Cors filter         | Already fixed in `app/Config/Filters.php`                                                  |
| `Unable to open database` during migrate                    | DB not created yet                     | Re-run the `CREATE DATABASE` command from step 4                                           |
| `intl extension not found`                                  | Missing PHP extension                   | `brew install php@8.2` (intl included), or `pecl install intl`                              |

## Module status

| Module                 | Schema | Service | Controller | Views | Status     |
|------------------------|:------:|:-------:|:----------:|:-----:|------------|
| Authentication / RBAC  | ✅      | ✅       | ✅          | ✅     | Complete    |
| Multi-Company          | ✅      | ✅       | ✅          | ✅     | Complete    |
| Dashboard              | n/a    | ✅       | ✅          | ✅     | Complete    |
| Public website         | n/a    | n/a     | ✅          | ✅     | Complete    |
| Visa Management        | ✅      | ⏳       | stub       | ⏳     | Schema + seeders |
| Container/Import       | ✅      | ⏳       | stub       | ⏳     | Schema + seeders |
| Customers              | ✅      | ⏳       | stub       | ⏳     | Schema + seeders |
| Sales / Payments       | ✅      | ⏳       | stub       | ⏳     | Schema + seeders |
| Employees              | ✅      | ⏳       | ⏳          | ⏳     | Schema + seeders |
| Farm Projects          | ✅      | ⏳       | ⏳          | ⏳     | Schema + seeders |
| Expenses               | ✅      | ⏳       | ⏳          | ⏳     | Schema + seeders |
| Reports & Analytics    | n/a    | ⏳       | ⏳          | ⏳     | Planned     |
| Settings               | ✅      | ⏳       | ⏳          | ⏳     | Schema + seeders |
| Activity Logs / Audit  | ✅      | ✅       | n/a        | n/a   | Working     |

## Continuing the build

This is **session 1** of a multi-session build. Just say "continue" and the next session will:

1. Visa module (controller, service, repository, model, payments, views)
2. Customer + Sales + Payment (linked module group with invoice generator)
3. Container/Import workflow
4. Employees, Farm Projects, Expenses
5. Reports (PDF/Excel) + Settings UI

## License

MIT
