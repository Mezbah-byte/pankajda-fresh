# Pankaj Da ERP — Developer Handoff Guide

> A complete, runnable guide for any developer joining this project. Read top to bottom on day one — it covers what's built, what isn't, how everything fits together, and how to add new code in the project's style.

---

## 1. What this project is

**Pankaj Da ERP** is a modern, ERP-style Business Management System for:

- Visa processing agencies
- Import / export businesses (containerized goods)
- Vegetable & fruit trading (wholesale, cash + credit)
- Farm project management
- Office accounting & expense tracking

It's a single CodeIgniter 4 application that exposes both:

- An **HTML admin panel** at `/admin/*` (Bootstrap 5, session-protected)
- A **public website** at `/`, `/about`, `/services`, `/companies`, `/contact`
- A **REST JSON API** at `/api/v1/*` (JWT-protected)

The core domain entities are: Companies → (Visas, Containers, Customers, Employees, Farm Projects, Expenses) → (Sales → Payments). Every relationship uses public `un_id` UUIDs, never numeric IDs.

---

## 2. Tech stack

| Layer | Choice | Notes |
|---|---|---|
| Framework | **CodeIgniter 4.4.x (or newer)** | Installed via `composer create-project codeigniter4/appstarter` |
| Language | **PHP 8.2+** | Strict-typed properties used throughout |
| Database | **MySQL 8** (utf8mb4) | All schema is via `php spark` migrations |
| Auth | **firebase/php-jwt 6.10+** | Access + refresh token pair, server-side refresh-token revocation |
| UUIDs | **ramsey/uuid 4.7+** | Wrapped in `App\Libraries\UuidGenerator` |
| CSS | **Bootstrap 5.3** | Loaded from CDN; no build step |
| Icons | **Bootstrap Icons** | CDN |
| Charts | **Chart.js 4** | CDN, loaded only on pages that need it |

Optional / used on dev only: PHP's built-in dev server via `php spark serve`.

---

## 3. Architecture — strict 4-layer separation

```
HTTP request
    ↓
Controller       (Controllers/Admin/, Controllers/Api/, Controllers/Web/)
    ↓            thin handlers — parse input, call service, render response
Service          (Services/)
    ↓            ALL business logic lives here (transactions, audit, validation)
Repository       (Repositories/)
    ↓            persistence access (DB, cache, external APIs) hidden behind a stable interface
Model            (Models/)
                 table name + allowed fields + CI4 validation rules ONLY
```

### Rules

1. **Controllers are thin.** They parse the request, validate input shape, call one or two service methods, return a response. Never call a Model directly.
2. **Services own transactions.** Any service method that writes >1 row wraps its work in `$this->transaction(fn () => ...)`.
3. **Services emit audit logs** via `$this->audit('event.name', 'entity', $unId, $context)`.
4. **Repositories return arrays**, never query builders. They hide whether data came from DB, cache, or an API call.
5. **Models are dumb.** Table name, allowed fields, validation rules — nothing else. No business logic.
6. **Public IDs are always `un_id`** (UUID v4 with a 3-letter prefix). Numeric `id` is internal only and never appears in API responses, route segments, or foreign-key columns.

### Why this matters

This layering lets the team:
- Unit-test services without touching the DB (mock the repository)
- Swap MySQL for Postgres / SQLite by changing only repositories
- Add caching to a slow read by editing the repository, with zero controller / service changes
- Onboard new devs predictably — every module looks identical

---

## 4. Project structure

