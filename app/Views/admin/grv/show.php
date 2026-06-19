<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $cur = (new \App\Services\SettingService())->get('finance.currency_symbol', '৳'); ?>

<div class="mz-page-header d-flex align-items-center justify-content-between no-print">
    <div>
        <h4><i class="bi bi-arrow-return-left me-2"></i><?= esc($grv['grv_no']) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/grv') ?>" class="text-muted text-decoration-none">GRV</a></li>
            <li><?= esc($grv['grv_no']) ?></li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <?php if ($grv['status'] === 'draft'): ?>
            <a href="<?= site_url('admin/grv/' . $grv['un_id'] . '/edit') ?>" class="btn btn-light">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <form method="post" action="<?= site_url('admin/grv/' . $grv['un_id'] . '/approve') ?>"
                  onsubmit="return confirm('Approve this GRV? This will update stock and cannot be undone.')" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i>Approve GRV
                </button>
            </form>
        <?php endif; ?>
        <button onclick="window.print()" class="btn btn-light"><i class="bi bi-printer me-1"></i>Print</button>
        <a href="<?= site_url('admin/grv') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-4 no-print"><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-4 no-print"><?= esc($err) ?></div>
<?php endif; ?>

<div class="row g-3">
    <!-- ── Left: GRV details ─────────────────────────────────── -->
    <div class="col-md-8">
        <div class="pd-card mb-3">
            <div class="d-flex align-items-start justify-content-between mb-4">
                <h6 class="fw-semibold mb-0" style="color:var(--mz-text-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;">
                    Goods Return Voucher
                </h6>
                <?php
                    $statusColors = ['draft' => 'secondary', 'approved' => 'success', 'cancelled' => 'danger'];
                    $sc = $statusColors[$grv['status']] ?? 'secondary';
                ?>
                <span class="badge bg-<?= $sc ?>-subtle text-<?= $sc ?> px-3 py-2" style="font-size:.82rem;">
                    <?= esc(ucfirst($grv['status'])) ?>
                </span>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;">GRV Number</div>
                    <div class="fw-bold" style="font-size:1.1rem;"><?= esc($grv['grv_no']) ?></div>
                </div>
                <div class="col-sm-6">
                    <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;">Date</div>
                    <div class="fw-semibold"><?= esc($grv['grv_date']) ?></div>
                </div>
                <div class="col-sm-6">
                    <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;">Customer</div>
                    <div class="fw-semibold">
                        <?php if ($customer): ?>
                            <a href="<?= site_url('admin/customers/' . $customer['un_id']) ?>" class="text-decoration-none">
                                <?= esc($customer['customer_name']) ?>
                            </a>
                        <?php else: ?><span class="text-muted">—</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($company): ?>
                <div class="col-sm-6">
                    <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;">Company</div>
                    <div class="fw-semibold">
                        <a href="<?= site_url('admin/companies/' . $company['un_id']) ?>" class="text-decoration-none">
                            <?= esc($company['company_name']) ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($sale): ?>
                <div class="col-sm-6">
                    <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;">Linked Sale</div>
                    <div class="fw-semibold">
                        <a href="<?= site_url('admin/sales/' . $sale['un_id']) ?>" class="text-decoration-none">
                            <?= esc($sale['invoice_no'] ?? $sale['un_id']) ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($grv['notes'])): ?>
            <div class="p-3 rounded mb-0" style="background:#f7f8fc;font-size:.88rem;">
                <strong class="d-block mb-1 text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.4px;">Notes</strong>
                <?= nl2br(esc($grv['notes'])) ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- ── Returned Items Table ─────────────────────────── -->
        <div class="pd-card">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;">
                Returned Items
                <span class="badge bg-secondary ms-1"><?= count($grv_items) ?></span>
            </h6>

            <?php if (empty($grv_items)): ?>
                <div class="text-center py-4 text-muted" style="font-size:.875rem;">
                    <i class="bi bi-box-seam d-block mb-2" style="font-size:2rem;color:#E5EAF2;"></i>
                    No items recorded for this GRV.
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle" style="font-size:.875rem;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Unit</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">VAT</th>
                            <th class="text-end">Line Total</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grv_items as $idx => $item): ?>
                        <tr>
                            <td class="text-muted"><?= $idx + 1 ?></td>
                            <td>
                                <?php if ($item['product_un_id']): ?>
                                    <a href="<?= site_url('admin/products/' . $item['product_un_id'] . '/edit') ?>"
                                       class="text-decoration-none fw-semibold">
                                        <?= esc($item['product_name']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="fw-semibold"><?= esc($item['product_name']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted"><?= esc($item['unit']) ?></td>
                            <td class="text-end"><?= number_format((float)$item['quantity'], 3) ?></td>
                            <td class="text-end"><?= $cur ?> <?= number_format((float)$item['unit_price'], 2) ?></td>
                            <td class="text-end text-muted"><?= $cur ?> <?= number_format((float)($item['vat'] ?? 0), 2) ?></td>
                            <td class="text-end fw-semibold">
                                <?= $cur ?> <?= number_format((float)$item['quantity'] * (float)$item['unit_price'] + (float)($item['vat'] ?? 0), 2) ?>
                            </td>
                            <td class="text-muted"><?= esc($item['reason'] ?: '—') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background:#f7f8fc;">
                            <td colspan="6" class="text-end fw-bold">Total Return Amount</td>
                            <td class="text-end fw-bold" style="font-size:1.05rem;color:#FA896B;">
                                <?= $cur ?> <?= number_format((float)$grv['total_amount'], 2) ?>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Right: summary + actions ─────────────────────────── -->
    <div class="col-md-4">
        <div class="pd-card mb-3">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;">Summary</h6>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Items</span>
                <span class="fw-semibold"><?= count($grv_items) ?></span>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">Total Returned</span>
                <span class="fw-bold" style="font-size:1.4rem;color:#FA896B;">
                    <?= $cur ?> <?= number_format((float)$grv['total_amount'], 2) ?>
                </span>
            </div>

            <?php if ($grv['status'] === 'approved'): ?>
            <div class="mt-3 p-2 rounded" style="background:#e6f9f0;font-size:.82rem;color:#1a7a45;">
                <i class="bi bi-check-circle-fill me-1"></i>
                Stock updated when approved.
            </div>
            <?php endif; ?>
        </div>

        <!-- Timeline / Audit -->
        <div class="pd-card mb-3">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;">Activity</h6>
            <div style="font-size:.82rem;">
                <div class="d-flex gap-2 mb-2">
                    <i class="bi bi-plus-circle text-primary mt-1"></i>
                    <div>
                        <div class="fw-semibold">Created</div>
                        <div class="text-muted"><?= esc($grv['created_at'] ?? '-') ?></div>
                    </div>
                </div>
                <?php if ($grv['status'] === 'approved'): ?>
                <div class="d-flex gap-2">
                    <i class="bi bi-check-circle text-success mt-1"></i>
                    <div>
                        <div class="fw-semibold">Approved</div>
                        <div class="text-muted"><?= esc($grv['updated_at'] ?? '-') ?></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="pd-card no-print">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;">Actions</h6>
            <div class="d-grid gap-2">
                <?php if ($grv['status'] === 'draft'): ?>
                    <a href="<?= site_url('admin/grv/' . $grv['un_id'] . '/edit') ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit GRV
                    </a>
                    <form method="post" action="<?= site_url('admin/grv/' . $grv['un_id'] . '/approve') ?>"
                          onsubmit="return confirm('Approve this GRV? This will update stock.')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-check-circle me-1"></i>Approve &amp; Update Stock
                        </button>
                    </form>
                <?php endif; ?>
                <?php if ($customer): ?>
                    <a href="<?= site_url('admin/grv/create?customer_un_id=' . $customer['un_id']) ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-plus me-1"></i>New GRV for Same Customer
                    </a>
                <?php endif; ?>
                <form method="post" action="<?= site_url('admin/grv/' . $grv['un_id'] . '/delete') ?>"
                      onsubmit="return confirm('Permanently delete this GRV?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-trash me-1"></i>Delete GRV
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
