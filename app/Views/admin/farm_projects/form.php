<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0 fw-bold"><?= esc($title) ?></h4>
    <a href="<?= site_url('admin/farm-projects') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach ((array) $errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form method="post" action="<?= esc($action) ?>" class="pd-card">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="project_name" value="<?= esc(old('project_name', $project['project_name'] ?? '')) ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Crop Name</label>
            <input type="text" class="form-control" name="crop_name" value="<?= esc(old('crop_name', $project['crop_name'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
                <?php foreach (['active','completed','cancelled'] as $st): ?>
                    <option value="<?= $st ?>" <?= old('status', $project['status'] ?? 'active') === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Company</label>
            <select name="company_un_id" class="form-select">
                <option value="">— None —</option>
                <?php foreach (($companies ?? []) as $cm): ?>
                    <option value="<?= esc($cm['un_id']) ?>" <?= old('company_un_id', $project['company_un_id'] ?? '') === $cm['un_id'] ? 'selected' : '' ?>><?= esc($cm['company_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Land Size</label>
            <input type="number" step="0.01" class="form-control" name="land_size" value="<?= esc(old('land_size', $project['land_size'] ?? 0)) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Unit</label>
            <input type="text" class="form-control" name="land_unit" value="<?= esc(old('land_unit', $project['land_unit'] ?? 'acre')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Start Date</label>
            <input type="date" class="form-control" name="start_date" value="<?= esc(old('start_date', $project['start_date'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">End Date</label>
            <input type="date" class="form-control" name="end_date" value="<?= esc(old('end_date', $project['end_date'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Total Cost (৳)</label>
            <input type="number" step="0.01" class="form-control" name="total_cost" value="<?= esc(old('total_cost', $project['total_cost'] ?? 0)) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Production</label>
            <input type="number" step="0.01" class="form-control" name="production_amount" value="<?= esc(old('production_amount', $project['production_amount'] ?? 0)) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Production Unit</label>
            <input type="text" class="form-control" name="production_unit" value="<?= esc(old('production_unit', $project['production_unit'] ?? 'kg')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Sale Amount (৳)</label>
            <input type="number" step="0.01" class="form-control" name="sale_amount" value="<?= esc(old('sale_amount', $project['sale_amount'] ?? 0)) ?>">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold">Notes</label>
            <textarea class="form-control" name="notes" rows="3"><?= esc(old('notes', $project['notes'] ?? '')) ?></textarea>
        </div>
    </div>
    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save Project</button>
        <a href="<?= site_url('admin/farm-projects') ?>" class="btn btn-light">Cancel</a>
    </div>
</form>

<?= $this->endSection() ?>