```
pankajda-fresh/
├── app/
│   ├── Common.php                  Global helpers (currently empty)
│   ├── Config/                     CI4 framework + custom config (~36 files)
│   │   ├── App.php, Database.php, Routes.php, Filters.php, Services.php
│   │   ├── Constants.php           Domain constants (ROLE_*, STATUS_*, PAYMENT_*, SALE_*)
│   │   └── Boot/                   per-environment bootstrap (development.php, production.php, testing.php)
│   ├── Controllers/
│   │   ├── Admin/                  Web admin (session-auth). One per resource.
│   │   ├── Api/                    REST JSON API (JWT-auth). One per resource.
│   │   └── Web/                    Public website + login form
│   ├── Database/
│   │   ├── Migrations/             11 numbered migrations (one per major table group)
│   │   └── Seeds/                  12 seeders (RolesSeeder, UsersSeeder, …, DatabaseSeeder orchestrates)
│   ├── Entities/                   Optional — empty so far
│   ├── Filters/
│   │   ├── JwtAuthFilter.php       decodes Bearer token, attaches $request->auth_user
│   │   ├── RoleFilter.php          role-based access guard (used via 'filter' => 'role:admin')
│   │   ├── WebAuthFilter.php       session guard for /admin/*
│   │   ├── ApiCorsFilter.php       CORS for /api/* + OPTIONS preflight
│   │   └── RateLimitFilter.php     CI4 throttler, 30 req/min per IP on auth endpoints
│   ├── Helpers/
│   │   ├── uuid_helper.php         generate_un_id(), short_un_id(), is_un_id()
│   │   └── response_helper.php     api_success(), api_error(), api_paginated(), …
│   ├── Libraries/
│   │   ├── JwtService.php          encode/decode/extract tokens
│   │   ├── UuidGenerator.php       wraps ramsey/uuid
│   │   └── ActivityLogger.php      writes activity_logs rows
│   ├── Models/                     ~19 models, all extend BaseModel
│   │   └── BaseModel.php           soft delete + auto un_id via UnIdTrait
│   ├── Repositories/               ~12 repositories, all extend BaseRepository
│   │   └── BaseRepository.php      paginate(), findByUnId(), updateByUnId(), …
│   ├── Services/                   ~13 services, all extend BaseService
│   │   └── BaseService.php         transaction() + audit() helpers
│   ├── Traits/
│   │   ├── UnIdTrait.php           auto-fills un_id on insert (typed: protected string $unIdPrefix)
│   │   └── ApiResponseTrait.php    $this->ok(), $this->failNotFound(), etc. on controllers
│   └── Views/
│       ├── layouts/                admin.php (sidebar/topbar) + public.php (navbar/footer)
│       ├── admin/                  one folder per module: companies/, visas/, sales/, etc.
│       │                           each contains index.php, form.php, show.php
│       ├── auth/login.php          login form
│       ├── web/                    public site pages (home, about, services, companies, contact)
│       └── errors/html/error_404.php  custom 404
├── public/index.php                front controller
├── spark                           CLI entry point
├── writable/                       cache, logs, sessions (don't commit contents)
├── vendor/                         composer dependencies
├── .env                            DB creds + JWT secret (NOT committed)
└── composer.json
```

---

## 5. Database

### Conventions

Every table has the same metadata columns:

```sql
id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY  -- internal
un_id         VARCHAR(60) UNIQUE NOT NULL                 -- public (UUID v4 with prefix)
created_at    DATETIME
updated_at    DATETIME
deleted_at    DATETIME NULL                               -- soft delete
```

Foreign references **always** use the `<entity>_un_id` VARCHAR(60) column. Never numeric FKs.

### Tables (11 migrations, 17 tables)

| File | Tables created |
|---|---|
| `…_CreateUsersTable.php`           | `users` |
| `…_CreateRolesTable.php`           | `roles`, `permissions`, `role_permissions` |
| `…_CreateCompaniesTable.php`       | `companies` |
| `…_CreateEmployeesTable.php`       | `employees` |
| `…_CreateVisasTable.php`           | `visas`, `visa_payments` |
| `…_CreateContainersTable.php`      | `containers` |
| `…_CreateCustomersTable.php`       | `customers` (carries `current_due`, `credit_limit`) |
| `…_CreateSalesTable.php`           | `sales`, `sale_items`, `sale_payments` |
| `…_CreateExpensesTable.php`        | `expenses` |
| `…_CreateFarmProjectsTable.php`    | `farm_projects`, `farm_activities` |
| `…_CreateActivityLogsTable.php`    | `activity_logs`, `settings`, `refresh_tokens` |

