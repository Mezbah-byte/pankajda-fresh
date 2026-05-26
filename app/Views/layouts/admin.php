<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?> - Pankaj Da ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --pd-primary: #5e60ce;
            --pd-primary-2: #6930c3;
            --pd-success: #2ec4b6;
            --pd-bg: #f7f8fc;
            --pd-card-shadow: 0 6px 24px rgba(33, 41, 70, .06);
            --pd-radius: 14px;
        }
        body { background: var(--pd-bg); font-family: 'Inter', -apple-system, sans-serif; }
        .pd-sidebar {
            position: fixed; top: 0; left: 0; bottom: 0; width: 260px;
            background: linear-gradient(180deg, #1f1d3a 0%, #2b2356 100%);
            color: #cfd1ff; padding: 22px 0; z-index: 1030;
            transition: transform .25s ease;
        }
        .pd-sidebar .brand {
            color: #fff; font-size: 1.25rem; font-weight: 700;
            padding: 0 22px 24px; border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex; align-items: center; gap: 10px;
        }
        .pd-sidebar .brand i { color: var(--pd-success); }
        .pd-nav { padding: 16px 12px; }
        .pd-nav a {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px; margin-bottom: 4px;
            color: #b9bce6; text-decoration: none; border-radius: 10px;
            font-size: .92rem; font-weight: 500;
            transition: all .15s;
        }
        .pd-nav a:hover { background: rgba(255,255,255,.06); color: #fff; }
        .pd-nav a.active {
            background: linear-gradient(135deg, var(--pd-primary), var(--pd-primary-2));
            color: #fff; box-shadow: 0 4px 14px rgba(94,96,206,.4);
        }
        .pd-nav .nav-heading {
            font-size: .7rem; text-transform: uppercase; letter-spacing: 1px;
            color: #6e72a8; padding: 16px 14px 6px;
        }
        .pd-content { margin-left: 260px; min-height: 100vh; }
        .pd-topbar {
            background: #fff; padding: 14px 28px; box-shadow: var(--pd-card-shadow);
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
        }
        .pd-page { padding: 28px; }
        .pd-card {
            background: #fff; border-radius: var(--pd-radius);
            box-shadow: var(--pd-card-shadow); padding: 22px; margin-bottom: 22px;
            border: 0;
        }
        .pd-stat {
            background: #fff; border-radius: var(--pd-radius); padding: 22px;
            box-shadow: var(--pd-card-shadow); border: 0;
            position: relative; overflow: hidden;
        }
        .pd-stat .stat-icon {
            width: 52px; height: 52px; border-radius: 12px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.4rem; margin-bottom: 12px;
        }
        .pd-stat .stat-label { color: #6c757d; font-size: .82rem; text-transform: uppercase; letter-spacing: .5px; }
        .pd-stat .stat-value { font-size: 1.85rem; font-weight: 700; color: #1f1d3a; }
        .pd-stat.gradient-1 .stat-icon { background: linear-gradient(135deg, #6a11cb, #2575fc); color: #fff; }
        .pd-stat.gradient-2 .stat-icon { background: linear-gradient(135deg, #11998e, #38ef7d); color: #fff; }
        .pd-stat.gradient-3 .stat-icon { background: linear-gradient(135deg, #f7971e, #ffd200); color: #fff; }
        .pd-stat.gradient-4 .stat-icon { background: linear-gradient(135deg, #ee0979, #ff6a00); color: #fff; }
        .pd-stat.gradient-5 .stat-icon { background: linear-gradient(135deg, #00c6ff, #0072ff); color: #fff; }
        .pd-stat.gradient-6 .stat-icon { background: linear-gradient(135deg, #ff5858, #f857a6); color: #fff; }
        .table thead th { color: #6c757d; font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; font-weight: 600; }
        .btn-primary {
            background: linear-gradient(135deg, var(--pd-primary), var(--pd-primary-2));
            border: 0;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #4f51ba, #5b289e);
        }
        .badge-status { padding: .35em .7em; font-weight: 500; border-radius: 8px; }
        @media (max-width: 991.98px) {
            .pd-sidebar { transform: translateX(-100%); }
            .pd-sidebar.open { transform: translateX(0); }
            .pd-content { margin-left: 0; }
        }
        .toast-stack { position: fixed; top: 80px; right: 24px; z-index: 1080; }
    </style>
    <?= $this->renderSection('head') ?>
</head>
<body>

<aside class="pd-sidebar" id="pdSidebar">
    <div class="brand">
        <i class="bi bi-gem"></i>
        <span>Pankaj Da ERP</span>
    </div>
    <nav class="pd-nav">
        <div class="nav-heading">Main</div>
        <a href="<?= site_url('admin/dashboard') ?>" class="<?= url_is('admin/dashboard*') || url_is('admin') ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="nav-heading">Business</div>
        <a href="<?= site_url('admin/companies') ?>" class="<?= url_is('admin/companies*') ? 'active' : '' ?>">
            <i class="bi bi-buildings"></i> Companies
        </a>
        <a href="<?= site_url('admin/visas') ?>" class="<?= url_is('admin/visas*') ? 'active' : '' ?>">
            <i class="bi bi-passport"></i> Visas
        </a>
        <a href="<?= site_url('admin/containers') ?>" class="<?= url_is('admin/containers*') ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i> Containers
        </a>
        <a href="<?= site_url('admin/customers') ?>" class="<?= url_is('admin/customers*') ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i> Customers
        </a>
        <a href="<?= site_url('admin/sales') ?>" class="<?= url_is('admin/sales*') ? 'active' : '' ?>">
            <i class="bi bi-cart-check"></i> Sales
        </a>

        <div class="nav-heading">Operations</div>
        <a href="<?= site_url('admin/employees') ?>" class="<?= url_is('admin/employees*') ? 'active' : '' ?>">
            <i class="bi bi-person-badge"></i> Employees
        </a>
        <a href="<?= site_url('admin/farm-projects') ?>" class="<?= url_is('admin/farm*') ? 'active' : '' ?>">
            <i class="bi bi-tree"></i> Farm Projects
        </a>
        <a href="<?= site_url('admin/expenses') ?>" class="<?= url_is('admin/expenses*') ? 'active' : '' ?>">
            <i class="bi bi-cash-coin"></i> Expenses
        </a>

        <div class="nav-heading">Insights</div>
        <a href="<?= site_url('admin/reports') ?>" class="<?= url_is('admin/reports*') ? 'active' : '' ?>">
            <i class="bi bi-graph-up"></i> Reports
        </a>
        <a href="<?= site_url('admin/settings') ?>" class="<?= url_is('admin/settings*') ? 'active' : '' ?>">
            <i class="bi bi-gear-fill"></i> Settings
        </a>
        <a href="<?= site_url('admin/notifications') ?>" class="<?= url_is('admin/notifications*') ? 'active' : '' ?>">
            <i class="bi bi-bell"></i> Notifications
        </a>
    </nav>
</aside>

<div class="pd-content">
    <header class="pd-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none" onclick="document.getElementById('pdSidebar').classList.toggle('open')">
                <i class="bi bi-list"></i>
            </button>
            <h5 class="m-0 fw-bold"><?= esc($title ?? 'Dashboard') ?></h5>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Notification Bell Dropdown -->
            <div class="dropdown">
                <button class="btn btn-light btn-sm position-relative" data-bs-toggle="dropdown" id="notifBell" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notifBadge" style="font-size:.6rem;"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow p-0" style="min-width:320px;max-width:380px;" id="notifMenu">
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                        <span class="fw-semibold small">Notifications</span>
                        <div class="d-flex gap-2">
                            <button class="btn btn-link btn-sm p-0 text-muted" id="notifMarkAll" title="Mark all read"><i class="bi bi-check2-all"></i></button>
                            <a href="<?= site_url('admin/notifications') ?>" class="btn btn-link btn-sm p-0 text-muted" title="View all"><i class="bi bi-arrow-right-circle"></i></a>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0" id="notifList" style="max-height:320px;overflow-y:auto;">
                        <li class="text-center py-4 text-muted small" id="notifLoading"><i class="bi bi-hourglass-split me-1"></i>Loading…</li>
                    </ul>
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:.8rem;">
                        <?= esc(strtoupper(substr(session('user_name') ?? 'A', 0, 1))) ?>
                    </span>
                    <span class="d-none d-md-inline"><?= esc(session('user_name') ?? 'Admin') ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item" href="<?= site_url('admin/profile') ?>"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="<?= site_url('admin/settings') ?>"><i class="bi bi-gear me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <main class="pd-page">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('success')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?= $this->renderSection('content') ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// -----------------------------------------------------------------------
// Notification bell — fetch unread on page load and on dropdown open
// -----------------------------------------------------------------------
(function () {
    const bell    = document.getElementById('notifBell');
    const badge   = document.getElementById('notifBadge');
    const list    = document.getElementById('notifList');
    const loading = document.getElementById('notifLoading');
    const markAll = document.getElementById('notifMarkAll');

    if (!bell) return;

    const BASE = '<?= site_url('admin/notifications') ?>';
    const CSRF = '<?= csrf_hash() ?>';

    function renderItem(n) {
        const li = document.createElement('li');
        li.id = 'nb-' + n.un_id;
        li.className = 'border-bottom px-3 py-2 d-flex gap-2 align-items-start' + (!n.read_at ? ' bg-light' : '');
        li.innerHTML =
            '<div class="flex-shrink-0 mt-1">' +
                (n.read_at
                    ? '<i class="bi bi-circle text-muted" style="font-size:.5rem;"></i>'
                    : '<i class="bi bi-circle-fill text-primary" style="font-size:.5rem;"></i>') +
            '</div>' +
            '<div class="flex-grow-1" style="font-size:.82rem;">' +
                '<div class="fw-semibold">' + escHtml(n.title) + '</div>' +
                (n.body ? '<div class="text-muted">' + escHtml(n.body) + '</div>' : '') +
                '<div class="text-muted" style="font-size:.72rem;">' + escHtml(n.type) + '</div>' +
            '</div>' +
            (n.link
                ? '<a href="' + escHtml(n.link) + '" class="btn btn-sm btn-light py-0 px-1 align-self-center"><i class="bi bi-arrow-right-circle"></i></a>'
                : '');
        return li;
    }

    function escHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function loadUnread() {
        fetch(BASE + '/../../api/v1/notifications/unread', {
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const items = res.data || [];
            // Update badge
            if (items.length > 0) {
                badge.textContent = items.length > 99 ? '99+' : items.length;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
            // Populate list
            if (loading) loading.remove();
            list.innerHTML = '';
            if (items.length === 0) {
                list.innerHTML = '<li class="text-center py-4 text-muted small"><i class="bi bi-bell-slash me-1"></i>No new notifications</li>';
                return;
            }
            items.forEach(n => list.appendChild(renderItem(n)));
        })
        .catch(() => {});
    }

    // Load on dropdown open
    bell.addEventListener('show.bs.dropdown', loadUnread);

    // Mark all read
    if (markAll) {
        markAll.addEventListener('click', e => {
            e.stopPropagation();
            fetch(BASE + '/read-all', {
                method: 'POST',
                headers: {'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF}
            }).then(() => {
                badge.classList.add('d-none');
                list.querySelectorAll('.bg-light').forEach(el => el.classList.remove('bg-light'));
                list.querySelectorAll('.bi-circle-fill').forEach(el => {
                    el.classList.replace('bi-circle-fill', 'bi-circle');
                    el.classList.replace('text-primary', 'text-muted');
                });
            });
        });
    }

    // Initial badge count (silent, without opening dropdown)
    fetch(BASE + '/../../api/v1/notifications/count', {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(res => {
        if (res.success && res.data && res.data.unread > 0) {
            badge.textContent = res.data.unread > 99 ? '99+' : res.data.unread;
            badge.classList.remove('d-none');
        }
    })
    .catch(() => {});
})();
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
