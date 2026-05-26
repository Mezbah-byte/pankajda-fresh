<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Invoice <?= esc($sale['invoice_no']) ?></h4>
        <p class="text-muted small m-0"><?= esc($sale['sale_date']) ?> &middot; <?= esc(ucfirst($sale['sale_type'])) ?> sale</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/sales/' . $sale['un_id'] . '/invoice') ?>" target="_blank" class="btn btn-light"><i class="bi bi-printer me-2"></i>Print</a>
        <a href="<?= site_url('admin/sales') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <h6 class="fw-bold mb-1">Customer</h6>
                    <div class="fw-semibold"><?= esc($customer['customer_name'] ?? '-') ?></div>
                    <div class="small text-muted"><?= esc($customer['phone'] ?? '') ?></div>
                </div>
                <?php if (! empty($company)): ?>
                    <div class="text-end">
                        <h6 class="fw-bold mb-1">Company</h6>
                        <div><?= esc($company['company_name']) ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Product</th><th class="text-end">Qty</th><th>Unit</th><th class="text-end">Price</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                        <?php foreach (($sale['items'] ?? []) as $it): ?>
                            <tr>
                                <td><?= esc($it['product_name']) ?></td>
                                <td class="text-end"><?= number_format((float) $it['quantity'], 2) ?></td>
                                <td><?= esc($it['unit']) ?></td>
                                <td class="text-end">৳ <?= number_format((float) $it['unit_price'], 2) ?></td>
                                <td class="text-end fw-semibold">৳ <?= number_format((float) $it['total'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (! empty($sale['notes'])): ?>
                <p class="text-muted small mt-3 mb-0"><strong>Notes:</strong> <?= esc($sale['notes']) ?></p>
            <?php endif; ?>
        </div>

        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0">Payment History</h6>
                <?php if ((float) $sale['due_amount'] > 0): ?>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#addSalePay"><i class="bi bi-plus-circle me-1"></i>Add Payment</button>
                <?php endif; ?>
            </div>

            <?php if ((float) $sale['due_amount'] > 0): ?>
                <div class="collapse" id="addSalePay">
                    <form method="post" action="<?= site_url('admin/sales/' . $sale['un_id'] . '/payments') ?>" class="border rounded p-3 mb-3" style="background:#f7f8fc;">
                        <?= csrf_field() ?>
                        <div class="row g-2">
                            <div class="col-md-3"><input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" max="<?= esc($sale['due_amount']) ?>" required></div>
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
                            <div class="col-12 d-flex gap-2"><button class="btn btn-primary btn-sm"><i class="bi bi-check2 me-1"></i>Record</button></div>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <table class="table align-middle">
                <thead><tr><th>Date</th><th>Method</th><th>Reference</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
                    <?php foreach (($sale['payments'] ?? []) as $p): ?>
                        <tr>
                            <td><?= esc($p['payment_date']) ?></td>
                            <td><span class="badge bg-light text-dark"><?= esc($p['payment_method']) ?></span></td>
                            <td><?= esc($p['reference_no'] ?? '-') ?></td>
                            <td class="text-end fw-semibold text-success">৳ <?= number_format((float) $p['amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($sale['payments'])): ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">No payments yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Totals</h6>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Subtotal</span><span>৳ <?= number_format((float) $sale['subtotal'], 2) ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Discount</span><span>− ৳ <?= number_format((float) $sale['discount'], 2) ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Tax</span><span>+ ৳ <?= number_format((float) $sale['tax'], 2) ?></span></div>
            <hr>
            <div class="d-flex justify-content-between mb-2 fs-5"><span class="fw-bold">Total</span><span class="fw-bold">৳ <?= number_format((float) $sale['total_amount'], 2) ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Paid</span><span class="text-success">৳ <?= number_format((float) $sale['paid_amount'], 2) ?></span></div>
            <div class="d-flex justify-content-between"><span class="text-muted">Due</span><span class="fw-bold text-danger">৳ <?= number_format((float) $sale['due_amount'], 2) ?></span></div>
            <div class="text-center mt-3">
                <?php $st = $sale['payment_status']; ?>
                <span class="badge badge-status <?= $st === 'paid' ? 'bg-success' : ($st === 'partial' ? 'bg-warning' : 'bg-danger') ?> px-3 py-2"><?= esc(ucfirst($st)) ?></span>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
