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
        <a href="<?= site_url('admin/grv/create?customer_un_id=' . $customer['un_id']) ?>" class="btn btn-light"><i class="bi bi-arrow-return-left me-2"></i>New GRV</a>
        <a href="<?= site_url('admin/customers/' . $customer['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/customers') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <!-- Company banner (if assigned) -->
        <?php if ($company): ?>
        <div class="pd-card mb-0" style="background:linear-gradient(135deg,#ECF2FF,#f0f4ff);border-left:4px solid var(--mz-primary);">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-building" style="font-size:1.8rem;color:var(--mz-primary);"></i>
                <div>
                    <div class="fw-bold" style="font-size:1.05rem;"><?= esc($company['company_name']) ?></div>
                    <div class="text-muted" style="font-size:.8rem;">Company</div>
                </div>
            </div>
        </div>
        <?php endif; ?>

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

        <!-- Recent Invoices grouped under company -->
        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold m-0">
                    <?php if ($company): ?>
                        <i class="bi bi-building me-2" style="color:var(--mz-primary);"></i><?= esc($company['company_name']) ?> — Invoices
                    <?php else: ?>
                        Invoices
                    <?php endif; ?>
                </h6>
                <a href="<?= site_url('admin/customers/' . $customer['un_id'] . '/ledger') ?>" class="btn btn-sm btn-light">Full Ledger</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle" style="font-size:.88rem;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_sales)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">No invoices yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_sales as $idx => $s): ?>
                                <tr>
                                    <td class="text-muted"><?= $idx + 1 ?></td>
                                    <td>
                                        <a href="<?= site_url('admin/sales/' . $s['un_id']) ?>" class="text-decoration-none fw-semibold" style="color:var(--mz-primary);"><?= esc($s['invoice_no']) ?></a>
                                    </td>
                                    <td><?= esc($s['sale_date']) ?></td>
                                    <td class="text-end">৳ <?= number_format((float) $s['total_amount'], 2) ?></td>
                                    <td>
                                        <?php $ps = $s['payment_status']; ?>
                                        <span class="badge <?= $ps === 'paid' ? 'bg-success' : ($ps === 'partial' ? 'bg-warning text-dark' : 'bg-danger') ?>" style="font-size:.7rem;"><?= ucfirst($ps) ?></span>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= site_url('admin/sales/' . $s['un_id'] . '/invoice') ?>" class="btn btn-sm btn-light" target="_blank" title="Invoice"><i class="bi bi-receipt"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- GRVs -->
        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold m-0"><i class="bi bi-arrow-return-left me-2" style="color:#FA896B;"></i>Goods Return Vouchers (GRV)</h6>
                <a href="<?= site_url('admin/grv/create?customer_un_id=' . $customer['un_id']) ?>" class="btn btn-sm btn-light"><i class="bi bi-plus-circle me-1"></i>New GRV</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle" style="font-size:.88rem;">
                    <thead>
                        <tr><th>GRV No</th><th>Date</th><th class="text-end">Amount</th><th>Status</th><th class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($grvs)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">No GRVs yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($grvs as $g): ?>
                                <tr>
                                    <td><a href="<?= site_url('admin/grv/' . $g['un_id']) ?>" class="text-decoration-none fw-semibold" style="color:var(--mz-primary);"><?= esc($g['grv_no']) ?></a></td>
                                    <td><?= esc($g['grv_date']) ?></td>
                                    <td class="text-end" style="color:#FA896B;">৳ <?= number_format((float) $g['total_amount'], 2) ?></td>
                                    <td><span class="badge-<?= $g['status'] === 'approved' ? 'success' : 'secondary' ?>-soft"><?= esc(ucfirst($g['status'])) ?></span></td>
                                    <td class="text-end"><a href="<?= site_url('admin/grv/' . $g['un_id']) ?>" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
                <span class="text-muted">Payment Received (−)</span>
                <span class="fw-semibold" style="color:#02a98f;">৳ <?= number_format((float) $total_payments, 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Discount (−)</span>
                <span class="fw-semibold" style="color:#02a98f;">৳ <?= number_format((float) $total_discount, 2) ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">Current Due</span>
                <span class="fw-bold" style="font-size:1.4rem;color:#FA896B;">৳ <?= number_format((float) $customer['current_due'], 2) ?></span>
            </div>
        </div>

        <div class="pd-card mt-3">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Quick Links</h6>
            <div class="d-grid gap-2">
                <a href="<?= site_url('admin/customers/' . $customer['un_id'] . '/ledger') ?>" class="btn btn-light text-start"><i class="bi bi-journal-text me-2"></i>View Full Ledger</a>
                <a href="<?= site_url('admin/sales/create') ?>?customer_un_id=<?= $customer['un_id'] ?>" class="btn btn-light text-start"><i class="bi bi-receipt me-2"></i>New Invoice</a>
                <a href="<?= site_url('admin/grv/create?customer_un_id=' . $customer['un_id']) ?>" class="btn btn-light text-start"><i class="bi bi-arrow-return-left me-2"></i>New GRV</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
