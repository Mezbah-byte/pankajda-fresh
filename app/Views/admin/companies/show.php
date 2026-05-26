<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold"><?= esc($company['company_name']) ?></h4>
        <p class="text-muted small m-0"><?= esc($company['company_type'] ?? 'Business') ?> &middot; <?= esc(short_un_id($company['un_id'])) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/companies/' . $company['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/companies') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Company Information</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4 text-muted">Trade License</dt><dd class="col-sm-8"><?= esc($company['trade_license'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Tax ID</dt><dd class="col-sm-8"><?= esc($company['tax_id'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Phone</dt><dd class="col-sm-8"><?= esc($company['phone'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Email</dt><dd class="col-sm-8"><?= esc($company['email'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Website</dt><dd class="col-sm-8"><?= esc($company['website'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Address</dt><dd class="col-sm-8"><?= esc(($company['address'] ?? '') . ($company['city'] ? ', ' . $company['city'] : '')) ?></dd>
                <dt class="col-sm-4 text-muted">Country</dt><dd class="col-sm-8"><?= esc($company['country'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Currency</dt><dd class="col-sm-8"><?= esc($company['currency'] ?? 'BDT') ?></dd>
                <dt class="col-sm-4 text-muted">Opening Balance</dt><dd class="col-sm-8"><?= esc($company['currency'] ?? 'BDT') ?> <?= number_format((float) ($company['opening_balance'] ?? 0), 2) ?></dd>
            </dl>
        </div>
        <?php if (! empty($company['notes'])): ?>
            <div class="pd-card">
                <h6 class="fw-bold mb-2">Notes</h6>
                <p class="mb-0 text-muted"><?= nl2br(esc($company['notes'])) ?></p>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Quick Stats</h6>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Status</span><span class="badge <?= ($company['status'] ?? '') === 'active' ? 'bg-success' : 'bg-secondary' ?>"><?= esc(ucfirst($company['status'] ?? 'active')) ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Created</span><span><?= esc($company['created_at'] ?? '-') ?></span></div>
            <div class="d-flex justify-content-between"><span class="text-muted">Last Updated</span><span><?= esc($company['updated_at'] ?? '-') ?></span></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
