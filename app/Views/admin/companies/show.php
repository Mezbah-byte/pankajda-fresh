<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($company['company_name']) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/companies') ?>" class="text-muted text-decoration-none">Companies</a></li>
            <li><?= esc($company['company_name']) ?></li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/companies/' . $company['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/companies') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-2 fw-bold"
                     style="width:50px;height:50px;background:linear-gradient(135deg,#5D87FF,#49BEFF);color:#fff;font-size:1.2rem;flex-shrink:0;">
                    <?= esc(strtoupper(substr($company['company_name'], 0, 1))) ?>
                </div>
                <div>
                    <h6 class="fw-bold m-0"><?= esc($company['company_name']) ?></h6>
                    <span class="text-muted" style="font-size:.8rem;"><?= esc($company['company_type'] ?? 'Business') ?> &middot; <?= esc(short_un_id($company['un_id'])) ?></span>
                </div>
            </div>
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Company Information</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4">Trade License</dt><dd class="col-sm-8"><?= esc($company['trade_license'] ?? '-') ?></dd>
                <dt class="col-sm-4">Tax ID</dt><dd class="col-sm-8"><?= esc($company['tax_id'] ?? '-') ?></dd>
                <dt class="col-sm-4">Phone</dt><dd class="col-sm-8"><?= esc($company['phone'] ?? '-') ?></dd>
                <dt class="col-sm-4">Email</dt><dd class="col-sm-8"><?= esc($company['email'] ?? '-') ?></dd>
                <dt class="col-sm-4">Website</dt><dd class="col-sm-8"><?= esc($company['website'] ?? '-') ?></dd>
                <dt class="col-sm-4">Address</dt><dd class="col-sm-8"><?= esc(($company['address'] ?? '') . ($company['city'] ? ', ' . $company['city'] : '')) ?></dd>
                <dt class="col-sm-4">Country</dt><dd class="col-sm-8"><?= esc($company['country'] ?? '-') ?></dd>
                <dt class="col-sm-4">Currency</dt><dd class="col-sm-8"><?= esc($company['currency'] ?? 'BDT') ?></dd>
                <dt class="col-sm-4">Opening Balance</dt><dd class="col-sm-8"><?= esc($company['currency'] ?? 'BDT') ?> <?= number_format((float) ($company['opening_balance'] ?? 0), 2) ?></dd>
            </dl>
        </div>
        <?php if (! empty($company['notes'])): ?>
            <div class="pd-card">
                <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Notes</h6>
                <p class="mb-0" style="color:var(--mz-text-primary);font-size:.875rem;line-height:1.7;"><?= nl2br(esc($company['notes'])) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Quick Stats</h6>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Status</span>
                <?php $st = $company['status'] ?? 'active'; ?>
                <?php if ($st === 'active'): ?>
                    <span class="badge-success-soft">Active</span>
                <?php elseif ($st === 'pending'): ?>
                    <span class="badge-warning-soft">Pending</span>
                <?php else: ?>
                    <span class="badge-secondary-soft"><?= esc(ucfirst($st)) ?></span>
                <?php endif; ?>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Created</span>
                <span style="font-size:.875rem;"><?= esc($company['created_at'] ?? '-') ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted">Last Updated</span>
                <span style="font-size:.875rem;"><?= esc($company['updated_at'] ?? '-') ?></span>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
