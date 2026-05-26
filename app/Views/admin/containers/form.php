<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($title) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/containers') ?>" class="text-muted text-decoration-none">Containers</a></li>
            <li><?= esc($title) ?></li>
        </ul>
    </div>
    <a href="<?= site_url('admin/containers') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger mb-4">
        <ul class="mb-0"><?php foreach ((array) $errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= esc($action) ?>">
    <?= csrf_field() ?>
    <div class="pd-card">
        <h6 class="fw-bold mb-4" style="color:var(--mz-text-primary);">Container Details</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Container Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="container_number" value="<?= esc(old('container_number', $container['container_number'] ?? '')) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">BL Number</label>
                <input type="text" class="form-control" name="bl_number" value="<?= esc(old('bl_number', $container['bl_number'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Company</label>
                <select name="company_un_id" class="form-select">
                    <option value="">— None —</option>
                    <?php foreach (($companies ?? []) as $cm): ?>
                        <option value="<?= esc($cm['un_id']) ?>" <?= old('company_un_id', $container['company_un_id'] ?? '') === $cm['un_id'] ? 'selected' : '' ?>><?= esc($cm['company_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Product</label>
                <input type="text" class="form-control" name="product_name" value="<?= esc(old('product_name', $container['product_name'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Origin Country</label>
                <input type="text" class="form-control" name="origin_country" value="<?= esc(old('origin_country', $container['origin_country'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Arrival Date</label>
                <input type="date" class="form-control" name="arrival_date" value="<?= esc(old('arrival_date', $container['arrival_date'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Customs Status</label>
                <select name="customs_status" class="form-select">
                    <?php foreach (['pending','cleared','held'] as $cs): ?>
                        <option value="<?= $cs ?>" <?= old('customs_status', $container['customs_status'] ?? 'pending') === $cs ? 'selected' : '' ?>><?= ucfirst($cs) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Customs Clear Date</label>
                <input type="date" class="form-control" name="customs_clear_date" value="<?= esc(old('customs_clear_date', $container['customs_clear_date'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Total Products</label>
                <input type="number" step="0.01" class="form-control" name="total_products" value="<?= esc(old('total_products', $container['total_products'] ?? 0)) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Damaged</label>
                <input type="number" step="0.01" class="form-control" name="damaged_products" value="<?= esc(old('damaged_products', $container['damaged_products'] ?? 0)) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Unit</label>
                <input type="text" class="form-control" name="unit" value="<?= esc(old('unit', $container['unit'] ?? 'kg')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['in_transit','received','sold','closed'] as $st): ?>
                        <option value="<?= $st ?>" <?= old('status', $container['status'] ?? 'in_transit') === $st ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$st)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Total Cost (৳)</label>
                <input type="number" step="0.01" class="form-control" name="cost_total" value="<?= esc(old('cost_total', $container['cost_total'] ?? 0)) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Customs Cost (৳)</label>
                <input type="number" step="0.01" class="form-control" name="customs_cost" value="<?= esc(old('customs_cost', $container['customs_cost'] ?? 0)) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Transport Cost (৳)</label>
                <input type="number" step="0.01" class="form-control" name="transport_cost" value="<?= esc(old('transport_cost', $container['transport_cost'] ?? 0)) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Other Cost (৳)</label>
                <input type="number" step="0.01" class="form-control" name="other_cost" value="<?= esc(old('other_cost', $container['other_cost'] ?? 0)) ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea class="form-control" name="notes" rows="3"><?= esc(old('notes', $container['notes'] ?? '')) ?></textarea>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save Container</button>
            <a href="<?= site_url('admin/containers') ?>" class="btn btn-light">Cancel</a>
        </div>
    </div>
</form>

<?= $this->endSection() ?>
