<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Profit &amp; Loss</h4>
        <ul class="mz-breadcrumb">
            <li><a href="<?= site_url('admin/reports') ?>" class="text-muted text-decoration-none">Reports</a></li>
            <li>Profit &amp; Loss</li>
        </ul>
    </div>
    <div class="d-flex gap-2">
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
        <div class="col-md-8">
            <table class="table align-middle" style="font-size:.9rem;">
                <tbody>
                    <tr>
                        <td class="text-muted py-3">Sales Revenue</td>
                        <td class="text-end py-3" style="font-size:1.1rem;font-weight:600;color:#02a98f;">+ ৳ <?= number_format($data['sales'], 2) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-3">Farm Project Profit</td>
                        <td class="text-end py-3" style="font-size:1.1rem;font-weight:600;color:<?= $data['farm_profit'] >= 0 ? '#02a98f' : '#FA896B' ?>;">
                            <?= $data['farm_profit'] >= 0 ? '+' : '−' ?> ৳ <?= number_format(abs($data['farm_profit']), 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted py-3">Container Costs</td>
                        <td class="text-end py-3" style="font-size:1.1rem;font-weight:600;color:#FA896B;">− ৳ <?= number_format($data['container_cost'], 2) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-3">Operating Expenses</td>
                        <td class="text-end py-3" style="font-size:1.1rem;font-weight:600;color:#FA896B;">− ৳ <?= number_format($data['expenses'], 2) ?></td>
                    </tr>
                    <tr style="background:#F9FAFB;border-top:2px solid var(--mz-border);">
                        <td class="py-4 fw-bold" style="font-size:1.05rem;">Net Profit</td>
                        <td class="text-end py-4 fw-bold" style="font-size:1.6rem;color:<?= $data['net_profit'] >= 0 ? '#02a98f' : '#FA896B' ?>;">
                            ৳ <?= number_format($data['net_profit'], 2) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="col-md-4">
            <div class="rounded-3 text-center p-5" style="background:linear-gradient(135deg,<?= $data['net_profit'] >= 0 ? '#13DEB9,#02a98f' : '#FA896B,#e85347' ?>);color:#fff;">
                <div style="font-size:.85rem;opacity:.8;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">Bottom Line</div>
                <div style="font-size:2.2rem;font-weight:800;line-height:1.2;">
                    <?= $data['net_profit'] >= 0 ? '+' : '' ?>৳ <?= number_format($data['net_profit'], 0) ?>
                </div>
                <div style="font-size:.85rem;opacity:.8;margin-top:8px;">
                    <?= $data['net_profit'] >= 0 ? 'Profitable period' : 'Loss this period' ?>
                </div>
            </div>
            <div class="mt-3 text-muted text-center" style="font-size:.78rem;">
                <?= esc($data['from']) ?> — <?= esc($data['to']) ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
