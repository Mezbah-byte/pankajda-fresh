<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($container['container_number']) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/containers') ?>" class="text-muted text-decoration-none">Containers</a></li>
            <li><?= esc($container['container_number']) ?></li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/containers/' . $container['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/containers') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <h6 class="fw-semibold mb-4" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Container Details</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4">BL Number</dt><dd class="col-sm-8"><?= esc($container['bl_number'] ?? '-') ?></dd>
                <dt class="col-sm-4">Product</dt><dd class="col-sm-8"><?= esc($container['product_name'] ?? '-') ?></dd>
                <dt class="col-sm-4">Origin</dt><dd class="col-sm-8"><?= esc($container['origin_country'] ?? '-') ?></dd>
                <dt class="col-sm-4">Arrival Date</dt><dd class="col-sm-8"><?= esc($container['arrival_date'] ?? '-') ?></dd>
                <dt class="col-sm-4">Customs Status</dt>
                <dd class="col-sm-8">
                    <?php $cs = $container['customs_status'] ?? 'pending'; ?>
                    <?php if ($cs === 'cleared'): ?>
                        <span class="badge-success-soft">Cleared</span>
                    <?php elseif ($cs === 'held'): ?>
                        <span class="badge-danger-soft">Held</span>
                    <?php else: ?>
                        <span class="badge-warning-soft">Pending</span>
                    <?php endif; ?>
                    <?php if (! empty($container['customs_clear_date'])): ?>
                        <span class="text-muted ms-1" style="font-size:.8rem;">on <?= esc($container['customs_clear_date']) ?></span>
                    <?php endif; ?>
                </dd>
                <dt class="col-sm-4">Total Products</dt><dd class="col-sm-8"><?= number_format((float) $container['total_products'], 2) ?> <?= esc($container['unit']) ?></dd>
                <dt class="col-sm-4">Damaged</dt><dd class="col-sm-8" style="color:#FA896B;"><?= number_format((float) $container['damaged_products'], 2) ?> <?= esc($container['unit']) ?></dd>
                <dt class="col-sm-4">Company</dt><dd class="col-sm-8"><?= esc($company['company_name'] ?? '-') ?></dd>
                <dt class="col-sm-4">Notes</dt><dd class="col-sm-8"><?= nl2br(esc($container['notes'] ?? '-')) ?></dd>
            </dl>
        </div>
    </div>

    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-semibold mb-4" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Cost Breakdown</h6>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Product Cost</span>
                <span>৳ <?= number_format(max(0, (float) $container['cost_total'] - (float) $container['customs_cost'] - (float) $container['transport_cost'] - (float) $container['other_cost']), 0) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Customs</span>
                <span>৳ <?= number_format((float) $container['customs_cost'], 0) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Transport</span>
                <span>৳ <?= number_format((float) $container['transport_cost'], 0) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Other</span>
                <span>৳ <?= number_format((float) $container['other_cost'], 0) ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-4">
                <span class="fw-bold">Total Cost</span>
                <span class="fw-bold">৳ <?= number_format((float) $container['cost_total'], 0) ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Sales (so far)</span>
                <span style="color:#02a98f;">৳ <?= number_format((float) ($container['total_sold'] ?? 0), 0) ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="fw-bold">Profit / Loss</span>
                <span class="fw-bold <?= ((float) ($container['profit'] ?? 0)) >= 0 ? '' : '' ?>"
                      style="color:<?= ((float) ($container['profit'] ?? 0)) >= 0 ? '#02a98f' : '#FA896B' ?>;">
                    ৳ <?= number_format((float) ($container['profit'] ?? 0), 0) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
