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

    <!-- Basic Info -->
    <div class="pd-card mb-4">
        <h6 class="fw-bold mb-4 d-flex align-items-center gap-2" style="color:var(--mz-text-primary);">
            <i class="bi bi-passport text-primary"></i> Visa Information
        </h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Visa Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="visa_name" value="<?= esc(old('visa_name', $visa['visa_name'] ?? '')) ?>" required placeholder="e.g. Saudi Work Visa">
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
                <label class="form-label fw-semibold">Origin Country <small class="text-muted">(From)</small></label>
                <select name="from_country" class="form-select">
                    <option value="">-- Select origin --</option>
                    <?php $selFrom = old('from_country', $visa['from_country'] ?? ''); ?>
                    <?php foreach (($countries ?? []) as $cn): ?>
                        <option value="<?= esc($cn) ?>" <?= $selFrom === $cn ? 'selected' : '' ?>><?= esc($cn) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Destination Country <small class="text-muted">(To)</small></label>
                <select name="country" class="form-select">
                    <option value="">-- Select destination --</option>
                    <?php $selTo = old('country', $visa['country'] ?? ''); ?>
                    <?php foreach (($countries ?? []) as $cn): ?>
                        <option value="<?= esc($cn) ?>" <?= $selTo === $cn ? 'selected' : '' ?>><?= esc($cn) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Category / Type</label>
                <input type="text" class="form-control" name="category" placeholder="Work, Tourist, Business…" value="<?= esc(old('category', $visa['category'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Beneficiary Name</label>
                <input type="text" class="form-control" name="beneficiary_name" value="<?= esc(old('beneficiary_name', $visa['beneficiary_name'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Passport No</label>
                <input type="text" class="form-control" name="passport_no" value="<?= esc(old('passport_no', $visa['passport_no'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Visa Number</label>
                <input type="text" class="form-control" name="visa_number" value="<?= esc(old('visa_number', $visa['visa_number'] ?? '')) ?>" placeholder="VN-XXXXXXXX">
            </div>
        </div>
    </div>

    <!-- Dates & Status -->
    <div class="pd-card mb-4">
        <h6 class="fw-bold mb-4 d-flex align-items-center gap-2" style="color:var(--mz-text-primary);">
            <i class="bi bi-calendar-range text-warning"></i> Dates & Status
        </h6>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Visa Issue Date</label>
                <input type="date" class="form-control" name="visa_issue_date" value="<?= esc(old('visa_issue_date', $visa['visa_issue_date'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Visa Expiry Date</label>
                <input type="date" class="form-control" name="visa_expiry_date" value="<?= esc(old('visa_expiry_date', $visa['visa_expiry_date'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Work Permit Number</label>
                <input type="text" class="form-control" name="work_permit_number" value="<?= esc(old('work_permit_number', $visa['work_permit_number'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['active','expired','cancelled'] as $st): ?>
                        <option value="<?= $st ?>" <?= old('status', $visa['status'] ?? 'active') === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Work Permit Issue Date</label>
                <input type="date" class="form-control" name="work_permit_issue_date" value="<?= esc(old('work_permit_issue_date', $visa['work_permit_issue_date'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Work Permit Expiry Date</label>
                <input type="date" class="form-control" name="work_permit_expiry_date" value="<?= esc(old('work_permit_expiry_date', $visa['work_permit_expiry_date'] ?? '')) ?>">
            </div>
        </div>
    </div>

    <!-- Financial -->
    <div class="pd-card mb-4">
        <h6 class="fw-bold mb-4 d-flex align-items-center gap-2" style="color:var(--mz-text-primary);">
            <i class="bi bi-currency-dollar text-success"></i> Financial Details
        </h6>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Visa Cost (৳) <small class="text-muted">Cost to us</small></label>
                <input type="number" step="0.01" min="0" class="form-control" name="purchase_price" id="purchase_price" value="<?= esc(old('purchase_price', $visa['purchase_price'] ?? 0)) ?>" oninput="calcProfit()">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Selling Price (৳) <small class="text-muted">Charged to client</small></label>
                <input type="number" step="0.01" min="0" class="form-control" name="selling_price" id="selling_price" value="<?= esc(old('selling_price', $visa['selling_price'] ?? 0)) ?>" oninput="calcProfit()">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Visa Customer Payment (৳) <small class="text-muted">Charged to client</small></label>
                <input type="number" step="0.01" min="0" class="form-control" name="visa_cost" value="<?= esc(old('visa_cost', $visa['visa_cost'] ?? 0)) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Paid Amount (৳)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="paid_amount" value="<?= esc(old('paid_amount', $visa['paid_amount'] ?? 0)) ?>">
            </div>
        </div>
        <div class="mt-3 p-3 rounded" style="background:rgba(var(--mz-accent-rgb,25,135,84),0.07); border:1px solid rgba(var(--mz-accent-rgb,25,135,84),0.2);">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-graph-up-arrow text-success fs-5"></i>
                <div>
                    <small class="text-muted d-block">Estimated Profit</small>
                    <span class="fw-bold fs-5" id="profit_display" style="color:var(--mz-success,#198754);">৳ 0.00</span>
                </div>
                <small class="text-muted ms-2">(Selling Price − Visa Cost − Extra Costs)</small>
            </div>
        </div>
    </div>

    <!-- Notes -->
    <div class="pd-card mb-4">
        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color:var(--mz-text-primary);">
            <i class="bi bi-sticky text-info"></i> Notes
        </h6>
        <textarea class="form-control" name="notes" rows="3" placeholder="Any additional notes…"><?= esc(old('notes', $visa['notes'] ?? '')) ?></textarea>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-4"><i class="bi bi-check-circle me-2"></i>Save Visa</button>
        <a href="<?= site_url('admin/visas') ?>" class="btn btn-light">Cancel</a>
    </div>
</form>

<script>
function calcProfit() {
    const buy  = parseFloat(document.getElementById('purchase_price').value) || 0;
    const sell = parseFloat(document.getElementById('selling_price').value)  || 0;
    const profit = sell - buy;
    const el = document.getElementById('profit_display');
    el.textContent = '৳ ' + profit.toLocaleString('en-BD', {minimumFractionDigits:2, maximumFractionDigits:2});
    el.style.color = profit >= 0 ? 'var(--mz-success,#198754)' : 'var(--mz-danger,#dc3545)';
}
document.addEventListener('DOMContentLoaded', calcProfit);
</script>

<?= $this->endSection() ?>
