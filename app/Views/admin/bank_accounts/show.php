<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($account['account_name']) ?></h4>
        <ul class="mz-breadcrumb"><li>Finance</li><li><a href="<?= site_url('admin/bank-accounts') ?>">Bank Accounts</a></li><li><?= esc($account['account_name']) ?></li></ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/bank-accounts/' . $account['un_id'] . '/edit') ?>" class="btn btn-light"><i class="bi bi-pencil me-1"></i>Edit</a>
        <form method="post" action="<?= site_url('admin/bank-accounts/' . $account['un_id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this account?');">
            <?= csrf_field() ?>
            <button class="btn btn-light text-danger"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
    </div>
</div>

<?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>

<div class="row g-3">
    <div class="col-md-4">
        <div class="pd-card">
            <div style="background:linear-gradient(135deg,var(--mz-primary),#7c9dff);border-radius:12px;padding:24px;color:#fff;margin-bottom:16px;">
                <div style="font-size:.78rem;opacity:.8;text-transform:uppercase;letter-spacing:.5px;"><?= esc($account['bank_name']) ?></div>
                <div style="font-size:1.8rem;font-weight:700;margin:8px 0;">৳ <?= number_format((float)($account['balance']??0), 2) ?></div>
                <div style="font-size:.85rem;opacity:.9;"><?= esc($account['account_number']) ?></div>
                <?php if (!empty($account['branch'])): ?><div style="font-size:.78rem;opacity:.7;margin-top:4px;"><?= esc($account['branch']) ?></div><?php endif; ?>
            </div>
            <table class="table table-borderless table-sm">
                <tr><td class="text-muted" style="width:40%">Currency</td><td class="fw-semibold"><?= esc($account['currency']??'BDT') ?></td></tr>
                <tr><td class="text-muted">Routing</td><td><?= esc($account['routing_number']??'-') ?></td></tr>
                <tr><td class="text-muted">Status</td><td><span class="badge bg-<?= ($account['status']??'active')==='active'?'success':'secondary' ?>-subtle text-<?= ($account['status']??'active')==='active'?'success':'secondary' ?>"><?= ucfirst($account['status']??'active') ?></span></td></tr>
            </table>
            <?php if (!empty($account['notes'])): ?>
                <div class="text-muted" style="font-size:.82rem;"><?= nl2br(esc($account['notes'])) ?></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-8">
        <!-- Adjust balance -->
        <div class="pd-card mb-3">
            <h6 class="fw-semibold mb-3">Adjust Balance</h6>
            <form method="post" action="<?= site_url('admin/bank-accounts/' . $account['un_id'] . '/adjust') ?>">
                <?= csrf_field() ?>
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">New Balance (৳)</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="new_balance" value="<?= esc(number_format((float)($account['balance']??0), 2, '.', '')) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Note</label>
                        <input type="text" class="form-control" name="note" placeholder="Reason for adjustment">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-warning w-100"><i class="bi bi-arrow-repeat me-1"></i>Adjust</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="pd-card">
            <h6 class="fw-semibold mb-3">Account Details</h6>
            <p class="text-muted">Transaction history coming soon. Use this account in expenses and sales to track cashflow.</p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