### Seeders

Run `php spark db:seed DatabaseSeeder` to populate ~12 customers, 8 visas, 8 containers, 25 sales (with line items & payments), 12 expenses, 4 farm projects, 8 employees, default settings.

Default users created by seeders:

| Email | Password | Role |
|---|---|---|
| `admin@pankajda.example` | `admin@1234` | super_admin |
| `manager@pankajda.example` | `manager@1234` | manager |
| `accountant@pankajda.example` | `account@1234` | accountant |

---

## 6. Running the project locally (macOS)

### Prerequisites

```bash
brew install php@8.2 composer mysql
brew services start mysql
```

### One-time setup

```bash
git clone <repo>  # or unzip the project
cd pankajda-fresh

# 1. Install PHP deps
composer install

# 2. Configure environment
cp env .env   # then edit .env if MySQL needs a password
# Note: database.default.hostname must be 127.0.0.1 (NOT localhost) on macOS,
# otherwise PHP tries to connect via Unix socket and fails.

# 3. Create DB
mysql -u root -e "CREATE DATABASE pankajda_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. Migrate + seed
php spark migrate
php spark db:seed DatabaseSeeder

# 5. Run
php spark serve
```

Then open `http://localhost:8080` (or whatever port spark printed).

### Daily workflow

```bash
php spark serve                       # start dev server
php spark migrate                     # apply new migrations
php spark migrate:rollback            # undo last migration batch
php spark migrate:fresh --all         # nuke + re-run everything (data loss!)
php spark db:seed CompaniesSeeder     # run one seeder
php spark routes                      # list every registered route
composer dump-autoload -o             # after moving / renaming PHP classes
```

---

## 7. URL surface area

### Public site (no auth)
- `GET  /`            home
- `GET  /about`       about us
- `GET  /services`    services list
- `GET  /companies`   public list of active companies
- `GET  /contact`     contact form
- `POST /contact`     contact form submit (currently just flashes a thank-you)
- `GET  /login`       login form
- `POST /login`       login submit (session-based)
- `GET  /logout`      destroy session

### Admin panel — `/admin/*` (session-protected by `webAuth` filter)
- `/admin/dashboard`            KPIs + sales chart + recent activity
- `/admin/companies`            CRUD
- `/admin/visas`                CRUD + payment ledger
- `/admin/customers`            CRUD + due tracking
- `/admin/sales`                CRUD + line items + payments + printable invoice
- `/admin/containers`           CRUD + cost breakdown + sales linkage
- `/admin/employees`            CRUD + payroll total
- `/admin/farm-projects`        CRUD + activities (workers, seeds, harvest)
- `/admin/expenses`             CRUD + category filtering
- `/admin/reports`              landing page → 6 reports
- `/admin/reports/sales-daily`  daily totals, CSV export
- `/admin/reports/sales-monthly` monthly chart + CSV
- `/admin/reports/customer-dues` who owes how much
- `/admin/reports/expenses-by-category` table + doughnut chart
- `/admin/reports/profit-loss`  sales − expenses − container cost + farm profit
- `/admin/reports/company-wise` per-company breakdown
- `/admin/settings`             grouped key/value config editor

### REST API — `/api/v1/*` (JWT in `Authorization: Bearer <token>`)

All endpoints return:
```json
{ "success": true, "message": "OK", "data": {...}, "meta": {...|null}, "errors": null }
```

**Auth (public):**
- `POST /api/v1/auth/login`     → `{access_token, refresh_token, token_type, expires_in, user}`
- `POST /api/v1/auth/register`
- `POST /api/v1/auth/refresh`   → rotates refresh token
- `POST /api/v1/auth/logout`    → revokes refresh token
- `GET  /api/v1/auth/me`        → current user

