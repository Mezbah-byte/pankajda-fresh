<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($title) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Operations</li>
            <li><a href="<?= site_url('admin/expenses') ?>" class="text-muted text-decoration-none">Expenses</a></li>
            <li><?= esc($title) ?></li>
        </ul>
    </div>
    <a href="<?= site_url('admin/expenses') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger mb-4">
        <ul class="mb-0"><?php foreach ((array) $errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= esc($action) ?>">
    <?= csrf_field() ?>
    <div class="pd-card">
        <h6 class="fw-bold mb-4" style="color:var(--mz-text-primary);">Expense Details</h6>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Expense Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="expense_title" value="<?= esc(old('expense_title', $expense['expense_title'] ?? '')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Amount (৳) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" name="amount" value="<?= esc(old('amount', $expense['amount'] ?? 0)) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="expense_date" value="<?= esc(old('expense_date', $expense['expense_date'] ?? date('Y-m-d'))) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Category</label>
                <input type="text" class="form-control" name="category" list="catList" value="<?= esc(old('category', $expense['category'] ?? 'office')) ?>">
                <datalist id="catList">
                    <?php foreach (($categories ?? []) as $cat): ?>
                        <option value="<?= esc($cat) ?>"></option>
                    <?php endforeach; ?>
                    <option value="office">
                    <option value="utilities">
                    <option value="transport">
                    <option value="marketing">
                    <option value="customs">
                    <option value="banking">
                </datalist>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Payment Method</label>
                <select name="payment_method" class="form-select">
                    <?php foreach (['cash','bank_transfer','cheque','mfs','card'] as $m): ?>
                        <option value="<?= $m ?>" <?= old('payment_method', $expense['payment_method'] ?? 'cash') === $m ? 'selected' : '' ?>><?= esc(str_replace('_',' ', ucfirst($m))) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Reference</label>
                <input type="text" class="form-control" name="reference_no" value="<?= esc(old('reference_no', $expense['reference_no'] ?? '')) ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold">Company</label>
                <select name="company_un_id" class="form-select">
                    <option value="">— None —</option>
                    <?php foreach (($companies ?? []) as $cm): ?>
                        <option value="<?= esc($cm['un_id']) ?>" <?= old('company_un_id', $expense['company_un_id'] ?? '') === $cm['un_id'] ? 'selected' : '' ?>><?= esc($cm['company_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea class="form-control" name="notes" rows="3"><?= esc(old('notes', $expense['notes'] ?? '')) ?></textarea>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save Expense</button>
            <a href="<?= site_url('admin/expenses') ?>" class="btn btn-light">Cancel</a>
        </div>
    </div>
</form>

<?= $this->endSection() ?>
