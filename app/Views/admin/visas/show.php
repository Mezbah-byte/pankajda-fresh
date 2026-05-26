<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold"><?= esc($visa['visa_name']) ?></h4>
        <p class="text-muted small m-0">
            <?= esc($visa['country'] ?? '') ?> &middot; <?= esc($visa['category'] ?? '') ?> &middot;
            <?= esc(short_un_id($visa['un_id'])) ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/visas/' . $visa['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/visas') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Visa Information</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4 text-muted">Beneficiary</dt><dd class="col-sm-8"><?= esc($visa['beneficiary_name'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Passport No</dt><dd class="col-sm-8"><?= esc($visa['passport_no'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Visa Number</dt><dd class="col-sm-8"><?= esc($visa['visa_number'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Issue Date</dt><dd class="col-sm-8"><?= esc($visa['visa_issue_date'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Expiry Date</dt><dd class="col-sm-8"><?= esc($visa['visa_expiry_date'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Company</dt><dd class="col-sm-8"><?= esc($company['company_name'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Notes</dt><dd class="col-sm-8"><?= nl2br(esc($visa['notes'] ?? '-')) ?></dd>
            </dl>
        </div>

        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0">Payment History</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#addPayment">
                    <i class="bi bi-plus-circle me-1"></i>Add Payment
                </button>
            </div>

            <div class="collapse <?= ($visa['due_amount'] ?? 0) > 0 ? '' : '' ?>" id="addPayment">
                <form method="post" action="<?= site_url('admin/visas/' . $visa['un_id'] . '/payments') ?>" class="border rounded p-3 mb-3" style="background:#f7f8fc;">
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
                        <div class="col-12 d-flex gap-2"><button class="btn btn-primary btn-sm"><i class="bi bi-check2 me-1"></i>Record Payment</button></div>
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
                                <td><span class="badge bg-light text-dark"><?= esc($p['payment_method']) ?></span></td>
                                <td><?= esc($p['reference_no'] ?? '-') ?></td>
                                <td class="text-end fw-semibold text-success">৳ <?= number_format((float) $p['amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($payments)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">No payments recorded yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Cost Summary</h6>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Visa Cost</span><span class="fw-semibold">৳ <?= number_format((float) $visa['visa_cost'], 2) ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Paid</span><span class="fw-semibold text-success">৳ <?= number_format((float) $visa['paid_amount'], 2) ?></span></div>
            <hr>
            <div class="d-flex justify-content-between"><span class="fw-bold">Due</span><span class="fw-bold text-danger fs-5">৳ <?= number_format((float) $visa['due_amount'], 2) ?></span></div>
            <div class="text-center mt-3">
                <?php $st = $visa['payment_status']; ?>
                <span class="badge badge-status <?= $st === 'paid' ? 'bg-success' : ($st === 'partial' ? 'bg-warning' : 'bg-danger') ?> px-3 py-2"><?= esc(ucfirst($st)) ?></span>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