**Resources (JWT required):**
- `companies`, `visas`, `customers`, `sales`, `containers`, `employees`, `farm-projects`, `expenses` — full REST (`GET`, `POST`, `PUT`, `DELETE`)
- `POST /api/v1/visas/{un_id}/payments`
- `POST /api/v1/sales/{un_id}/payments`
- `POST /api/v1/farm-projects/{un_id}/activities`
- `GET  /api/v1/expenses/categories`
- `GET  /api/v1/dashboard/stats`

**Settings (JWT required):**
- `GET  /api/v1/settings`            → all settings grouped by area
- `GET  /api/v1/settings/{key}`      → single setting value
- `PUT  /api/v1/settings`            → bulk update (JSON body: `{"key":"value", ...}`)

**Reports (JWT required):**
- `GET  /api/v1/reports/sales-daily?date_from=&date_to=`
- `GET  /api/v1/reports/sales-monthly?date_from=&date_to=`
- `GET  /api/v1/reports/customer-dues`
- `GET  /api/v1/reports/expenses-by-category?date_from=&date_to=`
- `GET  /api/v1/reports/profit-loss?date_from=&date_to=`
- `GET  /api/v1/reports/company-wise`

---

## 8. Naming, validation, and error conventions

### Class & file naming
- Controllers: `XController` (singular) → `XControllerTest` for tests
- Models: `XModel` (singular)
- Repositories: `XRepository`
- Services: `XService`
- Views folder: lowercase plural (`admin/companies/index.php`)

### Constants
All defined in `app/Config/Constants.php` — never use bare strings for roles/statuses:

```php
ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_MANAGER, ROLE_ACCOUNTANT, ROLE_STAFF
STATUS_ACTIVE, STATUS_INACTIVE, STATUS_PENDING
PAYMENT_PAID, PAYMENT_PARTIAL, PAYMENT_DUE
SALE_CASH, SALE_CREDIT
```

### API response helpers (controllers)
```php
return $this->ok($data);                          // 200 with payload
return $this->created($data, 'Created');          // 201
return $this->paginated($items, $page, $perPage, $total);
return $this->failValidation($errors);            // 422 with field-level errors
return $this->failNotFound('Foo not found.');     // 404
return $this->failUnauthorized();                 // 401
return $this->failForbidden();                    // 403
```

### Admin web responses
```php
return redirect()->to('admin/foo')->with('success', '…');
return redirect()->back()->withInput()->with('errors', $validator->getErrors());
```

### Validation
Always validate at the controller boundary using `$this->validate([...])` for web or `$this->validateData($body, [...])` for API.

### Audit logging
Every state-changing service method should emit an audit entry:
```php
$this->audit('company.created', 'company', $unId, ['name' => $data['company_name']]);
```

Entries land in `activity_logs` and appear on the admin dashboard's "Recent Activity" panel.

---

## 9. How to add a NEW module (step-by-step)

Pick a name — let's use `Vendor` as the example. Every module is 6 files + 3 views + routes + (optional) seeder + (optional) API.

### Step 1: Migration

`app/Database/Migrations/2026-02-01-000001_CreateVendorsTable.php`
```php
public function up()
{
    $this->forge->addField([
        'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
        'un_id'          => ['type' => 'VARCHAR', 'constraint' => 60],
        'company_un_id'  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
        'vendor_name'    => ['type' => 'VARCHAR', 'constraint' => 200],
        'phone'          => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
        'status'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
        'created_at'     => ['type' => 'DATETIME', 'null' => true],
        'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
    ]);
    $this->forge->addPrimaryKey('id');
    $this->forge->addUniqueKey('un_id');
    $this->forge->createTable('vendors');
}
```

### Step 2: Model

