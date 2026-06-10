<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <?php $_appName = (new \App\Services\SettingService())->get('site.name', 'Pankaj Da ERP'); ?>
    <title><?= esc($title ?? 'Dashboard') ?> - <?= esc($_appName) ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    /* ═══════════════════════════════════════════════════════════════
       MODERNIZE DESIGN SYSTEM — Pankaj Da ERP
       Primary color : #5D87FF   Success : #13DEB9
       Warning       : #FFAE1F   Error   : #FA896B
    ═══════════════════════════════════════════════════════════════ */

    /* ── Design tokens ─────────────────────────────────────────── */
    :root {
        --mz-primary:       #5D87FF;
        --mz-primary-light: #ECF2FF;
        --mz-secondary:     #49BEFF;
        --mz-success:       #13DEB9;
        --mz-success-light: #E6FFFA;
        --mz-warning:       #FFAE1F;
        --mz-warning-light: #FFF5E0;
        --mz-error:         #FA896B;
        --mz-error-light:   #FDECEA;
        --mz-bg:            #F2F6FA;
        --mz-card-bg:       #ffffff;
        --mz-text-primary:  #2A3547;
        --mz-text-muted:    #5A6A85;
        --mz-border:        #E5EAF2;
        --mz-radius:        14px;
        --mz-radius-sm:     8px;
        --mz-shadow:        0 2px 12px rgba(93,135,255,.08);
        --mz-shadow-hover:  0 6px 24px rgba(93,135,255,.14);
        --mz-sidebar-width: 270px;
        --mz-topbar-h:      70px;
    }

    /* ── Reset / base ──────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; }
    html { -webkit-tap-highlight-color: transparent; }
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background: var(--mz-bg);
        color: var(--mz-text-primary);
        margin: 0;
        overflow-x: hidden;
    }

    /* ── Sidebar ───────────────────────────────────────────────── */
    .mz-sidebar {
        position: fixed;
        top: 0; left: 0; bottom: 0;
        width: var(--mz-sidebar-width);
        background: var(--mz-card-bg);
        border-right: 1px solid var(--mz-border);
        z-index: 1040;
        overflow-y: auto;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        transition: transform .28s cubic-bezier(.4,0,.2,1);
    }
    .mz-sidebar::-webkit-scrollbar { width: 4px; }
    .mz-sidebar::-webkit-scrollbar-thumb { background: var(--mz-border); border-radius: 4px; }

    .mz-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px;
        border-bottom: 1px solid var(--mz-border);
        text-decoration: none;
        flex-shrink: 0;
    }
    .mz-brand-icon {
        width: 40px; height: 40px;
        background: linear-gradient(135deg, var(--mz-primary), var(--mz-secondary));
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.1rem; flex-shrink: 0;
    }
    .mz-brand-name { font-weight: 700; font-size: .95rem; color: var(--mz-text-primary); line-height: 1.2; }
    .mz-brand-sub  { font-size: .7rem; color: var(--mz-text-muted); }

    .mz-nav { padding: 10px 12px 80px; flex: 1; }
    .mz-nav-heading {
        font-size: .65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .9px;
        color: var(--mz-text-muted);
        padding: 16px 10px 5px;
    }
    .mz-nav a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        margin-bottom: 2px;
        border-radius: 10px;
        color: var(--mz-text-muted);
        text-decoration: none;
        font-size: .875rem;
        font-weight: 500;
        transition: background .15s, color .15s;
        white-space: nowrap;
    }
    .mz-nav a i { font-size: 1rem; flex-shrink: 0; width: 20px; text-align: center; }
    .mz-nav a:hover  { background: var(--mz-primary-light); color: var(--mz-primary); }
    .mz-nav a.active { background: var(--mz-primary); color: #fff; }
    .mz-nav a.active i { color: #fff; }

    /* Sidebar overlay (mobile backdrop) */
    .mz-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(42,53,71,.45);
        z-index: 1039;
        backdrop-filter: blur(2px);
    }
    .mz-overlay.show { display: block; }

    /* ── Content shell ─────────────────────────────────────────── */
    .mz-content {
        margin-left: var(--mz-sidebar-width);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* ── Topbar ────────────────────────────────────────────────── */
    .mz-topbar {
        background: var(--mz-card-bg);
        border-bottom: 1px solid var(--mz-border);
        padding: 0 24px;
        height: var(--mz-topbar-h);
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: var(--mz-shadow);
        gap: 12px;
    }
    .mz-topbar-left  { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .mz-topbar-right { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }

    /* Mobile search bar (below topbar) */
    .mz-mobile-search {
        background: var(--mz-card-bg);
        border-bottom: 1px solid var(--mz-border);
        padding: 10px 16px;
    }
    .mz-mobile-search .mz-search { width: 100%; }
    .mz-mobile-search .mz-search input { width: 100%; }

    .mz-search {
        position: relative;
        display: flex;
        align-items: center;
    }
    .mz-search i {
        position: absolute;
        left: 11px;
        color: var(--mz-text-muted);
        font-size: .875rem;
        pointer-events: none;
    }
    .mz-search input {
        background: var(--mz-bg);
        border: 1px solid var(--mz-border);
        border-radius: var(--mz-radius-sm);
        padding: 8px 14px 8px 34px;
        font-size: .875rem;
        color: var(--mz-text-primary);
        outline: none;
        width: 220px;
        transition: border-color .15s, box-shadow .15s;
        font-family: inherit;
    }
    .mz-search input::placeholder { color: var(--mz-text-muted); }
    .mz-search input:focus {
        border-color: var(--mz-primary);
        box-shadow: 0 0 0 3px rgba(93,135,255,.15);
    }

    .mz-icon-btn {
        width: 38px; height: 38px;
        border-radius: var(--mz-radius-sm);
        border: 1px solid var(--mz-border);
        background: var(--mz-card-bg);
        color: var(--mz-text-muted);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: background .15s, color .15s;
        font-size: 1rem;
        text-decoration: none;
        padding: 0;
    }
    .mz-icon-btn:hover { background: var(--mz-bg); color: var(--mz-primary); }

    .mz-user-btn {
        display: flex; align-items: center; gap: 8px;
        padding: 5px 10px 5px 5px;
        border-radius: 10px;
        border: 1px solid var(--mz-border);
        background: var(--mz-card-bg);
        cursor: pointer;
        color: var(--mz-text-primary);
        transition: background .15s;
        font-family: inherit;
    }
    .mz-user-btn:hover { background: var(--mz-bg); }
    .mz-avatar {
        width: 34px; height: 34px;
        border-radius: var(--mz-radius-sm);
        background: linear-gradient(135deg, var(--mz-primary), var(--mz-secondary));
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: .8rem; font-weight: 700;
        flex-shrink: 0;
    }
    .mz-user-name { font-size: .875rem; font-weight: 600; line-height: 1.2; }

    /* ── Page content area ─────────────────────────────────────── */
    .mz-page {
        padding: 28px;
        flex: 1;
    }

    /* ── Page header (title + breadcrumb) ──────────────────────── */
    .mz-page-header {
        margin-bottom: 24px;
    }
    .mz-page-header h4 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--mz-text-primary);
    }
    .mz-breadcrumb {
        display: flex;
        align-items: center;
        gap: 6px;
        list-style: none;
        padding: 0; margin: 4px 0 0;
        font-size: .78rem;
        color: var(--mz-text-muted);
    }
    .mz-breadcrumb li:not(:last-child)::after { content: '/'; margin-left: 6px; opacity: .5; }
    .mz-breadcrumb li:last-child { color: var(--mz-primary); font-weight: 500; }

    /* ── Cards ─────────────────────────────────────────────────── */
    .pd-card {
        background: var(--mz-card-bg);
        border-radius: var(--mz-radius);
        box-shadow: var(--mz-shadow);
        padding: 22px;
        margin-bottom: 22px;
        border: 1px solid var(--mz-border);
    }
    .pd-card:hover { box-shadow: var(--mz-shadow-hover); }

    /* ── Stat cards ────────────────────────────────────────────── */
    .pd-stat {
        background: var(--mz-card-bg);
        border-radius: var(--mz-radius);
        box-shadow: var(--mz-shadow);
        padding: 22px;
        border: 1px solid var(--mz-border);
        transition: box-shadow .2s;
    }
    .pd-stat:hover { box-shadow: var(--mz-shadow-hover); }
    .pd-stat .stat-icon {
        width: 44px; height: 44px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 14px;
        color: #fff;
    }
    .pd-stat .stat-label {
        color: var(--mz-text-muted);
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-weight: 600;
    }
    .pd-stat .stat-value {
        font-size: 1.7rem;
        font-weight: 700;
        color: var(--mz-text-primary);
        line-height: 1.2;
        margin-top: 4px;
    }
    .pd-stat.gradient-1 .stat-icon { background: linear-gradient(135deg,#5D87FF,#49BEFF); }
    .pd-stat.gradient-2 .stat-icon { background: linear-gradient(135deg,#13DEB9,#02a98f); }
    .pd-stat.gradient-3 .stat-icon { background: linear-gradient(135deg,#FFAE1F,#f97316); }
    .pd-stat.gradient-4 .stat-icon { background: linear-gradient(135deg,#FA896B,#e85347); }
    .pd-stat.gradient-5 .stat-icon { background: linear-gradient(135deg,#6930c3,#5D87FF); }
    .pd-stat.gradient-6 .stat-icon { background: linear-gradient(135deg,#00c6ff,#0072ff); }

    /* ── Buttons ───────────────────────────────────────────────── */
    .btn-primary {
        background: var(--mz-primary);
        border-color: var(--mz-primary);
        border-radius: var(--mz-radius-sm);
        font-weight: 500;
        font-size: .875rem;
    }
    .btn-primary:hover, .btn-primary:focus {
        background: #4a73ee;
        border-color: #4a73ee;
    }
    .btn-outline-primary {
        color: var(--mz-primary);
        border-color: var(--mz-primary);
        border-radius: var(--mz-radius-sm);
    }
    .btn-outline-primary:hover {
        background: var(--mz-primary);
        border-color: var(--mz-primary);
    }
    .btn-light {
        background: var(--mz-bg);
        border-color: var(--mz-border);
        color: var(--mz-text-primary);
        border-radius: var(--mz-radius-sm);
        font-size: .875rem;
    }
    .btn-light:hover {
        background: var(--mz-border);
        border-color: var(--mz-border);
    }
    .btn { border-radius: var(--mz-radius-sm); font-family: inherit; }

    /* ── Form controls ─────────────────────────────────────────── */
    .form-control, .form-select {
        border-color: var(--mz-border);
        border-radius: var(--mz-radius-sm);
        color: var(--mz-text-primary);
        font-size: .875rem;
        font-family: inherit;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--mz-primary);
        box-shadow: 0 0 0 3px rgba(93,135,255,.15);
    }
    .form-label {
        font-weight: 500;
        font-size: .875rem;
        color: var(--mz-text-primary);
        margin-bottom: 6px;
    }
    .input-group-text {
        background: var(--mz-bg);
        border-color: var(--mz-border);
        color: var(--mz-text-muted);
    }

    /* ── Tables ────────────────────────────────────────────────── */
    .table { color: var(--mz-text-primary); }
    .table thead th {
        color: var(--mz-text-muted);
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .6px;
        font-weight: 600;
        border-bottom: 2px solid var(--mz-border) !important;
        white-space: nowrap;
        padding: 12px 14px;
    }
    .table td { padding: 12px 14px; vertical-align: middle; border-color: var(--mz-border); }
    .table tbody tr { transition: background .12s; }
    .table tbody tr:hover { background: #F9FAFB; }
    /* Always wrap tables for mobile */
    .pd-card .table-responsive,
    .pd-card > .table { overflow-x: auto; -webkit-overflow-scrolling: touch; }

    /* ── Soft badges ───────────────────────────────────────────── */
    .badge-primary-soft   { background: var(--mz-primary-light); color: var(--mz-primary);  border-radius: 6px; padding: 3px 10px; font-size: .75rem; font-weight: 500; }
    .badge-success-soft   { background: var(--mz-success-light); color: #02a98f;             border-radius: 6px; padding: 3px 10px; font-size: .75rem; font-weight: 500; }
    .badge-warning-soft   { background: var(--mz-warning-light); color: #c98400;             border-radius: 6px; padding: 3px 10px; font-size: .75rem; font-weight: 500; }
    .badge-danger-soft    { background: var(--mz-error-light);   color: #d0593a;             border-radius: 6px; padding: 3px 10px; font-size: .75rem; font-weight: 500; }
    .badge-secondary-soft { background: var(--mz-bg);            color: var(--mz-text-muted);border-radius: 6px; padding: 3px 10px; font-size: .75rem; font-weight: 500; }

    /* ── Alerts (flash messages) ───────────────────────────────── */
    .alert-success { background: var(--mz-success-light); border-color: rgba(19,222,185,.25); color: #02a98f; border-radius: var(--mz-radius-sm); }
    .alert-danger  { background: var(--mz-error-light);   border-color: rgba(250,137,107,.25); color: #d0593a; border-radius: var(--mz-radius-sm); }

    /* ── Dropdowns ─────────────────────────────────────────────── */
    .dropdown-menu {
        border: 1px solid var(--mz-border);
        border-radius: var(--mz-radius);
        box-shadow: 0 8px 30px rgba(93,135,255,.12);
        font-size: .875rem;
        padding: 8px;
    }
    .dropdown-item {
        border-radius: var(--mz-radius-sm);
        color: var(--mz-text-primary);
        padding: 8px 12px;
    }
    .dropdown-item:hover { background: var(--mz-primary-light); color: var(--mz-primary); }
    .dropdown-item.text-danger:hover { background: var(--mz-error-light); color: #d0593a; }
    .dropdown-divider { border-color: var(--mz-border); margin: 6px 0; }

    /* ── Pagination ────────────────────────────────────────────── */
    .page-link { color: var(--mz-primary); border-color: var(--mz-border); border-radius: var(--mz-radius-sm)!important; font-size: .8rem; padding: 5px 11px; }
    .page-item.active .page-link { background: var(--mz-primary); border-color: var(--mz-primary); }
    .page-link:hover { background: var(--mz-primary-light); color: var(--mz-primary); }

    /* ── Misc ──────────────────────────────────────────────────── */
    hr { border-color: var(--mz-border); opacity: 1; }
    .text-muted { color: var(--mz-text-muted) !important; }
    dt { font-weight: 500; color: var(--mz-text-muted); font-size: .875rem; }
    dd { font-size: .875rem; color: var(--mz-text-primary); margin-bottom: .75rem; }
    .list-group-item { border-color: var(--mz-border); font-size: .875rem; }
    .card { border-color: var(--mz-border); border-radius: var(--mz-radius); }

    /* ── Mobile nav bar (bottom) ───────────────────────────────── */
    .mz-bottomnav {
        display: none;
        position: fixed;
        bottom: 0; left: 0; right: 0;
        height: 60px;
        background: var(--mz-card-bg);
        border-top: 1px solid var(--mz-border);
        z-index: 1030;
        box-shadow: 0 -2px 12px rgba(93,135,255,.08);
    }
    .mz-bottomnav-inner {
        display: flex;
        height: 100%;
    }
    .mz-bottomnav a {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 3px;
        color: var(--mz-text-muted);
        text-decoration: none;
        font-size: .6rem;
        font-weight: 500;
        transition: color .15s;
    }
    .mz-bottomnav a i { font-size: 1.2rem; }
    .mz-bottomnav a.active { color: var(--mz-primary); }
    .mz-bottomnav a:hover  { color: var(--mz-primary); }

    /* ═══════════════════════════════════════════════════════════
       RESPONSIVE BREAKPOINTS
    ═══════════════════════════════════════════════════════════ */

    /* Tablet and below (< 992px) */
    @media (max-width: 991.98px) {
        .mz-sidebar {
            transform: translateX(-100%);
            /* On mobile, sidebar slides over content, no gap */
        }
        .mz-sidebar.open {
            transform: translateX(0);
            box-shadow: 4px 0 24px rgba(42,53,71,.18);
        }
        .mz-content {
            margin-left: 0;           /* full width on mobile */
        }
        .mz-page {
            padding: 20px 16px;
            padding-bottom: 80px;    /* space for bottom nav */
        }
        .mz-bottomnav { display: block; }
        /* Safe area for notched phones */
        .mz-bottomnav { padding-bottom: env(safe-area-inset-bottom, 0); height: calc(60px + env(safe-area-inset-bottom, 0)); }
    }

    /* Mobile (< 768px) */
    @media (max-width: 767.98px) {
        .mz-topbar { padding: 0 14px; height: 60px; }
        --mz-topbar-h: 60px;
        .mz-page { padding: 16px 12px; padding-bottom: 80px; }

        /* Stack stat cards 2-up instead of 4-up */
        .row.g-3 > .col-md-3 { flex: 0 0 50%; max-width: 50%; }

        /* Make tables scroll horizontally */
        .pd-card { overflow-x: auto; -webkit-overflow-scrolling: touch; padding: 16px; }
        .pd-card .table { min-width: 600px; }

        /* Hide desktop search (mobile toggle handles it) */
        .mz-search.d-none.d-md-flex { display: none !important; }

        /* Full-width buttons in forms on mobile */
        .mz-page-header .btn { width: 100%; margin-top: 10px; }
        .mz-page-header { flex-direction: column; align-items: flex-start !important; }
    }

    /* Small mobile (< 576px) */
    @media (max-width: 575.98px) {
        .pd-stat .stat-value { font-size: 1.4rem; }
        .mz-brand-name { font-size: .875rem; }
        /* Stack all columns full width */
        .row.g-3 > [class*="col-"] { flex: 0 0 100%; max-width: 100%; }
        /* Smaller page header */
        .mz-page-header h4 { font-size: 1.1rem; }
    }
    </style>

    <?= $this->renderSection('head') ?>
</head>
<body>

<?= $this->include('layouts/partials/_sidebar') ?>

<!-- ── Main Content ──────────────────────────────────────────────── -->
<div class="mz-content">

    <?= $this->include('layouts/partials/_topbar') ?>

    <!-- Page Content -->
    <main class="mz-page">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <?php foreach ((array) session()->getFlashdata('errors') as $err): ?>
                    <div><?= esc($err) ?></div>
                <?php endforeach; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

    <!-- Mobile bottom navigation -->
    <nav class="mz-bottomnav" aria-label="Mobile navigation">
        <div class="mz-bottomnav-inner">
            <a href="<?= site_url('admin/dashboard') ?>" class="<?= url_is('admin/dashboard*') || url_is('admin') ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2"></i><span>Dashboard</span>
            </a>
            <a href="<?= site_url('admin/sales') ?>" class="<?= url_is('admin/sales*') ? 'active' : '' ?>">
                <i class="bi bi-cart-check"></i><span>Sales</span>
            </a>
            <a href="<?= site_url('admin/customers') ?>" class="<?= url_is('admin/customers*') ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i><span>Customers</span>
            </a>
            <a href="<?= site_url('admin/expenses') ?>" class="<?= url_is('admin/expenses*') ? 'active' : '' ?>">
                <i class="bi bi-cash-coin"></i><span>Expenses</span>
            </a>
            <a href="#" onclick="openSidebar();return false;">
                <i class="bi bi-grid-3x3-gap"></i><span>More</span>
            </a>
        </div>
    </nav>

</div><!-- /.mz-content -->

<?= $this->include('layouts/partials/_scripts') ?>
<?= $this->renderSection('scripts') ?>
</body>
</html>
