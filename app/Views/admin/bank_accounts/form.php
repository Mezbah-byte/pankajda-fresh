<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header">
    <div>
        <h4><?= esc($title) ?></h4>
        <ul class="mz-breadcrumb"><li>Finance</li><li><a href="<?= site_url('admin/bank-accounts') ?>">Bank Accounts</a></li><li><?= esc($title) ?></li></ul>
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
                <label class="form-label">Account Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="account_name" value="<?= esc(old('account_name', $account['account_name'] ?? '')) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="bank_name" value="<?= esc(old('bank_name', $account['bank_name'] ?? '')) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Account Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="account_number" value="<?= esc(old('account_number', $account['account_number'] ?? '')) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Branch</label>
                <input type="text" class="form-control" name="branch" value="<?= esc(old('branch', $account['branch'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Routing Number</label>
                <input type="text" class="form-control" name="routing_number" value="<?= esc(old('routing_number', $account['routing_number'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Currency</label>
                <select class="form-select" name="currency">
                    <option value="BDT" <?= (old('currency', $account['currency']??'BDT'))==='BDT'?'selected':'' ?>>BDT (Taka)</option>
                    <option value="USD" <?= (old('currency', $account['currency']??''))==='USD'?'selected':'' ?>>USD</option>
                    <option value="EUR" <?= (old('currency', $account['currency']??''))==='EUR'?'selected':'' ?>>EUR</option>
                </select>
            </div>
            <?php if (! $account): ?>
            <div class="col-md-6">
                <label class="form-label">Opening Balance (৳)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="balance" value="<?= esc(old('balance', '0')) ?>">
            </div>
            <?php endif; ?>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="active" <?= (old('status', $account['status']??'active'))==='active'?'selected':'' ?>>Active</option>
                    <option value="inactive" <?= (old('status', $account['status']??''))==='inactive'?'selected':'' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea class="form-control" name="notes" rows="2"><?= esc(old('notes', $account['notes'] ?? '')) ?></textarea>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Account</button>
            <a href="<?= site_url('admin/bank-accounts') ?>" class="btn btn-light">Cancel</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
