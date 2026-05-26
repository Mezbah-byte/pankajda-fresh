<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($visa['visa_name']) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/visas') ?>" class="text-muted text-decoration-none">Visas</a></li>
            <li><?= esc($visa['visa_name']) ?></li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/visas/' . $visa['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/visas') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <h6 class="fw-semibold mb-4" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Visa Information</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4">Beneficiary</dt><dd class="col-sm-8"><?= esc($visa['beneficiary_name'] ?? '-') ?></dd>
                <dt class="col-sm-4">Passport No</dt><dd class="col-sm-8"><?= esc($visa['passport_no'] ?? '-') ?></dd>
                <dt class="col-sm-4">Visa Number</dt><dd class="col-sm-8"><?= esc($visa['visa_number'] ?? '-') ?></dd>
                <dt class="col-sm-4">Country</dt><dd class="col-sm-8"><?= esc($visa['country'] ?? '-') ?></dd>
                <dt class="col-sm-4">Category</dt><dd class="col-sm-8"><?= esc($visa['category'] ?? '-') ?></dd>
                <dt class="col-sm-4">Issue Date</dt><dd class="col-sm-8"><?= esc($visa['visa_issue_date'] ?? '-') ?></dd>
                <dt class="col-sm-4">Expiry Date</dt><dd class="col-sm-8"><?= esc($visa['visa_expiry_date'] ?? '-') ?></dd>
                <dt class="col-sm-4">Company</dt><dd class="col-sm-8"><?= esc($company['company_name'] ?? '-') ?></dd>
                <dt class="col-sm-4">Notes</dt><dd class="col-sm-8"><?= nl2br(esc($visa['notes'] ?? '-')) ?></dd>
            </dl>
        </div>

        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold m-0">Payment History</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#addPayment">
                    <i class="bi bi-plus-circle me-1"></i>Add Payment
                </button>
            </div>

            <div class="collapse" id="addPayment">
                <form method="post" action="<?= site_url('admin/visas/' . $visa['un_id'] . '/payments') ?>" class="collapse-panel mb-4">
                    <?= csrf_field() ?>
                    <div class="row g-2">
                        <div class="col-md-3"><input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" required></div>
                        <div class="col-md-3">
                            <select name="payment_method" class="form-select">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="mfs">Mobile Banking</option>
                            </select>
                        </div>
                        <div class="col-md-3"><input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
                        <div class="col-md-3"><input type="text" name="reference_no" class="form-control" placeholder="Reference"></div>
                        <div class="col-12"><input type="text" name="notes" class="form-control" placeholder="Notes (optional)"></div>
                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-primary btn-sm"><i class="bi bi-check2 me-1"></i>Record Payment</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Date</th><th>Method</th><th>Reference</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                        <?php foreach (($payments ?? []) as $p): ?>
                            <tr>
                                <td><?= esc($p['payment_date']) ?></td>
                                <td><span class="badge-secondary-soft"><?= esc($p['payment_method']) ?></span></td>
                                <td><?= esc($p['reference_no'] ?? '-') ?></td>
                                <td class="text-end fw-semibold" style="color:#02a98f;">৳ <?= number_format((float) $p['amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($payments)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">No payments recorded yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-semibold mb-4" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Cost Summary</h6>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Visa Cost</span>
                <span class="fw-semibold">৳ <?= number_format((float) $visa['visa_cost'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Paid</span>
                <span class="fw-semibold" style="color:#02a98f;">৳ <?= number_format((float) $visa['paid_amount'], 2) ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-4">
                <span class="fw-bold">Due</span>
                <span class="fw-bold" style="font-size:1.25rem;color:#FA896B;">৳ <?= number_format((float) $visa['due_amount'], 2) ?></span>
            </div>
            <div class="text-center">
                <?php $st = $visa['payment_status']; ?>
                <?php if ($st === 'paid'): ?>
                    <span class="badge-success-soft" style="font-size:.85rem;padding:.5em 1.5em;">Paid</span>
                <?php elseif ($st === 'partial'): ?>
                    <span class="badge-warning-soft" style="font-size:.85rem;padding:.5em 1.5em;">Partial</span>
                <?php else: ?>
                    <span class="badge-danger-soft" style="font-size:.85rem;padding:.5em 1.5em;">Due</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
