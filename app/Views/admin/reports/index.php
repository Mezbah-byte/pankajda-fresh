<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Reports</h4>
        <p class="text-muted small m-0">Choose a report below. Each one supports date filtering, print, and CSV export.</p>
    </div>
</div>

<div class="row g-3">
    <?php
    $cards = [
        ['icon' => 'bi-graph-up',         'grad' => 1, 'title' => 'Daily Sales',         'desc' => 'Invoice count, total, paid, due by day.',              'url' => 'admin/reports/sales-daily'],
        ['icon' => 'bi-bar-chart-line',   'grad' => 2, 'title' => 'Monthly Sales',       'desc' => 'Trends over the last 12 months.',                       'url' => 'admin/reports/sales-monthly'],
        ['icon' => 'bi-people-fill',      'grad' => 3, 'title' => 'Customer Dues',       'desc' => 'Customers with outstanding balances, sorted by due.',   'url' => 'admin/reports/customer-dues'],
        ['icon' => 'bi-cash-coin',        'grad' => 4, 'title' => 'Expenses by Category','desc' => 'Office, utilities, transport, marketing breakdown.',    'url' => 'admin/reports/expenses-by-category'],
        ['icon' => 'bi-balance',          'grad' => 5, 'title' => 'Profit / Loss',       'desc' => 'Sales minus expenses minus container costs.',           'url' => 'admin/reports/profit-loss'],
        ['icon' => 'bi-buildings',        'grad' => 6, 'title' => 'Company-wise',        'desc' => 'Sales, expenses, dues per company.',                    'url' => 'admin/reports/company-wise'],
    ];
    foreach ($cards as $c): ?>
        <div class="col-md-6 col-lg-4">
            <a href="<?= site_url($c['url']) ?>" class="text-decoration-none">
                <div class="pd-card h-100 text-dark" style="cursor:pointer;transition:transform .15s;">
                    <div class="d-flex gap-3 align-items-start">
                        <div class="stat-icon gradient-<?= $c['grad'] ?>" style="width:52px;height:52px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;font-size:1.4rem;background:linear-gradient(135deg, #5e60ce, #6930c3);color:#fff;">
                            <i class="bi <?= $c['icon'] ?>"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1"><?= esc($c['title']) ?></h6>
                            <p class="text-muted small mb-0"><?= esc($c['desc']) ?></p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>
