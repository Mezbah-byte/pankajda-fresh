<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($title) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/customers') ?>" class="text-muted text-decoration-none">Customers</a></li>
            <li><?= esc($title) ?></li>
        </ul>
    </div>
    <a href="<?= site_url('admin/customers') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger mb-4">
        <ul class="mb-0"><?php foreach ((array) $errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= esc($action) ?>">
    <?= csrf_field() ?>
    <div class="pd-card">
        <h6 class="fw-bold mb-4" style="color:var(--mz-text-primary);">Customer Profile</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="customer_name" value="<?= esc(old('customer_name', $customer['customer_name'] ?? '')) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Customer Code</label>
                <input type="text" class="form-control" name="customer_code" placeholder="Auto/manual" value="<?= esc(old('customer_code', $customer['customer_code'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['active','inactive'] as $st): ?>
                        <option value="<?= $st ?>" <?= old('status', $customer['status'] ?? 'active') === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Company</label>
                <select name="company_un_id" class="form-select">
                    <option value="">— None —</option>
                    <?php foreach (($companies ?? []) as $cm): ?>
                        <option value="<?= esc($cm['un_id']) ?>" <?= old('company_un_id', $customer['company_un_id'] ?? '') === $cm['un_id'] ? 'selected' : '' ?>><?= esc($cm['company_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Phone</label>
                <input type="text" class="form-control" name="phone" value="<?= esc(old('phone', $customer['phone'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" name="email" value="<?= esc(old('email', $customer['email'] ?? '')) ?>">
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Address</label>
                <input type="text" class="form-control" name="address" value="<?= esc(old('address', $customer['address'] ?? '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">City</label>
                <input type="text" class="form-control" name="city" value="<?= esc(old('city', $customer['city'] ?? '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Opening Balance (৳)</label>
                <input type="number" step="0.01" class="form-control" name="opening_balance" value="<?= esc(old('opening_balance', $customer['opening_balance'] ?? 0)) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Credit Limit (৳)</label>
                <input type="number" step="0.01" class="form-control" name="credit_limit" value="<?= esc(old('credit_limit', $customer['credit_limit'] ?? 0)) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Current Due (৳)</label>
                <input type="number" step="0.01" class="form-control" value="<?= number_format((float) ($customer['current_due'] ?? 0), 2, '.', '') ?>" disabled>
                <div class="form-text text-muted">Auto-managed by sales &amp; payments</div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea class="form-control" name="notes" rows="3"><?= esc(old('notes', $customer['notes'] ?? '')) ?></textarea>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save Customer</button>
            <a href="<?= site_url('admin/customers') ?>" class="btn btn-light">Cancel</a>
        </div>
    </div>
</form>

<?= $this->endSection() ?>
