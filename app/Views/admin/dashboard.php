<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="pd-stat gradient-1">
            <div class="stat-icon"><i class="bi bi-buildings"></i></div>
            <div class="stat-label">Total Companies</div>
            <div class="stat-value"><?= number_format($stats['companies'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="pd-stat gradient-2">
            <div class="stat-icon"><i class="bi bi-passport"></i></div>
            <div class="stat-label">Active Visas</div>
            <div class="stat-value"><?= number_format($stats['visas'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="pd-stat gradient-3">
            <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
            <div class="stat-label">Containers</div>
            <div class="stat-value"><?= number_format($stats['containers'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="pd-stat gradient-4">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">Customers</div>
            <div class="stat-value"><?= number_format($stats['customers'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="pd-stat gradient-5">
            <div class="stat-icon"><i class="bi bi-cart-check"></i></div>
            <div class="stat-label">Total Sales</div>
            <div class="stat-value">৳ <?= number_format($stats['total_sales'] ?? 0, 0) ?></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="pd-stat gradient-6">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-label">Due Amount</div>
            <div class="stat-value">৳ <?= number_format($stats['total_due'] ?? 0, 0) ?></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="pd-stat gradient-1">
            <div class="stat-icon"><i class="bi bi-arrow-down-circle"></i></div>
            <div class="stat-label">Expenses (mo)</div>
            <div class="stat-value">৳ <?= number_format($stats['expense_month'] ?? 0, 0) ?></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="pd-stat gradient-2">
            <div class="stat-icon"><i class="bi bi-tree"></i></div>
            <div class="stat-label">Farm Profit</div>
            <div class="stat-value">৳ <?= number_format($stats['farm_profit'] ?? 0, 0) ?></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="m-0 fw-bold">Sales Overview</h6>
                <select class="form-select form-select-sm" style="width:140px;">
                    <option>Last 30 days</option>
                    <option>Last 90 days</option>
                </select>
            </div>
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="pd-card">
            <h6 class="m-0 fw-bold mb-3">Recent Activity</h6>
            <div class="list-group list-group-flush">
                <?php foreach (($recentActivity ?? []) as $a): ?>
                    <div class="list-group-item border-0 px-0 py-2">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:36px;height:36px;flex-shrink:0;">
                                <i class="bi bi-activity"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small fw-semibold"><?= esc($a['action'] ?? '') ?></div>
                                <div class="text-muted small"><?= esc($a['entity_type'] ?? '') ?> &middot; <?= esc($a['created_at'] ?? '') ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($recentActivity)): ?>
                    <p class="text-muted small text-center my-4">No activity yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
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
            borderColor: '#5e60ce',
            backgroundColor: 'rgba(94,96,206,.12)',
            fill: true, tension: .4, borderWidth: 3, pointRadius: 0,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#eef0f8' } }, x: { grid: { display: false } } }
    }
});
</script>
<?= $this->endSection() ?>
