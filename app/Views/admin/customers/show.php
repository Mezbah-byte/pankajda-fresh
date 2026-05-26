<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold"><?= esc($customer['customer_name']) ?></h4>
        <p class="text-muted small m-0"><?= esc($customer['customer_code'] ?? short_un_id($customer['un_id'])) ?> &middot; <?= esc($customer['city'] ?? '') ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/customers/' . $customer['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/customers') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Contact & Profile</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4 text-muted">Phone</dt><dd class="col-sm-8"><?= esc($customer['phone'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Email</dt><dd class="col-sm-8"><?= esc($customer['email'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Address</dt><dd class="col-sm-8"><?= esc($customer['address'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">City</dt><dd class="col-sm-8"><?= esc($customer['city'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Notes</dt><dd class="col-sm-8"><?= nl2br(esc($customer['notes'] ?? '-')) ?></dd>
            </dl>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Account Summary</h6>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Opening Balance</span><span class="fw-semibold">৳ <?= number_format((float) $customer['opening_balance'], 2) ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Credit Limit</span><span class="fw-semibold">৳ <?= number_format((float) $customer['credit_limit'], 2) ?></span></div>
            <hr>
            <div class="d-flex justify-content-between"><span class="fw-bold">Current Due</span><span class="fw-bold text-danger fs-5">৳ <?= number_format((float) $customer['current_due'], 2) ?></span></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
