<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0 fw-bold"><?= esc($title) ?></h4>
    <a href="<?= site_url('admin/employees') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach ((array) $errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form method="post" action="<?= esc($action) ?>" class="pd-card">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" value="<?= esc(old('name', $employee['name'] ?? '')) ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Employee Code</label>
            <input type="text" class="form-control" name="employee_code" value="<?= esc(old('employee_code', $employee['employee_code'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
                <?php foreach (['active','inactive'] as $st): ?>
                    <option value="<?= $st ?>" <?= old('status', $employee['status'] ?? 'active') === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Company</label>
            <select name="company_un_id" class="form-select">
                <option value="">— None —</option>
                <?php foreach (($companies ?? []) as $cm): ?>
                    <option value="<?= esc($cm['un_id']) ?>" <?= old('company_un_id', $employee['company_un_id'] ?? '') === $cm['un_id'] ? 'selected' : '' ?>><?= esc($cm['company_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Designation</label>
            <input type="text" class="form-control" name="designation" value="<?= esc(old('designation', $employee['designation'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Department</label>
            <input type="text" class="form-control" name="department" value="<?= esc(old('department', $employee['department'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Phone</label>
            <input type="text" class="form-control" name="phone" value="<?= esc(old('phone', $employee['phone'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" class="form-control" name="email" value="<?= esc(old('email', $employee['email'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">National ID</label>
            <input type="text" class="form-control" name="national_id" value="<?= esc(old('national_id', $employee['national_id'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Salary (৳)</label>
            <input type="number" step="0.01" class="form-control" name="salary" value="<?= esc(old('salary', $employee['salary'] ?? 0)) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Joined Date</label>
            <input type="date" class="form-control" name="joined_at" value="<?= esc(old('joined_at', $employee['joined_at'] ?? '')) ?>">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold">Address</label>
            <input type="text" class="form-control" name="address" value="<?= esc(old('address', $employee['address'] ?? '')) ?>">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold">Notes</label>
            <textarea class="form-control" name="notes" rows="3"><?= esc(old('notes', $employee['notes'] ?? '')) ?></textarea>
        </div>
    </div>
    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save Employee</button>
        <a href="<?= site_url('admin/employees') ?>" class="btn btn-light">Cancel</a>
    </div>
</form>

<?= $this->endSection() ?>
