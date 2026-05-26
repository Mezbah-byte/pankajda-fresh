<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Monthly Sales</h4>
        <p class="text-muted small m-0">From <?= esc($data['from']) ?> to <?= esc($data['to']) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="?from=<?= esc($data['from']) ?>&to=<?= esc($data['to']) ?>&export=csv" class="btn btn-light"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV</a>
        <button class="btn btn-light" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
        <a href="<?= site_url('admin/reports') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-3"><label class="form-label small text-muted mb-1">From</label><input type="date" class="form-control" name="from" value="<?= esc($data['from']) ?>"></div>
        <div class="col-md-3"><label class="form-label small text-muted mb-1">To</label><input type="date" class="form-control" name="to" value="<?= esc($data['to']) ?>"></div>
        <div class="col-md-3 d-flex align-items-end"><button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Apply</button></div>
    </form>

    <canvas id="monthlyChart" height="80" class="mb-4"></canvas>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Month</th><th class="text-end">Invoices</th><th class="text-end">Total</th><th class="text-end">Paid</th><th class="text-end">Due</th></tr></thead>
            <tbody>
                <?php foreach ($data['rows'] as $r): ?>
                    <tr>
                        <td><?= esc($r['month']) ?></td>
                        <td class="text-end"><?= number_format((int) $r['invoices']) ?></td>
                        <td class="text-end fw-semibold">৳ <?= number_format((float) $r['total'], 2) ?></td>
                        <td class="text-end text-success">৳ <?= number_format((float) $r['paid'], 2) ?></td>
                        <td class="text-end text-danger">৳ <?= number_format((float) $r['due'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="table-light fw-bold">
                    <td>Total</td>
                    <td class="text-end"><?= number_format($data['totals']['invoices']) ?></td>
                    <td class="text-end">৳ <?= number_format($data['totals']['total'], 2) ?></td>
                    <td class="text-end text-success">৳ <?= number_format($data['totals']['paid'], 2) ?></td>
                    <td class="text-end text-danger">৳ <?= number_format($data['totals']['due'], 2) ?></td>
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
    data: { labels, datasets: [{ label: 'Sales (৳)', data: totals, backgroundColor: '#5e60ce', borderRadius: 6 }] },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#eef0f8' } }, x: { grid: { display: false } } }
    }
});
</script>
<?= $this->endSection() ?>