`app/Models/VendorModel.php`
```php
class VendorModel extends BaseModel
{
    protected $table              = 'vendors';
    protected string $unIdPrefix  = 'VND';     // 3-letter prefix shown in UI
    protected $allowedFields      = ['un_id', 'company_un_id', 'vendor_name', 'phone', 'status'];

    protected $validationRules = [
        'vendor_name' => 'required|min_length[2]|max_length[200]',
    ];
}
```

**IMPORTANT**: `$unIdPrefix` MUST be typed `protected string` — the parent declares it with that type and PHP 8.2 enforces it.

### Step 3: Repository

`app/Repositories/VendorRepository.php`
```php
class VendorRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new VendorModel();
    }

    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $builder->like('vendor_name', $filters['q']);
            }
            if (! empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
        });
    }
}
```

### Step 4: Service

`app/Services/VendorService.php`
```php
class VendorService extends BaseService
{
    private VendorRepository $vendors;
    public function __construct(?VendorRepository $r = null) { $this->vendors = $r ?? new VendorRepository(); }

    public function list(array $f, int $p = 1, int $pp = 20): array { return $this->vendors->search($f, $p, $pp); }
    public function get(string $unId): ?array                       { return $this->vendors->findByUnId($unId); }

    public function create(array $input): array
    {
        $data = $this->normalize($input);
        $unId = $this->transaction(fn () => $this->vendors->create($data));
        $this->audit('vendor.created', 'vendor', $unId, ['name' => $data['vendor_name']]);
        return $this->vendors->findByUnId($unId);
    }

    // …update, delete same pattern…

    private function normalize(array $input): array
    {
        return array_intersect_key($input, array_flip(['company_un_id', 'vendor_name', 'phone', 'status']));
    }
}
```

### Step 5: Admin Controller

`app/Controllers/Admin/VendorController.php` — copy `CompanyController.php` and rename. Same 6 methods: `index`, `create`, `store`, `show`, `edit`, `update`, `delete`.

### Step 6: Views

Copy `app/Views/admin/companies/` to `app/Views/admin/vendors/`, change field names. Keep the layout — uses `<?= $this->extend('layouts/admin') ?>`.

### Step 7: Routes

Inside the `$routes->group('admin', ['filter' => 'webAuth'], …)` block in `app/Config/Routes.php`:

```php
$routes->get('vendors', 'Admin\VendorController::index');
$routes->get('vendors/create', 'Admin\VendorController::create');
$routes->post('vendors', 'Admin\VendorController::store');
$routes->get('vendors/(:segment)', 'Admin\VendorController::show/$1');
$routes->get('vendors/(:segment)/edit', 'Admin\VendorController::edit/$1');
$routes->post('vendors/(:segment)', 'Admin\VendorController::update/$1');
$routes->post('vendors/(:segment)/delete', 'Admin\VendorController::delete/$1');
```

### Step 8: Sidebar link

Add to `app/Views/layouts/admin.php` inside the `<nav class="pd-nav">` section:
```html
<a href="<?= site_url('admin/vendors') ?>" class="<?= url_is('admin/vendors*') ? 'active' : '' ?>">
    <i class="bi bi-shop"></i> Vendors
</a>
```

### Step 9 (optional): API controller

`app/Controllers/Api/VendorController.php` — extends `BaseApiController`, uses the same service. Routes go inside `$routes->group('api/v1', ['namespace' => 'App\Controllers\Api'], …)`:
```php
$routes->resource('vendors', [
    'controller' => 'VendorController',
    'placeholder' => '(:segment)',
    'except' => 'new,edit',
]);
```

### Step 10: Run it

```bash
php spark migrate
composer dump-autoload -o    # only needed when adding new classes
# spark serve picks up controller / view changes automatically
```

---

## 10. Authentication flow

