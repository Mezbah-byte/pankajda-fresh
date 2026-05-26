<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold"><?= esc($employee['name']) ?></h4>
        <p class="text-muted small m-0"><?= esc($employee['designation'] ?? '') ?> &middot; <?= esc($employee['department'] ?? '') ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/employees/' . $employee['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/employees') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Profile</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4 text-muted">Code</dt><dd class="col-sm-8"><?= esc($employee['employee_code'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Phone</dt><dd class="col-sm-8"><?= esc($employee['phone'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Email</dt><dd class="col-sm-8"><?= esc($employee['email'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">National ID</dt><dd class="col-sm-8"><?= esc($employee['national_id'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Address</dt><dd class="col-sm-8"><?= esc($employee['address'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Joined</dt><dd class="col-sm-8"><?= esc($employee['joined_at'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Company</dt><dd class="col-sm-8"><?= esc($company['company_name'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Notes</dt><dd class="col-sm-8"><?= nl2br(esc($employee['notes'] ?? '-')) ?></dd>
            </dl>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Compensation</h6>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Monthly Salary</span><span class="fw-bold fs-5">৳ <?= number_format((float) $employee['salary'], 2) ?></span></div>
            <hr>
            <div class="d-flex justify-content-between"><span class="text-muted">Status</span>
                <span class="badge <?= ($employee['status'] ?? '') === 'active' ? 'bg-success' : 'bg-secondary' ?>"><?= esc(ucfirst($employee['status'] ?? 'active')) ?></span>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
