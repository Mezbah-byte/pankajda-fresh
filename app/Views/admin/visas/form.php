<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($title) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/visas') ?>" class="text-muted text-decoration-none">Visas</a></li>
            <li><?= esc($title) ?></li>
        </ul>
    </div>
    <a href="<?= site_url('admin/visas') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger mb-4">
        <ul class="mb-0"><?php foreach ((array) $errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= esc($action) ?>">
    <?= csrf_field() ?>
    <div class="pd-card">
        <h6 class="fw-bold mb-4" style="color:var(--mz-text-primary);">Visa Details</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Visa Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="visa_name" value="<?= esc(old('visa_name', $visa['visa_name'] ?? '')) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Company <span class="text-danger">*</span></label>
                <select name="company_un_id" class="form-select" required>
                    <option value="">Select company…</option>
                    <?php foreach (($companies ?? []) as $c): ?>
                        <option value="<?= esc($c['un_id']) ?>" <?= old('company_un_id', $visa['company_un_id'] ?? '') === $c['un_id'] ? 'selected' : '' ?>><?= esc($c['company_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Visa Number</label>
                <input type="text" class="form-control" name="visa_number" value="<?= esc(old('visa_number', $visa['visa_number'] ?? '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Country</label>
                <input type="text" class="form-control" name="country" value="<?= esc(old('country', $visa['country'] ?? '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Category</label>
                <input type="text" class="form-control" name="category" placeholder="Work, Tourist, Business…" value="<?= esc(old('category', $visa['category'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Beneficiary Name</label>
                <input type="text" class="form-control" name="beneficiary_name" value="<?= esc(old('beneficiary_name', $visa['beneficiary_name'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Passport No</label>
                <input type="text" class="form-control" name="passport_no" value="<?= esc(old('passport_no', $visa['passport_no'] ?? '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Visa Cost (৳)</label>
                <input type="number" step="0.01" class="form-control" name="visa_cost" value="<?= esc(old('visa_cost', $visa['visa_cost'] ?? 0)) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Paid Amount (৳)</label>
                <input type="number" step="0.01" class="form-control" name="paid_amount" value="<?= esc(old('paid_amount', $visa['paid_amount'] ?? 0)) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['active','expired','cancelled'] as $st): ?>
                        <option value="<?= $st ?>" <?= old('status', $visa['status'] ?? 'active') === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Issue Date</label>
                <input type="date" class="form-control" name="visa_issue_date" value="<?= esc(old('visa_issue_date', $visa['visa_issue_date'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Expiry Date</label>
                <input type="date" class="form-control" name="visa_expiry_date" value="<?= esc(old('visa_expiry_date', $visa['visa_expiry_date'] ?? '')) ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea class="form-control" name="notes" rows="3"><?= esc(old('notes', $visa['notes'] ?? '')) ?></textarea>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save Visa</button>
            <a href="<?= site_url('admin/visas') ?>" class="btn btn-light">Cancel</a>
        </div>
    </div>
</form>

<?= $this->endSection() ?>