### Web (admin panel)
1. User submits `POST /login` with email + password.
2. `Web\AuthController::doLogin` calls `service('auth')->loginWebSession(...)`.
3. On success, `user_un_id`, `user_name`, `user_email`, `user_role` are written to the session.
4. `WebAuthFilter` on `/admin/*` checks `session('user_un_id')` and redirects to `/login` if missing.

### API (JWT)
1. Client `POST /api/v1/auth/login` → gets `{access_token, refresh_token}`.
2. Subsequent requests include `Authorization: Bearer <access_token>`.
3. `JwtAuthFilter` decodes the token, looks up the user, attaches `$request->auth_user = ['un_id' => …, 'role' => …]`.
4. When the access token expires (default 1h), client posts `refresh_token` to `/api/v1/auth/refresh` — old refresh token is revoked, new pair is issued.
5. Refresh tokens are stored in the `refresh_tokens` table (hashed) for server-side revocation.

### RBAC
Use the `RoleFilter`:
```php
$routes->get('admin/users', '…', ['filter' => 'role:admin,super_admin']);
```

---

## 11. Coding conventions (do these things)

- **Always whitelist fields** in service `normalize()` methods. Never pass `$this->request->getPost()` straight to a repository.
- **Always wrap multi-row writes in a transaction.** Use `$this->transaction(fn () => …)`.
- **Always audit state changes.** `audit('module.event', 'entity_type', $un_id, $context)`.
- **Use `un_id` in every URL, every API response, every FK column.** Never expose internal `id`.
- **Return arrays from repositories**, not entity objects or builders.
- **Use strict types where the parent has them.** `protected string $unIdPrefix` in subclass models.
- **Keep views logic-free.** Calculations belong in the service.
- **Soft-delete by default.** `BaseModel` already enables it. To hard-delete, override `$useSoftDeletes = false`.

---

## 12. Common pitfalls (read before debugging)

| Symptom | Cause | Fix |
|---|---|---|
| `Unable to connect to the database. No such file or directory` | Hostname `localhost` makes MySQLi look for a Unix socket | Set `database.default.hostname = 127.0.0.1` in `.env` |
| `Undefined constant CI_DEBUG` | `Config/Boot/development.php` missing the constant | Already fixed — make sure `defined('CI_DEBUG') \|\| define('CI_DEBUG', true)` is present |
| `Type of X::$unIdPrefix must be string` | Subclass model declared `protected $unIdPrefix` instead of `protected string $unIdPrefix` | Add the `string` type |
| `Class App\Models\X not found` after renaming files | Composer's classmap is stale | `composer dump-autoload -o` |
| Routes return 404 even though file exists | Realpath cache on the running spark serve | Restart `php spark serve` |
| Memory exhausted in `BaseConfig.php` | `Config/Modules.php` has `$discoverInComposer = true` (it scans every vendor namespace) | Set to `false` |
| 404 view crashes with `Undefined variable $message` | Framework default 404 view expects `$message`. We override it at `app/Views/errors/html/error_404.php` — make sure that file is yours, not vendor's |
| `php spark serve` fails with `index.php/foo` 404s | PHP's built-in server mis-parses PATH_INFO | Use clean URLs (`/foo`, not `/index.php/foo`). Set `App.php::$indexPage = ''` |

---

## 13. Current build status (audit run)

✅ = complete · ⚙️ = partial · ⏳ = not started

| Module | Migration | Model | Repo | Service | Admin Ctrl | API Ctrl | Views | Seeder |
|---|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|
| Companies      | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (3) | ✅ |
| Visas          | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (3) | ✅ |
| Customers      | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (3) | ✅ |
| Sales          | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (4) | ✅ |
| Containers     | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (3) | ✅ |
| Employees      | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (3) | ✅ |
| Farm Projects  | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (3) | ✅ |
| Expenses       | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (3) | ✅ |
| Reports        | n/a | n/a | n/a | ✅ | ✅ | ✅ | ✅ (7) | n/a |
| Settings       | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (1) | ✅ |
| Auth + RBAC    | ✅ | ✅ | ✅ | ✅ | n/a | ✅ | ✅ | ✅ (roles, users) |
| Dashboard      | n/a | n/a | n/a | ✅ | ✅ | ✅ | ✅ | n/a |
| Public Website | n/a | n/a | n/a | n/a | ✅ | n/a | ✅ (5) | n/a |
| Activity Logs  | ✅ | n/a | n/a | n/a (Library) | n/a | n/a | n/a | n/a |
| Notifications  | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (1) | n/a |

