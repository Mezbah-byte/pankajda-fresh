<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($employee['name']) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Operations</li>
            <li><a href="<?= site_url('admin/employees') ?>" class="text-muted text-decoration-none">Employees</a></li>
            <li><?= esc($employee['name']) ?></li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/employees/' . $employee['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/employees') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle fw-bold"
                     style="width:52px;height:52px;background:linear-gradient(135deg,#5D87FF,#49BEFF);color:#fff;font-size:1.2rem;flex-shrink:0;">
                    <?= esc(strtoupper(substr($employee['name'], 0, 1))) ?>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:1rem;"><?= esc($employee['name']) ?></div>
                    <div class="text-muted" style="font-size:.82rem;"><?= esc($employee['designation'] ?? '') ?> &middot; <?= esc($employee['department'] ?? '') ?></div>
                </div>
            </div>
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Profile</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4">Code</dt><dd class="col-sm-8"><?= esc($employee['employee_code'] ?? '-') ?></dd>
                <dt class="col-sm-4">Phone</dt><dd class="col-sm-8"><?= esc($employee['phone'] ?? '-') ?></dd>
                <dt class="col-sm-4">Email</dt><dd class="col-sm-8"><?= esc($employee['email'] ?? '-') ?></dd>
                <dt class="col-sm-4">National ID</dt><dd class="col-sm-8"><?= esc($employee['national_id'] ?? '-') ?></dd>
                <dt class="col-sm-4">Address</dt><dd class="col-sm-8"><?= esc($employee['address'] ?? '-') ?></dd>
                <dt class="col-sm-4">Joined</dt><dd class="col-sm-8"><?= esc($employee['joined_at'] ?? '-') ?></dd>
                <dt class="col-sm-4">Company</dt><dd class="col-sm-8"><?= esc($company['company_name'] ?? '-') ?></dd>
                <dt class="col-sm-4">Notes</dt><dd class="col-sm-8"><?= nl2br(esc($employee['notes'] ?? '-')) ?></dd>
            </dl>
        </div>
    </div>

    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-semibold mb-4" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Compensation</h6>
            <div class="d-flex justify-content-between mb-4">
                <span class="text-muted">Monthly Salary</span>
                <span class="fw-bold" style="font-size:1.3rem;color:var(--mz-text-primary);">৳ <?= number_format((float) $employee['salary'], 2) ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">Status</span>
                <?php if (($employee['status'] ?? '') === 'active'): ?>
                    <span class="badge-success-soft">Active</span>
                <?php else: ?>
                    <span class="badge-secondary-soft"><?= esc(ucfirst($employee['status'] ?? 'active')) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
