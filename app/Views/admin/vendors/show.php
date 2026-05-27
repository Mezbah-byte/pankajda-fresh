<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($vendor['vendor_name']) ?></h4>
        <ul class="mz-breadcrumb"><li>Accounts</li><li><a href="<?= site_url('admin/vendors') ?>">Vendors</a></li><li><?= esc($vendor['vendor_name']) ?></li></ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/vendors/' . $vendor['un_id'] . '/edit') ?>" class="btn btn-light"><i class="bi bi-pencil me-1"></i>Edit</a>
    </div>
</div>

<?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>

<div class="row g-3">
    <div class="col-md-4">
        <div class="pd-card h-100">
            <h6 class="text-uppercase text-muted mb-3" style="font-size:.72rem;letter-spacing:.6px;">Vendor Info</h6>
            <table class="table table-borderless table-sm">
                <tr><td class="text-muted" style="width:40%">Contact</td><td class="fw-semibold"><?= esc($vendor['contact_person'] ?? '-') ?></td></tr>
                <tr><td class="text-muted">Email</td><td><?= esc($vendor['email'] ?? '-') ?></td></tr>
                <tr><td class="text-muted">Phone</td><td><?= esc($vendor['phone'] ?? '-') ?></td></tr>
                <tr><td class="text-muted">Status</td><td><span class="badge bg-<?= ($vendor['status']??'active')==='active'?'success':'secondary' ?>-subtle text-<?= ($vendor['status']??'active')==='active'?'success':'secondary' ?>"><?= ucfirst($vendor['status']??'active') ?></span></td></tr>
                <tr><td class="text-muted">Address</td><td><?= nl2br(esc($vendor['address'] ?? '-')) ?></td></tr>
            </table>
        </div>
    </div>
    <div class="col-md-8">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="pd-stat gradient-3">
                    <div class="stat-label">Total Payable</div>
                    <div class="stat-value">৳ <?= number_format((float)($vendor['total_payable']??0), 2) ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="pd-stat gradient-1">
                    <div class="stat-label">Total Paid</div>
                    <div class="stat-value">৳ <?= number_format((float)($vendor['total_paid']??0), 2) ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="pd-stat <?= ((float)($vendor['total_payable']??0)-(float)($vendor['total_paid']??0))>0 ? 'gradient-4' : 'gradient-2' ?>">
                    <div class="stat-label">Balance Due</div>
                    <div class="stat-value">৳ <?= number_format(((float)($vendor['total_payable']??0))-((float)($vendor['total_paid']??0)), 2) ?></div>
                </div>
            </div>
        </div>

        <!-- Add Payment -->
        <div class="pd-card mb-3">
            <h6 class="fw-semibold mb-3">Record Payment</h6>
            <?php if (session('errors')): ?><div class="alert alert-danger py-2"><ul class="mb-0 small"><?php foreach (session('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
            <form method="post" action="<?= site_url('admin/vendors/' . $vendor['un_id'] . '/payment') ?>">
                <?= csrf_field() ?>
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="number" step="0.01" min="0.01" class="form-control" name="amount" placeholder="Amount (৳)" required>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="payment_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="reference_no" placeholder="Reference">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-success w-100"><i class="bi bi-check-circle me-1"></i>Pay</button>
                    </div>
                    <div class="col-12">
                        <input type="text" class="form-control" name="notes" placeholder="Notes (optional)">
                    </div>
                </div>
            </form>
        </div>

        <!-- Payment History -->
        <div class="pd-card">
            <h6 class="fw-semibold mb-3">Payment History</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Date</th><th>Amount</th><th>Reference</th><th>Notes</th></tr></thead>
                    <tbody>
                        <?php foreach (($payments['items'] ?? []) as $pay): ?>
                            <tr>
                                <td><?= esc($pay['payment_date']) ?></td>
                                <td class="fw-semibold text-success">৳ <?= number_format((float)$pay['amount'], 2) ?></td>
                                <td><?= esc($pay['reference_no'] ?? '-') ?></td>
                                <td class="text-muted"><?= esc($pay['notes'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($payments['items'])): ?><tr><td colspan="4" class="text-center text-muted py-3">No payments recorded.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
