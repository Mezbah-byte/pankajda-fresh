<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Invoice <?= esc($sale['invoice_no']) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/sales') ?>" class="text-muted text-decoration-none">Sales</a></li>
            <li><?= esc($sale['invoice_no']) ?></li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/sales/' . $sale['un_id'] . '/invoice') ?>" target="_blank" class="btn btn-light"><i class="bi bi-printer me-2"></i>Print</a>
        <a href="<?= site_url('admin/sales') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Customer</div>
                    <div class="fw-bold"><?= esc($customer['customer_name'] ?? '-') ?></div>
                    <div class="text-muted" style="font-size:.82rem;"><?= esc($customer['phone'] ?? '') ?></div>
                </div>
                <?php if (! empty($company)): ?>
                    <div class="text-end">
                        <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Company</div>
                        <div class="fw-semibold"><?= esc($company['company_name']) ?></div>
                    </div>
                <?php endif; ?>
                <div class="text-end">
                    <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Date</div>
                    <div class="fw-semibold"><?= esc($sale['sale_date']) ?></div>
                    <span class="badge-secondary-soft"><?= esc(ucfirst($sale['sale_type'])) ?></span>
                </div>
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
                <p class="text-muted mt-3 mb-0" style="font-size:.82rem;"><strong>Notes:</strong> <?= esc($sale['notes']) ?></p>
            <?php endif; ?>
        </div>

        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold m-0">Payment History</h6>
                <?php if ((float) $sale['due_amount'] > 0): ?>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#addSalePay"><i class="bi bi-plus-circle me-1"></i>Add Payment</button>
                <?php endif; ?>
            </div>

            <?php if ((float) $sale['due_amount'] > 0): ?>
                <div class="collapse" id="addSalePay">
                    <form method="post" action="<?= site_url('admin/sales/' . $sale['un_id'] . '/payments') ?>" class="collapse-panel mb-4">
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
                            <td><span class="badge-secondary-soft"><?= esc($p['payment_method']) ?></span></td>
                            <td><?= esc($p['reference_no'] ?? '-') ?></td>
                            <td class="text-end fw-semibold" style="color:#02a98f;">৳ <?= number_format((float) $p['amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($sale['payments'])): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No payments yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-semibold mb-4" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Totals</h6>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Subtotal</span>
                <span>৳ <?= number_format((float) $sale['subtotal'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Discount</span>
                <span>− ৳ <?= number_format((float) $sale['discount'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Tax</span>
                <span>+ ৳ <?= number_format((float) $sale['tax'], 2) ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-3">
                <span class="fw-bold" style="font-size:1.05rem;">Total</span>
                <span class="fw-bold" style="font-size:1.05rem;">৳ <?= number_format((float) $sale['total_amount'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Paid</span>
                <span style="color:#02a98f;">৳ <?= number_format((float) $sale['paid_amount'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <span class="text-muted">Due</span>
                <span class="fw-bold" style="color:#FA896B;">৳ <?= number_format((float) $sale['due_amount'], 2) ?></span>
            </div>
            <div class="text-center">
                <?php $st = $sale['payment_status']; ?>
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
