<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Profit &amp; Loss</h4>
        <p class="text-muted small m-0">From <?= esc($data['from']) ?> to <?= esc($data['to']) ?></p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-light" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
        <a href="<?= site_url('admin/reports') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-3"><label class="form-label small text-muted mb-1">From</label><input type="date" class="form-control" name="from" value="<?= esc($data['from']) ?>"></div>
        <div class="col-md-3"><label class="form-label small text-muted mb-1">To</label><input type="date" class="form-control" name="to" value="<?= esc($data['to']) ?>"></div>
        <div class="col-md-3 d-flex align-items-end"><button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Apply</button></div>
    </form>

    <div class="row g-3">
        <div class="col-md-8">
            <table class="table align-middle">
                <tbody>
                    <tr><td class="text-muted">Sales Revenue</td><td class="text-end fs-5 text-success">+ ৳ <?= number_format($data['sales'], 2) ?></td></tr>
                    <tr><td class="text-muted">Farm Project Profit</td><td class="text-end fs-5 <?= $data['farm_profit'] >= 0 ? 'text-success' : 'text-danger' ?>"><?= $data['farm_profit'] >= 0 ? '+' : '−' ?> ৳ <?= number_format(abs($data['farm_profit']), 2) ?></td></tr>
                    <tr><td class="text-muted">Container Costs</td><td class="text-end fs-5 text-danger">− ৳ <?= number_format($data['container_cost'], 2) ?></td></tr>
                    <tr><td class="text-muted">Operating Expenses</td><td class="text-end fs-5 text-danger">− ৳ <?= number_format($data['expenses'], 2) ?></td></tr>
                    <tr class="table-light fw-bold" style="border-top:2px solid #1f1d3a;">
                        <td class="fs-5">Net Profit</td>
                        <td class="text-end fs-3 <?= $data['net_profit'] >= 0 ? 'text-success' : 'text-danger' ?>">৳ <?= number_format($data['net_profit'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-4">
            <div class="pd-card" style="background:linear-gradient(135deg,#5e60ce,#6930c3);color:#fff;text-align:center;padding:30px;">
                <div class="small opacity-75 mb-2">Bottom Line</div>
                <div style="font-size:2.2rem;font-weight:800;"><?= $data['net_profit'] >= 0 ? '+' : '' ?>৳ <?= number_format($data['net_profit'], 0) ?></div>
                <div class="small opacity-75 mt-2"><?= $data['net_profit'] >= 0 ? 'Profitable period' : 'Loss this period' ?></div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
