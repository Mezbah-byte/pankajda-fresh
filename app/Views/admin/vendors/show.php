<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $cur = (new \App\Services\SettingService())->get('finance.currency_symbol', '৳'); ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-truck me-2"></i><?= esc($vendor['vendor_name']) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Accounts</li>
            <li><a href="<?= site_url('admin/vendors') ?>" class="text-muted text-decoration-none">Vendors</a></li>
            <li><?= esc($vendor['vendor_name']) ?></li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/vendors/' . $vendor['un_id'] . '/edit') ?>" class="btn btn-light">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
    </div>
</div>

<?php if (session('success')): ?><div class="alert alert-success mb-3"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="alert alert-danger mb-3"><?= esc(session('error')) ?></div><?php endif; ?>
<?php if (session('errors')): ?>
    <div class="alert alert-danger mb-3"><ul class="mb-0 small">
        <?php foreach (session('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul></div>
<?php endif; ?>

<!-- ── Stat Cards ──────────────────────────────────────────────── -->
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="pd-stat gradient-3">
            <div class="stat-label">Total Payable</div>
            <div class="stat-value"><?= $cur ?> <?= number_format((float)($vendor['total_payable']??0), 2) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="pd-stat gradient-1">
            <div class="stat-label">Total Paid</div>
            <div class="stat-value"><?= $cur ?> <?= number_format((float)($vendor['total_paid']??0), 2) ?></div>
        </div>
    </div>
    <?php $balance = (float)($vendor['total_payable']??0) - (float)($vendor['total_paid']??0); ?>
    <div class="col-md-3">
        <div class="pd-stat <?= $balance > 0 ? 'gradient-4' : 'gradient-2' ?>">
            <div class="stat-label">Balance Due</div>
            <div class="stat-value"><?= $cur ?> <?= number_format($balance, 2) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="pd-stat gradient-2">
            <div class="stat-label">Products Supplied</div>
            <div class="stat-value"><?= count($products ?? []) ?></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- ── Left col: Vendor info + Record Payment ─────────────── -->
    <div class="col-md-4">
        <div class="pd-card mb-3">
            <h6 class="text-uppercase text-muted mb-3" style="font-size:.72rem;letter-spacing:.6px;">Vendor Info</h6>
            <table class="table table-borderless table-sm" style="font-size:.875rem;">
                <tr>
                    <td class="text-muted" style="width:40%">Contact</td>
                    <td class="fw-semibold"><?= esc($vendor['contact_person'] ?? '—') ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Email</td>
                    <td><?= $vendor['email'] ? '<a href="mailto:' . esc($vendor['email']) . '">' . esc($vendor['email']) . '</a>' : '—' ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Phone</td>
                    <td><?= esc($vendor['phone'] ?? '—') ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Status</td>
                    <td>
                        <span class="badge bg-<?= ($vendor['status']??'active')==='active'?'success':'secondary' ?>-subtle
                              text-<?= ($vendor['status']??'active')==='active'?'success':'secondary' ?>">
                            <?= ucfirst($vendor['status']??'active') ?>
                        </span>
                    </td>
                </tr>
                <?php if (!empty($vendor['address'])): ?>
                <tr>
                    <td class="text-muted">Address</td>
                    <td><?= nl2br(esc($vendor['address'])) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($vendor['notes'])): ?>
                <tr>
                    <td class="text-muted">Notes</td>
                    <td class="text-muted" style="font-size:.82rem;"><?= nl2br(esc($vendor['notes'])) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Record Payment -->
        <div class="pd-card">
            <h6 class="fw-semibold mb-3" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;color:var(--mz-text-muted);">Record Payment</h6>
            <form method="post" action="<?= site_url('admin/vendors/' . $vendor['un_id'] . '/payment') ?>">
                <?= csrf_field() ?>
                <div class="row g-2">
                    <div class="col-12">
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm"
                               name="amount" placeholder="Amount (<?= esc($cur) ?>)" required>
                    </div>
                    <div class="col-12">
                        <input type="date" class="form-control form-control-sm"
                               name="payment_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-12">
                        <input type="text" class="form-control form-control-sm"
                               name="reference_no" placeholder="Reference No">
                    </div>
                    <div class="col-12">
                        <input type="text" class="form-control form-control-sm"
                               name="notes" placeholder="Notes (optional)">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-success btn-sm w-100"><i class="bi bi-check-circle me-1"></i>Record Payment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ── Right col ──────────────────────────────────────────── -->
    <div class="col-md-8">

        <!-- Products Supplied -->
        <div class="pd-card mb-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;color:var(--mz-text-muted);">
                    Products Supplied
                    <span class="badge bg-secondary ms-1"><?= count($products ?? []) ?></span>
                </h6>
                <a href="<?= site_url('admin/products/create') ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus me-1"></i>Add Product
                </a>
            </div>

            <?php if (empty($products)): ?>
                <div class="text-center py-4 text-muted" style="font-size:.875rem;">
                    <i class="bi bi-box-seam d-block mb-2" style="font-size:2rem;color:#E5EAF2;"></i>
                    No products linked to this vendor yet.
                    <a href="<?= site_url('admin/products/create') ?>" class="d-block mt-1">Add a product</a>
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0" style="font-size:.875rem;">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th class="text-end">Sale Price</th>
                            <th class="text-end">Cost Price</th>
                            <th>Status</th>
                            <th class="text-end"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($p['product_name']) ?></td>
                            <td class="text-muted"><?= esc($p['category'] ?: '—') ?></td>
                            <td class="text-muted"><?= esc($p['unit'] ?: '—') ?></td>
                            <td class="text-end">
                                <?php if ((float)($p['default_price']??0) > 0): ?>
                                    <?= $cur ?> <?= number_format((float)$p['default_price'], 2) ?>
                                <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ((float)($p['cost_price']??0) > 0): ?>
                                    <span class="text-muted"><?= $cur ?> <?= number_format((float)$p['cost_price'], 2) ?></span>
                                <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= ($p['status']??'active')==='active'?'success':'secondary' ?>-subtle
                                      text-<?= ($p['status']??'active')==='active'?'success':'secondary' ?>"
                                      style="font-size:.75rem;">
                                    <?= ucfirst($p['status']??'active') ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= site_url('admin/products/' . $p['un_id'] . '/edit') ?>"
                                   class="btn btn-xs btn-light" style="font-size:.75rem;padding:.2rem .5rem;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Payment History -->
        <div class="pd-card">
            <h6 class="fw-semibold mb-3" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;color:var(--mz-text-muted);">
                Payment History
            </h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0" style="font-size:.875rem;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-end">Amount</th>
                            <th>Reference</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($payments['items'] ?? []) as $pay): ?>
                        <tr>
                            <td><?= esc($pay['payment_date']) ?></td>
                            <td class="text-end fw-semibold text-success">
                                <?= $cur ?> <?= number_format((float)$pay['amount'], 2) ?>
                            </td>
                            <td><?= esc($pay['reference_no'] ?? '—') ?></td>
                            <td class="text-muted"><?= esc($pay['notes'] ?? '—') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($payments['items'])): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">No payments recorded.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
