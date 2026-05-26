<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($expense['expense_title']) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Operations</li>
            <li><a href="<?= site_url('admin/expenses') ?>" class="text-muted text-decoration-none">Expenses</a></li>
            <li><?= esc($expense['expense_title']) ?></li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/expenses/' . $expense['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/expenses') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <h6 class="fw-semibold mb-4" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Expense Details</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4">Date</dt><dd class="col-sm-8"><?= esc($expense['expense_date'] ?? '-') ?></dd>
                <dt class="col-sm-4">Category</dt><dd class="col-sm-8"><span class="badge-secondary-soft"><?= esc(ucfirst($expense['category'] ?? 'office')) ?></span></dd>
                <dt class="col-sm-4">Payment Method</dt><dd class="col-sm-8"><?= esc(ucfirst(str_replace('_', ' ', $expense['payment_method'] ?? 'cash'))) ?></dd>
                <dt class="col-sm-4">Reference No</dt><dd class="col-sm-8"><?= esc($expense['reference_no'] ?? '-') ?></dd>
                <dt class="col-sm-4">Company</dt><dd class="col-sm-8"><?= esc($company['company_name'] ?? '-') ?></dd>
            </dl>
        </div>
        <?php if (! empty($expense['notes'])): ?>
            <div class="pd-card">
                <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Notes</h6>
                <p class="mb-0" style="color:var(--mz-text-primary);font-size:.875rem;line-height:1.7;"><?= nl2br(esc($expense['notes'])) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-4">
        <div class="pd-card text-center">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Amount</h6>
            <div style="font-size:2.2rem;font-weight:800;color:#FA896B;margin-bottom:16px;">৳ <?= number_format((float) ($expense['amount'] ?? 0), 2) ?></div>
            <hr>
            <div class="d-flex justify-content-between mb-2 mt-3">
                <span class="text-muted" style="font-size:.82rem;">Created</span>
                <span style="font-size:.82rem;"><?= esc($expense['created_at'] ?? '-') ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted" style="font-size:.82rem;">Last Updated</span>
                <span style="font-size:.82rem;"><?= esc($expense['updated_at'] ?? '-') ?></span>
            </div>
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
