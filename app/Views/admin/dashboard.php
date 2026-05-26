<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<!-- Page header -->
<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Dashboard</h4>
        <ul class="mz-breadcrumb">
            <li>Home</li>
            <li>Dashboard</li>
        </ul>
    </div>
    <div class="text-muted small"><?= date('D, d M Y') ?></div>
</div>

<!-- ── KPI Stat Cards Row ────────────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="pd-stat gradient-1">
            <div class="stat-icon"><i class="bi bi-buildings"></i></div>
            <div class="stat-label">Total Companies</div>
            <div class="stat-value"><?= number_format($stats['companies'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="pd-stat gradient-2">
            <div class="stat-icon"><i class="bi bi-passport"></i></div>
            <div class="stat-label">Active Visas</div>
            <div class="stat-value"><?= number_format($stats['visas'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="pd-stat gradient-3">
            <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
            <div class="stat-label">Containers</div>
            <div class="stat-value"><?= number_format($stats['containers'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="pd-stat gradient-4">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">Customers</div>
            <div class="stat-value"><?= number_format($stats['customers'] ?? 0) ?></div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="pd-stat gradient-1">
            <div class="stat-icon"><i class="bi bi-cart-check"></i></div>
            <div class="stat-label">Total Sales</div>
            <div class="stat-value" style="font-size:1.4rem;">৳ <?= number_format($stats['total_sales'] ?? 0, 0) ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="pd-stat gradient-4">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-label">Due Amount</div>
            <div class="stat-value" style="font-size:1.4rem;">৳ <?= number_format($stats['total_due'] ?? 0, 0) ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="pd-stat gradient-3">
            <div class="stat-icon"><i class="bi bi-arrow-down-circle"></i></div>
            <div class="stat-label">Expenses (Month)</div>
            <div class="stat-value" style="font-size:1.4rem;">৳ <?= number_format($stats['expense_month'] ?? 0, 0) ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="pd-stat gradient-2">
            <div class="stat-icon"><i class="bi bi-tree"></i></div>
            <div class="stat-label">Farm Profit</div>
            <div class="stat-value" style="font-size:1.4rem;">৳ <?= number_format($stats['farm_profit'] ?? 0, 0) ?></div>
        </div>
    </div>
</div>

<!-- ── Charts & Activity ─────────────────────────────────────────── -->
<div class="row g-3">
    <!-- Revenue Chart -->
    <div class="col-lg-8">
        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="fw-bold m-0" style="color:var(--mz-text-primary);">Sales Overview</h6>
                    <p class="text-muted m-0" style="font-size:.78rem;">Revenue trend for the selected period</p>
                </div>
                <select class="form-select form-select-sm" style="width:140px;">
                    <option>Last 30 days</option>
                    <option>Last 90 days</option>
                </select>
            </div>
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-4">
        <div class="pd-card h-100">
            <h6 class="fw-bold mb-4" style="color:var(--mz-text-primary);">Recent Activity</h6>
            <?php if (empty($recentActivity)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-activity" style="font-size:2rem;color:#E5EAF2;"></i>
                    <p class="text-muted small mt-2 mb-0">No activity yet.</p>
                </div>
            <?php else: ?>
                <?php foreach (($recentActivity ?? []) as $a): ?>
                    <div class="d-flex gap-3 align-items-start mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:36px;height:36px;background:#ECF2FF;color:#5D87FF;">
                            <i class="bi bi-activity" style="font-size:.85rem;"></i>
                        </div>
                        <div class="flex-grow-1 border-bottom pb-3" style="border-color:var(--mz-border)!important;">
                            <div class="fw-semibold" style="font-size:.875rem;"><?= esc($a['action'] ?? '') ?></div>
                            <div class="text-muted" style="font-size:.75rem;"><?= esc($a['entity_type'] ?? '') ?> &middot; <?= esc($a['created_at'] ?? '') ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ── Quick Links ───────────────────────────────────────────────── -->
<div class="row g-3 mt-1">
    <?php
    $quickLinks = [
        ['url' => 'admin/companies/create',     'icon' => 'bi-buildings',     'label' => 'Add Company',    'color' => '#5D87FF'],
        ['url' => 'admin/customers/create',     'icon' => 'bi-person-plus',   'label' => 'Add Customer',   'color' => '#13DEB9'],
        ['url' => 'admin/sales/create',         'icon' => 'bi-cart-plus',     'label' => 'New Sale',       'color' => '#FFAE1F'],
        ['url' => 'admin/expenses/create',      'icon' => 'bi-cash-coin',     'label' => 'Add Expense',    'color' => '#FA896B'],
    ];
    foreach ($quickLinks as $ql):
    ?>
        <div class="col-sm-6 col-md-3">
            <a href="<?= site_url($ql['url']) ?>" class="text-decoration-none">
                <div class="pd-card d-flex align-items-center gap-3 py-3" style="margin-bottom:0;transition:transform .15s,box-shadow .15s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(93,135,255,.14)';" onmouseout="this.style.transform='';this.style.boxShadow='';">
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:40px;height:40px;background:<?= $ql['color'] ?>18;color:<?= $ql['color'] ?>;flex-shrink:0;font-size:1.1rem;">
                        <i class="bi <?= $ql['icon'] ?>"></i>
                    </div>
                    <span class="fw-semibold" style="font-size:.875rem;color:var(--mz-text-primary);"><?= $ql['label'] ?></span>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('salesChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chart['labels'] ?? []) ?>,
        datasets: [{
            label: 'Sales (৳)',
            data: <?= json_encode($chart['values'] ?? []) ?>,
            borderColor: '#5D87FF',
            backgroundColor: 'rgba(93,135,255,.08)',
            fill: true,
            tension: .4,
            borderWidth: 2.5,
            pointRadius: 0,
            pointHoverRadius: 5,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#E5EAF2' },
                border: { display: false },
                ticks: { color: '#5A6A85', font: { size: 11 } }
            },
            x: {
                grid: { display: false },
                border: { display: false },
                ticks: { color: '#5A6A85', font: { size: 11 } }
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