### Concretely still to do

1. ~~**API controllers** for Employees, FarmProjects, Expenses, Settings~~ — **DONE.** All five API controllers (Employees, FarmProjects, Expenses, Settings, Reports) are implemented and routed under `api/v1`.
2. ~~**`Views/admin/expenses/show.php`**~~ — **DONE.** Show page created, view link added to the expenses index table.
3. ~~**File Upload System**~~ — **DONE.** `app/Libraries/FileUploader.php` implemented. Upload subdirs at `writable/uploads/employees/`, `companies/`, `expenses/`. Registered as `service('fileUploader')`. Use `$uploader->upload('photo', 'employees')` in controllers.
4. ~~**Notification System**~~ — **DONE.** `notifications` table (migration 000012), `NotificationModel`, `NotificationRepository`, `NotificationService`. Admin controller at `Admin\NotificationController`, full-page view at `admin/notifications/index.php`. Bell dropdown in topbar auto-loads unread via AJAX. API endpoints under `api/v1/notifications`. Create notifications anywhere via `service('notifications')->notify([...])`.
5. ~~**Backup System**~~ — **DONE.** `php spark db:backup` command implemented at `app/Commands/DbBackup.php`. Dumps to `writable/backups/YYYY-MM-DD_HH-mm-ss_<db>.sql.gz`. Options: `--no-gz` (plain SQL), `--keep N` (retain last N files, default 30). Requires `mysqldump` on PATH.
6. ~~**PDF export**~~ — **DONE.** `app/Libraries/PdfExporter.php` wraps dompdf. PDF invoice route: `GET /admin/sales/{un_id}/invoice/pdf`. PDF-optimised view at `app/Views/admin/sales/invoice_pdf.php`. **First run:** `composer require dompdf/dompdf` (already in composer.json). Use `PdfExporter::streamView($view, $data, $filename)` from any controller.

---

## 14. API client examples (curl)

