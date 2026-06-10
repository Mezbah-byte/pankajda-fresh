<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($title) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Admin</li>
            <li><a href="<?= site_url('admin/countries') ?>" class="text-muted text-decoration-none">Countries</a></li>
            <li><?= esc($country['name']) ?></li>
        </ul>
    </div>
    <a href="<?= site_url('admin/countries') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger mb-3"><ul class="mb-0"><?php foreach ((array)$errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="pd-card" style="max-width:480px;">
    <h6 class="fw-bold mb-4">Edit Country</h6>
    <form method="post" action="<?= site_url('admin/countries/' . $country['un_id']) ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label fw-semibold">Country Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name"
                   value="<?= esc(old('name', $country['name'])) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">ISO Code</label>
            <input type="text" class="form-control" name="iso_code" maxlength="3"
                   value="<?= esc(old('iso_code', $country['iso_code'] ?? '')) ?>"
                   style="text-transform:uppercase;" placeholder="BD, SA…">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Sort Order</label>
            <input type="number" class="form-control" name="sort_order"
                   value="<?= esc(old('sort_order', $country['sort_order'])) ?>" min="0">
        </div>
        <div class="mb-4 form-check">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                   <?= old('is_active', $country['is_active']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save</button>
            <a href="<?= site_url('admin/countries') ?>" class="btn btn-light">Cancel</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
