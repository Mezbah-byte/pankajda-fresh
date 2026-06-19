<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $cur = (new \App\Services\SettingService())->get('finance.currency_symbol', '৳'); ?>

<div class="mz-page-header d-flex align-items-center justify-content-between no-print">
    <div>
        <h4><i class="bi bi-cart-plus me-2"></i><?= esc($purchase['purchase_no']) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/purchases') ?>" class="text-muted text-decoration-none">Purchases</a></li>
            <li><?= esc($purchase['purchase_no']) ?></li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <?php if ($purchase['status'] === 'draft'): ?>
            <form method="post" action="<?= site_url('admin/purchases/' . $purchase['un_id'] . '/receive') ?>"
                  onsubmit="return confirm('Receive this purchase? Stock and vendor payable will be updated.')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success"><i class="bi bi-box-arrow-in-down me-1"></i>Receive</button>
            </form>
        <?php endif; ?>
        <button onclick="window.print()" class="btn btn-light"><i class="bi bi-printer me-1"></i>Print</button>
        <a href="<?= site_url('admin/purchases') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<?php if (session('success')): ?><div class="alert alert-success mb-3 no-print"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="alert alert-danger mb-3 no-print"><?= esc(session('error')) ?></div><?php endif; ?>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card mb-3">
            <div class="d-flex align-items-start justify-content-between mb-4">
                <h6 class="fw-semibold mb-0" style="color:var(--mz-text-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;">Purchase Bill</h6>
                <span class="badge bg-<?= $purchase['status'] === 'received' ? 'success' : 'secondary' ?>-subtle
                      text-<?= $purchase['status'] === 'received' ? 'success' : 'secondary' ?> px-3 py-2" style="font-size:.82rem;">
                    <?= ucfirst($purchase['status']) ?>
                </span>
            </div>
            <div class="row g-3 mb-2">
                <div class="col-sm-6">
                    <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;">Purchase No</div>
                    <div class="fw-bold" style="font-size:1.1rem;"><?= esc($purchase['purchase_no']) ?></div>
                </div>
                <div class="col-sm-6">
                    <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;">Date</div>
                    <div class="fw-semibold"><?= esc($purchase['purchase_date']) ?></div>
                </div>
                <div class="col-sm-6">
                    <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;">Vendor</div>
                    <div class="fw-semibold">
                        <?php if ($vendor): ?>
                            <a href="<?= site_url('admin/vendors/' . $vendor['un_id']) ?>" class="text-decoration-none">
                                <?= esc($vendor['vendor_name']) ?>
                            </a>
                        <?php else: ?>—<?php endif; ?>
                    </div>
                </div>
                <?php if ($company): ?>
                <div class="col-sm-6">
                    <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.4px;">Company</div>
                    <div class="fw-semibold"><?= esc($company['company_name']) ?></div>
                </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($purchase['notes'])): ?>
                <div class="p-3 rounded mt-2" style="background:#f7f8fc;font-size:.88rem;"><?= nl2br(esc($purchase['notes'])) ?></div>
            <?php endif; ?>
        </div>

        <div class="pd-card">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;">
                Items <span class="badge bg-secondary ms-1"><?= count($purchase_items) ?></span>
            </h6>
            <div class="table-responsive">
                <table class="table align-middle" style="font-size:.875rem;">
                    <thead>
                        <tr>
                            <th>#</th><th>Product</th><th>Unit</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Unit Cost</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchase_items as $idx => $item): ?>
                        <tr>
                            <td class="text-muted"><?= $idx + 1 ?></td>
                            <td class="fw-semibold">
                                <?php if ($item['product_un_id']): ?>
                                    <a href="<?= site_url('admin/products/' . $item['product_un_id'] . '/edit') ?>" class="text-decoration-none">
                                        <?= esc($item['product_name']) ?>
                                    </a>
                                <?php else: ?>
                                    <?= esc($item['product_name']) ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted"><?= esc($item['unit']) ?></td>
                            <td class="text-end"><?= number_format((float)$item['quantity'], 3) ?></td>
                            <td class="text-end"><?= $cur ?> <?= number_format((float)$item['unit_cost'], 2) ?></td>
                            <td class="text-end fw-semibold"><?= $cur ?> <?= number_format((float)$item['total'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background:#f7f8fc;">
                            <td colspan="5" class="text-end fw-bold">Subtotal</td>
                            <td class="text-end fw-semibold"><?= $cur ?> <?= number_format((float)$purchase['subtotal'], 2) ?></td>
                        </tr>
                        <?php if ((float)$purchase['discount'] > 0): ?>
                        <tr style="background:#f7f8fc;">
                            <td colspan="5" class="text-end">Discount</td>
                            <td class="text-end text-success">− <?= $cur ?> <?= number_format((float)$purchase['discount'], 2) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr style="background:#f7f8fc;">
                            <td colspan="5" class="text-end fw-bold">Total Bill</td>
                            <td class="text-end fw-bold" style="font-size:1.05rem;"><?= $cur ?> <?= number_format((float)$purchase['total_amount'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="pd-card mb-3">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;">Payment</h6>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Total Bill</span>
                <span class="fw-semibold"><?= $cur ?> <?= number_format((float)$purchase['total_amount'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Paid</span>
                <span class="fw-semibold text-success"><?= $cur ?> <?= number_format((float)$purchase['paid_amount'], 2) ?></span>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between">
                <span class="fw-bold">Due to Vendor</span>
                <span class="fw-bold" style="font-size:1.2rem;color:#FA896B;">
                    <?= $cur ?> <?= number_format((float)$purchase['due_amount'], 2) ?>
                </span>
            </div>
            <?php if ((float)$purchase['due_amount'] > 0 && $vendor): ?>
                <a href="<?= site_url('admin/vendors/' . $vendor['un_id']) ?>" class="btn btn-outline-success btn-sm w-100 mt-3 no-print">
                    <i class="bi bi-cash me-1"></i>Pay on Vendor Page
                </a>
            <?php endif; ?>
        </div>

        <div class="pd-card no-print">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;">Actions</h6>
            <div class="d-grid gap-2">
                <?php if ($purchase['status'] === 'draft'): ?>
                    <form method="post" action="<?= site_url('admin/purchases/' . $purchase['un_id'] . '/receive') ?>"
                          onsubmit="return confirm('Receive this purchase? Stock and vendor payable will be updated.')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-box-arrow-in-down me-1"></i>Receive — Stock In
                        </button>
                    </form>
                <?php endif; ?>
                <?php if ($vendor): ?>
                    <a href="<?= site_url('admin/purchases/create?vendor_un_id=' . $vendor['un_id']) ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-plus me-1"></i>New Purchase — Same Vendor
                    </a>
                <?php endif; ?>
                <form method="post" action="<?= site_url('admin/purchases/' . $purchase['un_id'] . '/delete') ?>"
                      onsubmit="return confirm('Delete this purchase? Received stock and vendor payable will be reversed.')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-trash me-1"></i>Delete Purchase
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
