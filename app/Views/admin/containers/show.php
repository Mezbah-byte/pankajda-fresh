<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold"><?= esc($container['container_number']) ?></h4>
        <p class="text-muted small m-0"><?= esc($container['product_name'] ?? '') ?> &middot; <?= esc($container['origin_country'] ?? '') ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/containers/' . $container['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/containers') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Container Details</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4 text-muted">BL Number</dt><dd class="col-sm-8"><?= esc($container['bl_number'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Product</dt><dd class="col-sm-8"><?= esc($container['product_name'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Origin</dt><dd class="col-sm-8"><?= esc($container['origin_country'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Arrival Date</dt><dd class="col-sm-8"><?= esc($container['arrival_date'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Customs Status</dt>
                <dd class="col-sm-8">
                    <?php $cs = $container['customs_status'] ?? 'pending'; ?>
                    <span class="badge <?= $cs === 'cleared' ? 'bg-success' : ($cs === 'held' ? 'bg-danger' : 'bg-warning') ?>"><?= esc(ucfirst($cs)) ?></span>
                    <?php if (! empty($container['customs_clear_date'])): ?> on <?= esc($container['customs_clear_date']) ?><?php endif; ?>
                </dd>
                <dt class="col-sm-4 text-muted">Total Products</dt><dd class="col-sm-8"><?= number_format((float) $container['total_products'], 2) ?> <?= esc($container['unit']) ?></dd>
                <dt class="col-sm-4 text-muted">Damaged</dt><dd class="col-sm-8 text-danger"><?= number_format((float) $container['damaged_products'], 2) ?> <?= esc($container['unit']) ?></dd>
                <dt class="col-sm-4 text-muted">Company</dt><dd class="col-sm-8"><?= esc($company['company_name'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Notes</dt><dd class="col-sm-8"><?= nl2br(esc($container['notes'] ?? '-')) ?></dd>
            </dl>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Cost Breakdown</h6>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Product Cost</span><span>৳ <?= number_format(max(0, (float) $container['cost_total'] - (float) $container['customs_cost'] - (float) $container['transport_cost'] - (float) $container['other_cost']), 0) ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Customs</span><span>৳ <?= number_format((float) $container['customs_cost'], 0) ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Transport</span><span>৳ <?= number_format((float) $container['transport_cost'], 0) ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Other</span><span>৳ <?= number_format((float) $container['other_cost'], 0) ?></span></div>
            <hr>
            <div class="d-flex justify-content-between mb-3"><span class="fw-bold">Total Cost</span><span class="fw-bold">৳ <?= number_format((float) $container['cost_total'], 0) ?></span></div>
            <hr>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Sales (so far)</span><span class="text-success">৳ <?= number_format((float) ($container['total_sold'] ?? 0), 0) ?></span></div>
            <div class="d-flex justify-content-between"><span class="fw-bold">Profit/Loss</span><span class="fw-bold <?= ((float) ($container['profit'] ?? 0)) >= 0 ? 'text-success' : 'text-danger' ?>">৳ <?= number_format((float) ($container['profit'] ?? 0), 0) ?></span></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