```bash
# 1. Login
TOKEN=$(curl -s -X POST http://localhost:8080/api/v1/auth/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"admin@pankajda.example","password":"admin@1234"}' | jq -r .data.access_token)

# 2. List companies (paginated, filtered)
curl -s "http://localhost:8080/api/v1/companies?q=trading&status=active&page=1&per_page=10" \
  -H "Authorization: Bearer $TOKEN" | jq

# 3. Create a sale (cash, with one line item)
curl -s -X POST http://localhost:8080/api/v1/sales \
  -H "Authorization: Bearer $TOKEN" \
  -H 'Content-Type: application/json' \
  -d '{
    "customer_un_id": "CUS-…",
    "sale_type": "cash",
    "items": [
      {"product_name": "Onion", "quantity": 50, "unit": "kg", "unit_price": 60}
    ]
  }' | jq

# 4. Record a payment against a credit sale
curl -s -X POST http://localhost:8080/api/v1/sales/SAL-…/payments \
  -H "Authorization: Bearer $TOKEN" \
  -H 'Content-Type: application/json' \
  -d '{"amount": 5000, "payment_method": "bank_transfer", "reference_no": "TXN123"}'

# 5. List employees (filtered by department)
curl -s "http://localhost:8080/api/v1/employees?department=operations&status=active" \
  -H "Authorization: Bearer $TOKEN" | jq

# 6. Create a farm project
curl -s -X POST http://localhost:8080/api/v1/farm-projects \
  -H "Authorization: Bearer $TOKEN" \
  -H 'Content-Type: application/json' \
  -d '{"project_name": "Winter Rice 2026", "crop_name": "Rice", "land_size": 5, "land_unit": "bigha", "status": "active"}' | jq

# 7. Add a farm activity
curl -s -X POST http://localhost:8080/api/v1/farm-projects/FRM-…/activities \
  -H "Authorization: Bearer $TOKEN" \
  -H 'Content-Type: application/json' \
  -d '{"activity_type": "planting", "workers": 8, "cost": 12000, "description": "Seedling transplant"}' | jq

# 8. Create an expense
curl -s -X POST http://localhost:8080/api/v1/expenses \
  -H "Authorization: Bearer $TOKEN" \
  -H 'Content-Type: application/json' \
  -d '{"expense_title": "Office Rent", "category": "office", "amount": 15000, "expense_date": "2026-05-01"}' | jq

# 9. Get profit & loss report
curl -s "http://localhost:8080/api/v1/reports/profit-loss?date_from=2026-01-01&date_to=2026-05-16" \
  -H "Authorization: Bearer $TOKEN" | jq

# 10. Bulk update settings
curl -s -X PUT http://localhost:8080/api/v1/settings \
  -H "Authorization: Bearer $TOKEN" \
  -H 'Content-Type: application/json' \
  -d '{"site.name": "Pankaj Da Trading", "finance.currency": "BDT"}' | jq
```

---

## 15. Useful files at a glance

| Job | File |
|---|---|
| Change DB credentials | `.env` |
| Add a new route | `app/Config/Routes.php` |
| Add a new admin sidebar item | `app/Views/layouts/admin.php` |
| Customize 404 page | `app/Views/errors/html/error_404.php` |
| Add a global constant | `app/Config/Constants.php` |
| Add a service to the DI container | `app/Config/Services.php` |
| Change session TTL or session driver | `app/Config/Session.php` + `.env` |
| Tighten CORS for production | `app/Filters/ApiCorsFilter.php` |
| Change JWT secret / TTL | `.env` (`jwt.secret`, `jwt.accessTokenTTL`) |
| Recompile composer autoload | `composer dump-autoload -o` |

---

## 16. Where to start, by role

**New backend dev:** Read sections 3 → 4 → 9. Then open `app/Services/CompanyService.php` and `app/Repositories/CompanyRepository.php` side by side — that's the canonical reference. Build a `Vendor` module following section 9 as practice.

**New frontend / UI dev:** Read `app/Views/layouts/admin.php` for the design system. All KPI cards use `.pd-stat.gradient-N`. Every form uses the same `<form class="pd-card">` shell. Bootstrap 5 utility classes only — no custom CSS framework on top.

**DevOps:** Section 6 has the setup. There's no Dockerfile yet — the brief listed it as "optional". When you write one, base off `php:8.2-fpm-alpine` with `mysqli`, `intl`, `gd`, `mbstring`, `bcmath` extensions enabled.

**QA / tester:** Section 7 has the URL map. Default users in section 5. Run `php spark migrate:fresh --all && php spark db:seed DatabaseSeeder` to reset to a known state.

**Product / business:** Section 13 is the truth of what works. Anything marked ⏳ doesn't exist yet — sales demos should stick to the ✅ rows.

---

## 17. Contacts / hand-off notes

This codebase was built in iterative sessions following a strict architectural pattern. **Every module looks the same on purpose** — once you've read one, you've read them all. Future additions should preserve this consistency so the next developer can find their way blindfolded.

If something feels off-pattern, the first question to ask is: "What would `CompanyService` do here?" That module is the canonical reference.

Last updated: end of build session 4 — all stretch features implemented. Every item in section 13 is now ✅. Run `composer require dompdf/dompdf` then `php spark migrate` to activate PDF export and notifications respectively.
