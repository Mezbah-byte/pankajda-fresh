<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
// Helper: get setting value with fallback
function sv(array $flat, string $key, string $default = ''): string {
    return $flat[$key] ?? $default;
}
?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-gear-fill me-2"></i>Settings</h4>
        <ul class="mz-breadcrumb">
            <li>Admin</li>
            <li>Settings</li>
        </ul>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php if ($flash = session()->getFlashdata('success')): ?>
            <span class="badge-success-soft px-3 py-2"><i class="bi bi-check-circle me-1"></i><?= esc($flash) ?></span>
        <?php endif; ?>
    </div>
</div>

<?php if ($flash = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-3"><?= esc($flash) ?></div>
<?php endif; ?>

<div class="row g-4" id="settingsLayout">
    <!-- ── Left nav ───────────────────────────────────────────── -->
    <div class="col-lg-3 col-md-4">
        <div class="pd-card p-0" style="position:sticky;top:80px;">
            <div class="px-4 pt-4 pb-2">
                <div class="fw-bold" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:var(--mz-text-muted);">Configuration</div>
            </div>
            <nav id="settingsNav" class="d-flex flex-column pb-3">
                <a href="#section-general"   class="settings-nav-link active"><i class="bi bi-building me-2"></i>General</a>
                <a href="#section-finance"   class="settings-nav-link"><i class="bi bi-cash-stack me-2"></i>Finance</a>
                <a href="#section-invoice"   class="settings-nav-link"><i class="bi bi-receipt me-2"></i>Invoice</a>
                <a href="#section-system"    class="settings-nav-link"><i class="bi bi-sliders me-2"></i>System</a>
                <div class="mx-3 my-2" style="border-top:1px solid var(--mz-border);"></div>
                <a href="#section-data"      class="settings-nav-link"><i class="bi bi-database me-2"></i>Data Management</a>
            </nav>
        </div>
    </div>

    <!-- ── Right content ─────────────────────────────────────── -->
    <div class="col-lg-9 col-md-8">

        <?php if (empty($grouped)): ?>
            <div class="pd-card text-center py-5">
                <i class="bi bi-gear" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:12px;"></i>
                <p class="text-muted mb-1">No settings found.</p>
                <code class="small">php spark db:seed SettingsSeeder</code>
            </div>
        <?php else: ?>

        <!-- ── GENERAL ────────────────────────────────────────── -->
        <div class="pd-card mb-4" id="section-general">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-2"
                     style="width:42px;height:42px;background:#5D87FF18;color:#5D87FF;flex-shrink:0;">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0">General</h6>
                    <small class="text-muted">Business identity and contact details</small>
                </div>
            </div>
            <form method="post" action="<?= site_url('admin/settings') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Business Name</label>
                        <input type="text" class="form-control" name="site.name"
                               value="<?= esc(sv($settings_flat, 'site.name', 'Pankaj Da Business')) ?>">
                        <div class="form-text">Appears on invoices and reports.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tagline / Slogan</label>
                        <input type="text" class="form-control" name="site.tagline"
                               value="<?= esc(sv($settings_flat, 'site.tagline')) ?>">
                        <div class="form-text">Short description below business name.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Business Email</label>
                        <input type="email" class="form-control" name="site.email"
                               value="<?= esc(sv($settings_flat, 'site.email')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Business Phone</label>
                        <input type="text" class="form-control" name="site.phone"
                               value="<?= esc(sv($settings_flat, 'site.phone')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Website</label>
                        <input type="text" class="form-control" name="site.website"
                               value="<?= esc(sv($settings_flat, 'site.website')) ?>"
                               placeholder="https://example.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Address</label>
                        <input type="text" class="form-control" name="site.address"
                               value="<?= esc(sv($settings_flat, 'site.address')) ?>"
                               placeholder="City, Country">
                    </div>
                </div>
                <div class="mt-4 pt-3" style="border-top:1px solid var(--mz-border);">
                    <button class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save General</button>
                </div>
            </form>
        </div>

        <!-- ── FINANCE ────────────────────────────────────────── -->
        <div class="pd-card mb-4" id="section-finance">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-2"
                     style="width:42px;height:42px;background:#13DEB918;color:#13DEB9;flex-shrink:0;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0">Finance</h6>
                    <small class="text-muted">Currency, tax, and fiscal year settings</small>
                </div>
                <?php $curSymbol = sv($settings_flat, 'finance.currency_symbol', '৳'); $curCode = sv($settings_flat, 'finance.currency', 'BDT'); ?>
                <div class="ms-auto">
                    <span class="badge-success-soft px-3 py-2" style="font-size:.88rem;">
                        <strong><?= esc($curSymbol) ?></strong> <?= esc($curCode) ?>
                    </span>
                </div>
            </div>
            <form method="post" action="<?= site_url('admin/settings') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Currency Code</label>
                        <input type="text" class="form-control" name="finance.currency"
                               value="<?= esc(sv($settings_flat, 'finance.currency', 'BDT')) ?>"
                               placeholder="BDT, USD, EUR…" maxlength="10">
                        <div class="form-text">ISO 4217 code.</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Currency Symbol</label>
                        <input type="text" class="form-control" name="finance.currency_symbol"
                               value="<?= esc(sv($settings_flat, 'finance.currency_symbol', '৳')) ?>"
                               placeholder="৳, $, €…" maxlength="5">
                        <div class="form-text">Used on invoices.</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Default Tax Rate (%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control"
                               name="finance.tax_rate"
                               value="<?= esc(sv($settings_flat, 'finance.tax_rate', '0')) ?>">
                        <div class="form-text">Applied on new sales by default.</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fiscal Year Start</label>
                        <input type="text" class="form-control" name="finance.fiscal_year_start"
                               value="<?= esc(sv($settings_flat, 'finance.fiscal_year_start', '01-01')) ?>"
                               placeholder="MM-DD e.g. 07-01">
                        <div class="form-text">MM-DD format.</div>
                    </div>
                </div>
                <div class="mt-4 pt-3" style="border-top:1px solid var(--mz-border);">
                    <button class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save Finance</button>
                </div>
            </form>
        </div>

        <!-- ── INVOICE ────────────────────────────────────────── -->
        <div class="pd-card mb-4" id="section-invoice">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-2"
                     style="width:42px;height:42px;background:#FFAE1F18;color:#FFAE1F;flex-shrink:0;">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0">Invoice</h6>
                    <small class="text-muted">Numbering, due dates, and invoice templates</small>
                </div>
                <?php $invPrefix = sv($settings_flat, 'invoice.prefix', 'INV-'); $invStart = sv($settings_flat, 'invoice.start_no', '1001'); ?>
                <div class="ms-auto">
                    <span class="badge-warning-soft px-3 py-2" style="font-size:.88rem;">
                        <strong><?= esc($invPrefix) ?></strong><?= esc($invStart) ?>
                    </span>
                </div>
            </div>
            <form method="post" action="<?= site_url('admin/settings') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Invoice Prefix</label>
                        <input type="text" class="form-control" name="invoice.prefix"
                               value="<?= esc(sv($settings_flat, 'invoice.prefix', 'INV-')) ?>"
                               placeholder="INV-">
                        <div class="form-text">e.g. INV-, PD-, etc.</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Starting Number</label>
                        <input type="number" min="1" class="form-control" name="invoice.start_no"
                               value="<?= esc(sv($settings_flat, 'invoice.start_no', '1001')) ?>">
                        <div class="form-text">First invoice number.</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Default Due Days</label>
                        <input type="number" min="0" class="form-control" name="invoice.due_days"
                               value="<?= esc(sv($settings_flat, 'invoice.due_days', '30')) ?>">
                        <div class="form-text">Days from sale date.</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Show Bank Details</label>
                        <select class="form-select" name="invoice.show_bank_details">
                            <option value="1" <?= sv($settings_flat, 'invoice.show_bank_details', '1') === '1' ? 'selected' : '' ?>>Yes</option>
                            <option value="0" <?= sv($settings_flat, 'invoice.show_bank_details', '1') === '0' ? 'selected' : '' ?>>No</option>
                        </select>
                        <div class="form-text">Print bank info on invoices.</div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Invoice Footer Text</label>
                        <input type="text" class="form-control" name="invoice.footer_text"
                               value="<?= esc(sv($settings_flat, 'invoice.footer_text', 'Thank you for your business!')) ?>"
                               placeholder="e.g. Thank you for your business!">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Terms & Conditions</label>
                        <textarea class="form-control" name="invoice.terms" rows="3"
                                  placeholder="Default T&C printed on invoices…"><?= esc(sv($settings_flat, 'invoice.terms')) ?></textarea>
                        <div class="form-text">Printed at bottom of every invoice.</div>
                    </div>
                </div>
                <div class="mt-4 pt-3" style="border-top:1px solid var(--mz-border);">
                    <button class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save Invoice</button>
                    <a href="<?= site_url('admin/sales') ?>" class="btn btn-light ms-2">
                        <i class="bi bi-receipt me-1"></i>View Sales
                    </a>
                </div>
            </form>
        </div>

        <!-- ── SYSTEM ─────────────────────────────────────────── -->
        <div class="pd-card mb-4" id="section-system">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-2"
                     style="width:42px;height:42px;background:#FA896B18;color:#FA896B;flex-shrink:0;">
                    <i class="bi bi-sliders"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0">System</h6>
                    <small class="text-muted">Display preferences and regional settings</small>
                </div>
            </div>
            <form method="post" action="<?= site_url('admin/settings') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date Format</label>
                        <select class="form-select" name="system.date_format">
                            <?php
                            $dateFormats = [
                                'd M Y'   => date('d M Y') . ' (default)',
                                'Y-m-d'   => date('Y-m-d') . ' (ISO)',
                                'd/m/Y'   => date('d/m/Y'),
                                'm/d/Y'   => date('m/d/Y') . ' (US)',
                                'd-m-Y'   => date('d-m-Y'),
                            ];
                            $curDateFmt = sv($settings_flat, 'system.date_format', 'd M Y');
                            foreach ($dateFormats as $fmt => $label): ?>
                                <option value="<?= esc($fmt) ?>" <?= $curDateFmt === $fmt ? 'selected' : '' ?>><?= esc($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Timezone</label>
                        <select class="form-select" name="system.timezone">
                            <?php
                            $tzones = [
                                'Asia/Dhaka'     => 'Asia/Dhaka (Bangladesh)',
                                'Asia/Riyadh'    => 'Asia/Riyadh (Saudi Arabia)',
                                'Asia/Dubai'     => 'Asia/Dubai (UAE)',
                                'Asia/Kolkata'   => 'Asia/Kolkata (India)',
                                'Asia/Singapore' => 'Asia/Singapore',
                                'Asia/Kuala_Lumpur' => 'Asia/Kuala Lumpur',
                                'UTC'            => 'UTC',
                            ];
                            $curTz = sv($settings_flat, 'system.timezone', 'Asia/Dhaka');
                            foreach ($tzones as $tz => $label): ?>
                                <option value="<?= esc($tz) ?>" <?= $curTz === $tz ? 'selected' : '' ?>><?= esc($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Items Per Page</label>
                        <select class="form-select" name="system.items_per_page">
                            <?php
                            $curPP = sv($settings_flat, 'system.items_per_page', '15');
                            foreach (['10', '15', '25', '50', '100'] as $pp): ?>
                                <option value="<?= $pp ?>" <?= $curPP === $pp ? 'selected' : '' ?>><?= $pp ?> per page</option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Default pagination across all lists.</div>
                    </div>
                </div>
                <div class="mt-4 pt-3" style="border-top:1px solid var(--mz-border);">
                    <button class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save System</button>
                </div>
            </form>
        </div>

        <!-- ── DATA MANAGEMENT ────────────────────────────────── -->
        <div class="pd-card mb-4" id="section-data">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-2"
                     style="width:42px;height:42px;background:#5D87FF18;color:#5D87FF;flex-shrink:0;">
                    <i class="bi bi-database"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0">Data Management</h6>
                    <small class="text-muted">Manage reference data used throughout the ERP</small>
                </div>
            </div>

            <div class="row g-3">
                <!-- Company Types -->
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="border:1px solid var(--mz-border);background:var(--mz-bg-subtle,#f8f9fa);">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-2"
                             style="width:44px;height:44px;background:#5D87FF18;color:#5D87FF;flex-shrink:0;">
                            <i class="bi bi-tags fs-5"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold">Company Types</div>
                            <div class="text-muted" style="font-size:.82rem;"><?= (int) $company_types_count ?> type<?= $company_types_count != 1 ? 's' : '' ?> configured</div>
                        </div>
                        <a href="<?= site_url('admin/company-types') ?>" class="btn btn-sm btn-light">
                            Manage <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Countries -->
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="border:1px solid var(--mz-border);background:var(--mz-bg-subtle,#f8f9fa);">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-2"
                             style="width:44px;height:44px;background:#13DEB918;color:#13DEB9;flex-shrink:0;">
                            <i class="bi bi-globe fs-5"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold">Countries</div>
                            <div class="text-muted" style="font-size:.82rem;"><?= (int) $countries_count ?> countr<?= $countries_count != 1 ? 'ies' : 'y' ?> configured</div>
                        </div>
                        <a href="<?= site_url('admin/countries') ?>" class="btn btn-sm btn-light">
                            Manage <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Bank Accounts -->
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="border:1px solid var(--mz-border);background:var(--mz-bg-subtle,#f8f9fa);">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-2"
                             style="width:44px;height:44px;background:#FA896B18;color:#FA896B;flex-shrink:0;">
                            <i class="bi bi-bank fs-5"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold">Bank Accounts</div>
                            <div class="text-muted" style="font-size:.82rem;">Manage business bank accounts</div>
                        </div>
                        <a href="<?= site_url('admin/bank-accounts') ?>" class="btn btn-sm btn-light">
                            Manage <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Users -->
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="border:1px solid var(--mz-border);background:var(--mz-bg-subtle,#f8f9fa);">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-2"
                             style="width:44px;height:44px;background:#FFAE1F18;color:#FFAE1F;flex-shrink:0;">
                            <i class="bi bi-person-gear fs-5"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold">Admin Users</div>
                            <div class="text-muted" style="font-size:.82rem;">Manage user accounts and access</div>
                        </div>
                        <a href="<?= site_url('admin/users') ?>" class="btn btn-sm btn-light">
                            Manage <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── SEED HINT ───────────────────────────────────────── -->
        <div class="pd-card mb-4" style="background:rgba(93,135,255,.04);border:1px dashed rgba(93,135,255,.3);">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-info-circle text-primary mt-1"></i>
                <div>
                    <div class="fw-semibold mb-1" style="font-size:.85rem;">Missing settings?</div>
                    <div class="text-muted" style="font-size:.82rem;">
                        Run the seeders to populate default values:
                        <code class="d-block mt-1">php spark db:seed SettingsSeeder</code>
                        <code class="d-block mt-1">php spark db:seed CompanyTypesSeeder</code>
                        <code class="d-block mt-1">php spark db:seed CountriesSeeder</code>
                    </div>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div><!-- /col right -->
</div><!-- /row -->

<style>
.settings-nav-link {
    display: flex;
    align-items: center;
    padding: .6rem 1.25rem;
    color: var(--mz-text-secondary, #555);
    text-decoration: none;
    font-size: .875rem;
    transition: background .15s, color .15s;
    border-left: 3px solid transparent;
}
.settings-nav-link:hover {
    background: rgba(93,135,255,.06);
    color: var(--mz-primary, #5D87FF);
}
.settings-nav-link.active {
    background: rgba(93,135,255,.08);
    color: var(--mz-primary, #5D87FF);
    border-left-color: var(--mz-primary, #5D87FF);
    font-weight: 600;
}
</style>

<script>
// Highlight sidebar nav on scroll
(function () {
    const links   = document.querySelectorAll('.settings-nav-link[href^="#"]');
    const sections = Array.from(links).map(l => document.querySelector(l.getAttribute('href'))).filter(Boolean);

    function onScroll() {
        let cur = sections[0];
        sections.forEach(s => { if (window.scrollY + 120 >= s.offsetTop) cur = s; });
        links.forEach(l => l.classList.toggle('active', l.getAttribute('href') === '#' + cur.id));
    }
    window.addEventListener('scroll', onScroll, { passive: true });

    // Smooth scroll
    links.forEach(l => l.addEventListener('click', e => {
        e.preventDefault();
        const t = document.querySelector(l.getAttribute('href'));
        if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }));
})();
</script>

<?= $this->endSection() ?>
