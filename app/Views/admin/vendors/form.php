<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header">
    <div>
        <h4><?= esc($title) ?></h4>
        <ul class="mz-breadcrumb"><li>Accounts</li><li><a href="<?= site_url('admin/vendors') ?>">Vendors</a></li><li><?= esc($title) ?></li></ul>
    </div>
</div>

<?php if (session('errors')): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach (session('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="pd-card">
    <form method="post" action="<?= esc($action) ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Vendor Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="vendor_name" value="<?= esc(old('vendor_name', $vendor['vendor_name'] ?? '')) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Contact Person</label>
                <input type="text" class="form-control" name="contact_person" value="<?= esc(old('contact_person', $vendor['contact_person'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?= esc(old('email', $vendor['email'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" value="<?= esc(old('phone', $vendor['phone'] ?? '')) ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label">Address</label>
                <textarea class="form-control" name="address" rows="2"><?= esc(old('address', $vendor['address'] ?? '')) ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Total Payable (৳)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="total_payable" value="<?= esc(old('total_payable', $vendor['total_payable'] ?? '0')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="active" <?= (old('status', $vendor['status'] ?? 'active')) === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= (old('status', $vendor['status'] ?? '')) === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label">Notes</label>
                <textarea class="form-control" name="notes" rows="2"><?= esc(old('notes', $vendor['notes'] ?? '')) ?></textarea>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Vendor</button>
            <a href="<?= site_url('admin/vendors') ?>" class="btn btn-light">Cancel</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
