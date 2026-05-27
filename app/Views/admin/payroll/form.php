<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header">
    <div>
        <h4><?= esc($title) ?></h4>
        <ul class="mz-breadcrumb"><li>HR</li><li><a href="<?= site_url('admin/payroll') ?>">Payroll</a></li><li><?= esc($title) ?></li></ul>
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
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select class="form-select" name="employee_un_id" required>
                    <option value="">Select Employee</option>
                    <?php foreach (($employees ?? []) as $emp): ?>
                        <option value="<?= esc($emp['un_id']) ?>" <?= old('employee_un_id')===$emp['un_id']?'selected':'' ?>><?= esc($emp['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Pay Period <span class="text-danger">*</span></label>
                <input type="month" class="form-control" name="pay_period" value="<?= esc(old('pay_period', date('Y-m'))) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Company</label>
                <select class="form-select" name="company_un_id">
                    <option value="">Select Company</option>
                    <?php foreach (($companies ?? []) as $co): ?>
                        <option value="<?= esc($co['un_id']) ?>" <?= old('company_un_id')===$co['un_id']?'selected':'' ?>><?= esc($co['company_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12"><hr class="my-1"><small class="text-muted text-uppercase fw-semibold" style="letter-spacing:.5px;">Earnings</small></div>

            <div class="col-md-4">
                <label class="form-label">Basic Salary (৳) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" class="form-control" name="basic_salary" value="<?= esc(old('basic_salary', '')) ?>" required id="basicSalary">
            </div>
            <div class="col-md-4">
                <label class="form-label">House Allowance (৳)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="house_allowance" value="<?= esc(old('house_allowance', '0')) ?>" id="houseAllowance">
            </div>
            <div class="col-md-4">
                <label class="form-label">Transport Allowance (৳)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="transport_allowance" value="<?= esc(old('transport_allowance', '0')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Medical Allowance (৳)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="medical_allowance" value="<?= esc(old('medical_allowance', '0')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Other Allowances (৳)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="other_allowances" value="<?= esc(old('other_allowances', '0')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Overtime Pay (৳)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="overtime_pay" value="<?= esc(old('overtime_pay', '0')) ?>">
            </div>

            <div class="col-12"><hr class="my-1"><small class="text-muted text-uppercase fw-semibold" style="letter-spacing:.5px;">Deductions</small></div>

            <div class="col-md-4">
                <label class="form-label">Advance Deduction (৳)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="advance_deduction" value="<?= esc(old('advance_deduction', '0')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Tax Deduction (৳)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="tax_deduction" value="<?= esc(old('tax_deduction', '0')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Other Deductions (৳)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="other_deductions" value="<?= esc(old('other_deductions', '0')) ?>">
            </div>

            <div class="col-12"><hr class="my-1"></div>

            <div class="col-md-6">
                <label class="form-label">Notes</label>
                <textarea class="form-control" name="notes" rows="2"><?= esc(old('notes', '')) ?></textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="draft" <?= (old('status','draft'))==='draft'?'selected':'' ?>>Draft</option>
                    <option value="approved" <?= (old('status',''))==='approved'?'selected':'' ?>>Approved</option>
                </select>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Generate Payroll</button>
            <a href="<?= site_url('admin/payroll') ?>" class="btn btn-light">Cancel</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
