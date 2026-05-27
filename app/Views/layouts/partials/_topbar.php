<!-- ── Topbar ──────────────────────────────────────────────────── -->
<header class="mz-topbar">
    <div class="mz-topbar-left">
        <!-- Hamburger: visible on mobile only -->
        <button class="mz-icon-btn d-lg-none border-0"
                onclick="openSidebar()"
                style="background:transparent;"
                aria-label="Open menu">
            <i class="bi bi-list fs-5"></i>
        </button>

        <!-- Search: hidden on mobile, shown via toggle -->
        <div class="mz-search d-none d-md-flex">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Search…" aria-label="Search" id="mzSearchInput">
        </div>

        <!-- Mobile search toggle -->
        <button class="mz-icon-btn d-md-none border-0"
                id="mzMobileSearchBtn"
                onclick="toggleMobileSearch()"
                style="background:transparent;"
                aria-label="Search">
            <i class="bi bi-search"></i>
        </button>
    </div>

    <div class="mz-topbar-right">
        <!-- Dark Mode Toggle -->
        <button class="mz-icon-btn border-0" id="darkModeToggle" title="Toggle dark mode"
                onclick="toggleDarkMode()" style="background:transparent;">
            <i class="bi bi-moon" id="darkModeIcon"></i>
        </button>

        <!-- Notification Bell -->
        <div class="dropdown">
            <button class="mz-icon-btn position-relative"
                    data-bs-toggle="dropdown"
                    id="notifBell"
                    title="Notifications">
                <i class="bi bi-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"
                      id="notifBadge"
                      style="font-size:.55rem;min-width:16px;height:16px;padding:0 4px;line-height:16px;"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end p-0"
                 style="min-width:300px;max-width:360px;"
                 id="notifMenu">
                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                    <span class="fw-semibold" style="font-size:.875rem;">Notifications</span>
                    <div class="d-flex gap-2">
                        <button class="btn btn-link btn-sm p-0 text-muted"
                                id="notifMarkAll" title="Mark all read">
                            <i class="bi bi-check2-all"></i>
                        </button>
                        <a href="<?= site_url('admin/notifications') ?>"
                           class="btn btn-link btn-sm p-0 text-muted" title="View all">
                            <i class="bi bi-arrow-right-circle"></i>
                        </a>
                    </div>
                </div>
                <ul class="list-unstyled mb-0"
                    id="notifList"
                    style="max-height:300px;overflow-y:auto;">
                    <li class="text-center py-4 text-muted small" id="notifLoading">
                        <i class="bi bi-hourglass-split me-1"></i>Loading…
                    </li>
                </ul>
            </div>
        </div>

        <!-- User dropdown -->
        <div class="dropdown">
            <button class="mz-user-btn" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="mz-avatar">
                    <?= esc(strtoupper(substr(session('user_name') ?? 'A', 0, 1))) ?>
                </div>
                <div class="d-none d-md-block">
                    <div class="mz-user-name"><?= esc(session('user_name') ?? 'Admin') ?></div>
                    <div style="font-size:.7rem;color:var(--mz-text-muted);">
                        <?= esc(ucfirst(session('user_role') ?? 'Admin')) ?>
                    </div>
                </div>
                <i class="bi bi-chevron-down d-none d-md-block"
                   style="font-size:.7rem;color:var(--mz-text-muted);"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="min-width:190px;">
                <li>
                    <a class="dropdown-item" href="<?= site_url('admin/profile') ?>">
                        <i class="bi bi-person me-2 text-muted"></i>Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= site_url('admin/settings') ?>">
                        <i class="bi bi-gear me-2 text-muted"></i>Settings
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="<?= site_url('logout') ?>">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Mobile search bar (slides in below topbar) -->
<div class="mz-mobile-search d-md-none" id="mzMobileSearch" style="display:none!important;">
    <div class="mz-search" style="width:100%;">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Search…" aria-label="Search" style="width:100%;">
    </div>
</div>
