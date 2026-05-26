<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($title ?? 'Company') ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/companies') ?>" class="text-muted text-decoration-none">Companies</a></li>
            <li><?= esc($title ?? 'Form') ?></li>
        </ul>
    </div>
    <a href="<?= site_url('admin/companies') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger mb-4">
        <ul class="mb-0"><?php foreach ((array) $errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= esc($action) ?>">
    <?= csrf_field() ?>
    <div class="pd-card">
        <h6 class="fw-bold mb-4" style="color:var(--mz-text-primary);">Company Details</h6>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Company Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="company_name" value="<?= esc(old('company_name', $company['company_name'] ?? '')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['active','inactive','pending'] as $st): ?>
                        <option value="<?= $st ?>" <?= old('status', $company['status'] ?? 'active') === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Company Type</label>
                <input type="text" class="form-control" name="company_type" placeholder="Trading / Import / Farm / Service" value="<?= esc(old('company_type', $company['company_type'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Trade License</label>
                <input type="text" class="form-control" name="trade_license" value="<?= esc(old('trade_license', $company['trade_license'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Phone</label>
                <input type="text" class="form-control" name="phone" value="<?= esc(old('phone', $company['phone'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" name="email" value="<?= esc(old('email', $company['email'] ?? '')) ?>">
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Address</label>
                <input type="text" class="form-control" name="address" value="<?= esc(old('address', $company['address'] ?? '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">City</label>
                <input type="text" class="form-control" name="city" value="<?= esc(old('city', $company['city'] ?? '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Country</label>
                <input type="text" class="form-control" name="country" value="<?= esc(old('country', $company['country'] ?? 'Bangladesh')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Currency</label>
                <input type="text" class="form-control" name="currency" value="<?= esc(old('currency', $company['currency'] ?? 'BDT')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Opening Balance</label>
                <input type="number" step="0.01" class="form-control" name="opening_balance" value="<?= esc(old('opening_balance', $company['opening_balance'] ?? 0)) ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea class="form-control" name="notes" rows="3"><?= esc(old('notes', $company['notes'] ?? '')) ?></textarea>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save Company</button>
            <a href="<?= site_url('admin/companies') ?>" class="btn btn-light">Cancel</a>
        </div>
    </div>
</form>

<?= $this->endSection() ?>
