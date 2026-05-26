<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Expenses by Category</h4>
        <ul class="mz-breadcrumb">
            <li><a href="<?= site_url('admin/reports') ?>" class="text-muted text-decoration-none">Reports</a></li>
            <li>Expenses by Category</li>
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

    <div class="row g-4">
        <div class="col-md-7">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Category</th><th class="text-end">Entries</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                        <?php foreach ($data['rows'] as $r): ?>
                            <tr>
                                <td><span class="badge-secondary-soft"><?= esc(ucfirst($r['category'])) ?></span></td>
                                <td class="text-end"><?= number_format((int) $r['count']) ?></td>
                                <td class="text-end fw-semibold" style="color:#FA896B;">৳ <?= number_format((float) $r['total'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="background:#F9FAFB;font-weight:700;border-top:2px solid var(--mz-border);">
                            <td>Total</td>
                            <td class="text-end"><?= number_format((int) $data['totals']['count']) ?></td>
                            <td class="text-end" style="color:#FA896B;">৳ <?= number_format((float) $data['totals']['total'], 2) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-5">
            <canvas id="catChart" height="220"></canvas>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('catChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_map('ucfirst', array_column($data['rows'], 'category'))) ?>,
        datasets: [{
            data: <?= json_encode(array_map(fn ($r) => (float) $r['total'], $data['rows'])) ?>,
            backgroundColor: ['#5D87FF','#13DEB9','#FA896B','#FFAE1F','#49BEFF','#02a98f','#f97316','#e85347'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 12 }, color: '#5A6A85', padding: 16 } }
        },
        cutout: '60%'
    }
});
</script>
<?= $this->endSection() ?>
