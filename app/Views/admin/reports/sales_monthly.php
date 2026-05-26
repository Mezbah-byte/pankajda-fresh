<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Monthly Sales</h4>
        <ul class="mz-breadcrumb">
            <li><a href="<?= site_url('admin/reports') ?>" class="text-muted text-decoration-none">Reports</a></li>
            <li>Monthly Sales</li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="?from=<?= esc($data['from']) ?>&to=<?= esc($data['to']) ?>&export=csv" class="btn btn-light"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV</a>
        <button class="btn btn-light" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
        <a href="<?= site_url('admin/reports') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-3">
            <label class="form-label" style="font-size:.78rem;color:var(--mz-text-muted);margin-bottom:4px;">From</label>
            <input type="date" class="form-control" name="from" value="<?= esc($data['from']) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label" style="font-size:.78rem;color:var(--mz-text-muted);margin-bottom:4px;">To</label>
            <input type="date" class="form-control" name="to" value="<?= esc($data['to']) ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Apply</button>
        </div>
    </form>

    <div class="mb-4">
        <canvas id="monthlyChart" height="80"></canvas>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr><th>Month</th><th class="text-end">Invoices</th><th class="text-end">Total</th><th class="text-end">Paid</th><th class="text-end">Due</th></tr>
            </thead>
            <tbody>
                <?php foreach ($data['rows'] as $r): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($r['month']) ?></td>
                        <td class="text-end"><?= number_format((int) $r['invoices']) ?></td>
                        <td class="text-end fw-semibold">৳ <?= number_format((float) $r['total'], 2) ?></td>
                        <td class="text-end" style="color:#02a98f;">৳ <?= number_format((float) $r['paid'], 2) ?></td>
                        <td class="text-end" style="color:#FA896B;">৳ <?= number_format((float) $r['due'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="background:#F9FAFB;font-weight:700;">
                    <td>Total</td>
                    <td class="text-end"><?= number_format($data['totals']['invoices']) ?></td>
                    <td class="text-end">৳ <?= number_format($data['totals']['total'], 2) ?></td>
                    <td class="text-end" style="color:#02a98f;">৳ <?= number_format($data['totals']['paid'], 2) ?></td>
                    <td class="text-end" style="color:#FA896B;">৳ <?= number_format($data['totals']['due'], 2) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const labels = <?= json_encode(array_reverse(array_column($data['rows'], 'month'))) ?>;
const totals = <?= json_encode(array_reverse(array_map(fn ($r) => (float) $r['total'], $data['rows']))) ?>;
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Sales (৳)',
            data: totals,
            backgroundColor: '#5D87FF',
            borderRadius: 8,
            hoverBackgroundColor: '#4a70e8'
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#E5EAF2' }, border: { display: false }, ticks: { color: '#5A6A85', font: { size: 11 } } },
            x: { grid: { display: false }, border: { display: false }, ticks: { color: '#5A6A85', font: { size: 11 } } }
        }
    }
});
</script>
<?= $this->endSection() ?>
