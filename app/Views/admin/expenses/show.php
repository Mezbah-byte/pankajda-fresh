<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold"><?= esc($expense['expense_title']) ?></h4>
        <p class="text-muted small m-0"><?= esc(ucfirst($expense['category'] ?? 'office')) ?> &middot; <?= esc(short_un_id($expense['un_id'])) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/expenses/' . $expense['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/expenses') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Expense Details</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4 text-muted">Date</dt><dd class="col-sm-8"><?= esc($expense['expense_date'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Category</dt><dd class="col-sm-8"><span class="badge bg-light text-dark"><?= esc(ucfirst($expense['category'] ?? 'office')) ?></span></dd>
                <dt class="col-sm-4 text-muted">Payment Method</dt><dd class="col-sm-8"><?= esc(ucfirst(str_replace('_', ' ', $expense['payment_method'] ?? 'cash'))) ?></dd>
                <dt class="col-sm-4 text-muted">Reference No</dt><dd class="col-sm-8"><?= esc($expense['reference_no'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Company</dt><dd class="col-sm-8"><?= esc($company['company_name'] ?? '-') ?></dd>
            </dl>
        </div>
        <?php if (! empty($expense['notes'])): ?>
            <div class="pd-card">
                <h6 class="fw-bold mb-2">Notes</h6>
                <p class="mb-0 text-muted"><?= nl2br(esc($expense['notes'])) ?></p>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Amount</h6>
            <div class="text-center mb-3">
                <span class="fs-2 fw-bold text-danger">৳ <?= number_format((float) ($expense['amount'] ?? 0), 2) ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Created</span><span><?= esc($expense['created_at'] ?? '-') ?></span></div>
            <div class="d-flex justify-content-between"><span class="text-muted">Last Updated</span><span><?= esc($expense['updated_at'] ?? '-') ?></span></div>
        </div>
        <div class="pd-card">
            <form method="post" action="<?= site_url('admin/expenses/' . $expense['un_id'] . '/delete') ?>" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                <?= csrf_field() ?>
                <button class="btn btn-outline-danger w-100"><i class="bi bi-trash me-2"></i>Delete Expense</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
