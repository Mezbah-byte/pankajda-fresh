<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header">
    <h4>Reports</h4>
    <ul class="mz-breadcrumb">
        <li>Insights</li>
        <li>Reports</li>
    </ul>
</div>

<div class="row g-3">
    <?php
    $cards = [
        ['icon' => 'bi-graph-up',       'color' => '#5D87FF', 'bg' => '#ECF2FF', 'title' => 'Daily Sales',          'desc' => 'Invoice count, total, paid, due by day.',             'url' => 'admin/reports/sales-daily'],
        ['icon' => 'bi-bar-chart-line', 'color' => '#13DEB9', 'bg' => '#E6FFFA', 'title' => 'Monthly Sales',        'desc' => 'Trends over the last 12 months.',                      'url' => 'admin/reports/sales-monthly'],
        ['icon' => 'bi-people-fill',    'color' => '#FFAE1F', 'bg' => '#FFF5E0', 'title' => 'Customer Dues',        'desc' => 'Customers with outstanding balances, sorted by due.',  'url' => 'admin/reports/customer-dues'],
        ['icon' => 'bi-cash-coin',      'color' => '#FA896B', 'bg' => '#FDECEA', 'title' => 'Expenses by Category', 'desc' => 'Office, utilities, transport, marketing breakdown.',   'url' => 'admin/reports/expenses-by-category'],
        ['icon' => 'bi-balance',        'color' => '#5D87FF', 'bg' => '#ECF2FF', 'title' => 'Profit / Loss',        'desc' => 'Sales minus expenses minus container costs.',          'url' => 'admin/reports/profit-loss'],
        ['icon' => 'bi-buildings',      'color' => '#49BEFF', 'bg' => '#E8F7FF', 'title' => 'Company-wise',         'desc' => 'Sales, expenses, dues per company.',                   'url' => 'admin/reports/company-wise'],
    ];
    foreach ($cards as $c):
    ?>
        <div class="col-md-6 col-lg-4">
            <a href="<?= site_url($c['url']) ?>" class="text-decoration-none">
                <div class="pd-card h-100" style="cursor:pointer;transition:transform .15s,box-shadow .15s;margin-bottom:0;"
                     onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(93,135,255,.14)';"
                     onmouseout="this.style.transform='';this.style.boxShadow='';">
                    <div class="d-flex gap-3 align-items-start">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                             style="width:48px;height:48px;background:<?= $c['bg'] ?>;color:<?= $c['color'] ?>;font-size:1.2rem;">
                            <i class="bi <?= $c['icon'] ?>"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1" style="color:var(--mz-text-primary);"><?= esc($c['title']) ?></h6>
                            <p class="text-muted mb-0" style="font-size:.82rem;line-height:1.5;"><?= esc($c['desc']) ?></p>
                        </div>
                    </div>
                    <div class="mt-3 pt-2" style="border-top:1px solid var(--mz-border);">
                        <span style="font-size:.78rem;color:<?= $c['color'] ?>;font-weight:600;">View Report <i class="bi bi-arrow-right ms-1"></i></span>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>
