<!-- Sidebar overlay (mobile tap to close) -->
<div class="mz-overlay" id="mzOverlay" onclick="closeSidebar()"></div>

<!-- ── Sidebar ──────────────────────────────────────────────────── -->
<aside class="mz-sidebar" id="mzSidebar">
    <a href="<?= site_url('admin/dashboard') ?>" class="mz-brand">
        <div class="mz-brand-icon"><i class="bi bi-gem"></i></div>
        <div>
            <div class="mz-brand-name">Pankaj Da ERP</div>
            <div class="mz-brand-sub">Business Management</div>
        </div>
    </a>

    <nav class="mz-nav">
        <div class="mz-nav-heading">Main</div>
        <a href="<?= site_url('admin/dashboard') ?>" class="<?= url_is('admin/dashboard*') || url_is('admin') ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2"></i><span>Dashboard</span>
        </a>

        <div class="mz-nav-heading">Business</div>
        <a href="<?= site_url('admin/companies') ?>" class="<?= url_is('admin/companies*') ? 'active' : '' ?>">
            <i class="bi bi-buildings"></i><span>Companies</span>
        </a>
        <a href="<?= site_url('admin/visas') ?>" class="<?= url_is('admin/visas*') ? 'active' : '' ?>">
            <i class="bi bi-passport"></i><span>Visas</span>
        </a>
        <a href="<?= site_url('admin/containers') ?>" class="<?= url_is('admin/containers*') ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i><span>Containers</span>
        </a>
        <a href="<?= site_url('admin/customers') ?>" class="<?= url_is('admin/customers*') ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i><span>Customers</span>
        </a>
        <a href="<?= site_url('admin/sales') ?>" class="<?= url_is('admin/sales*') ? 'active' : '' ?>">
            <i class="bi bi-cart-check"></i><span>Sales</span>
        </a>

        <div class="mz-nav-heading">Operations</div>
        <a href="<?= site_url('admin/employees') ?>" class="<?= url_is('admin/employees*') ? 'active' : '' ?>">
            <i class="bi bi-person-badge"></i><span>Employees</span>
        </a>
        <a href="<?= site_url('admin/farm-projects') ?>" class="<?= url_is('admin/farm*') ? 'active' : '' ?>">
            <i class="bi bi-tree"></i><span>Farm Projects</span>
        </a>
        <a href="<?= site_url('admin/expenses') ?>" class="<?= url_is('admin/expenses*') ? 'active' : '' ?>">
            <i class="bi bi-cash-coin"></i><span>Expenses</span>
        </a>

        <div class="mz-nav-heading">Insights</div>
        <a href="<?= site_url('admin/reports') ?>" class="<?= url_is('admin/reports*') ? 'active' : '' ?>">
            <i class="bi bi-graph-up"></i><span>Reports</span>
        </a>
        <a href="<?= site_url('admin/settings') ?>" class="<?= url_is('admin/settings*') ? 'active' : '' ?>">
            <i class="bi bi-gear-fill"></i><span>Settings</span>
        </a>
        <a href="<?= site_url('admin/notifications') ?>" class="<?= url_is('admin/notifications*') ? 'active' : '' ?>">
            <i class="bi bi-bell"></i><span>Notifications</span>
        </a>
    </nav>
</aside>
