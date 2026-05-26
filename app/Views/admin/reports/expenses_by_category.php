<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Expenses by Category</h4>
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

    <div class="row">
        <div class="col-md-7">
            <table class="table align-middle">
                <thead><tr><th>Category</th><th class="text-end">Entries</th><th class="text-end">Total</th></tr></thead>
                <tbody>
                    <?php foreach ($data['rows'] as $r): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark"><?= esc(ucfirst($r['category'])) ?></span></td>
                            <td class="text-end"><?= number_format((int) $r['count']) ?></td>
                            <td class="text-end fw-semibold text-danger">৳ <?= number_format((float) $r['total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-light fw-bold">
                        <td>Total</td>
                        <td class="text-end"><?= number_format((int) $data['totals']['count']) ?></td>
                        <td class="text-end">৳ <?= number_format((float) $data['totals']['total'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
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
            backgroundColor: ['#5e60ce','#2ec4b6','#ff5858','#f7971e','#11998e','#ee0979','#00c6ff','#7209b7','#06d6a0']
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
</script>
<?= $this->endSection() ?>
