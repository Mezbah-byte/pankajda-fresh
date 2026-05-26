<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Daily Sales</h4>
        <ul class="mz-breadcrumb">
            <li><a href="<?= site_url('admin/reports') ?>" class="text-muted text-decoration-none">Reports</a></li>
            <li>Daily Sales</li>
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

    <!-- Summary stat cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="pd-stat gradient-1">
                <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                <div class="stat-label">Invoices</div>
                <div class="stat-value"><?= number_format($data['totals']['invoices']) ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="pd-stat gradient-2">
                <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
                <div class="stat-label">Total</div>
                <div class="stat-value" style="font-size:1.3rem;">৳ <?= number_format($data['totals']['total'], 0) ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="pd-stat gradient-3">
                <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                <div class="stat-label">Paid</div>
                <div class="stat-value" style="font-size:1.3rem;">৳ <?= number_format($data['totals']['paid'], 0) ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="pd-stat gradient-4">
                <div class="stat-icon"><i class="bi bi-exclamation-circle"></i></div>
                <div class="stat-label">Due</div>
                <div class="stat-value" style="font-size:1.3rem;">৳ <?= number_format($data['totals']['due'], 0) ?></div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr><th>Date</th><th class="text-end">Invoices</th><th class="text-end">Total</th><th class="text-end">Paid</th><th class="text-end">Due</th></tr>
            </thead>
            <tbody>
                <?php foreach ($data['rows'] as $r): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($r['sale_date']) ?></td>
                        <td class="text-end"><?= number_format((int) $r['invoices']) ?></td>
                        <td class="text-end fw-semibold">৳ <?= number_format((float) $r['total'], 2) ?></td>
                        <td class="text-end" style="color:#02a98f;">৳ <?= number_format((float) $r['paid'], 2) ?></td>
                        <td class="text-end" style="color:#FA896B;">৳ <?= number_format((float) $r['due'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($data['rows'])): ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">No sales in this range.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
