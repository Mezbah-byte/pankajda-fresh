<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($customer['customer_name']) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/customers') ?>" class="text-muted text-decoration-none">Customers</a></li>
            <li><?= esc($customer['customer_name']) ?></li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/customers/' . $customer['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/customers') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle fw-bold"
                     style="width:48px;height:48px;background:linear-gradient(135deg,#13DEB9,#02a98f);color:#fff;font-size:1.1rem;flex-shrink:0;">
                    <?= esc(strtoupper(substr($customer['customer_name'], 0, 1))) ?>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:1rem;"><?= esc($customer['customer_name']) ?></div>
                    <div class="text-muted" style="font-size:.8rem;"><?= esc($customer['customer_code'] ?? short_un_id($customer['un_id'])) ?> &middot; <?= esc($customer['city'] ?? '') ?></div>
                </div>
            </div>
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Contact &amp; Profile</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4">Phone</dt><dd class="col-sm-8"><?= esc($customer['phone'] ?? '-') ?></dd>
                <dt class="col-sm-4">Email</dt><dd class="col-sm-8"><?= esc($customer['email'] ?? '-') ?></dd>
                <dt class="col-sm-4">Address</dt><dd class="col-sm-8"><?= esc($customer['address'] ?? '-') ?></dd>
                <dt class="col-sm-4">City</dt><dd class="col-sm-8"><?= esc($customer['city'] ?? '-') ?></dd>
                <dt class="col-sm-4">Notes</dt><dd class="col-sm-8"><?= nl2br(esc($customer['notes'] ?? '-')) ?></dd>
            </dl>
        </div>
    </div>

    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-semibold mb-4" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Account Summary</h6>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Opening Balance</span>
                <span class="fw-semibold">৳ <?= number_format((float) $customer['opening_balance'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Credit Limit</span>
                <span class="fw-semibold">৳ <?= number_format((float) $customer['credit_limit'], 2) ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">Current Due</span>
                <span class="fw-bold" style="font-size:1.4rem;color:#FA896B;">৳ <?= number_format((float) $customer['current_due'], 2) ?></span>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
