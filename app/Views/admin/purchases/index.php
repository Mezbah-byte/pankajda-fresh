<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $cur = (new \App\Services\SettingService())->get('finance.currency_symbol', '৳'); ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-cart-plus me-2"></i>Purchases</h4>
        <ul class="mz-breadcrumb"><li>Business</li><li>Purchases</li></ul>
    </div>
    <a href="<?= site_url('admin/purchases/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>New Purchase
    </a>
</div>

<?php if (session('success')): ?><div class="alert alert-success mb-3"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="alert alert-danger mb-3"><?= esc(session('error')) ?></div><?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="pd-stat gradient-1">
            <div class="stat-label">Total Purchases</div>
            <div class="stat-value"><?= (int) $totals['count'] ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pd-stat gradient-2">
            <div class="stat-label">Total Amount</div>
            <div class="stat-value"><?= $cur ?> <?= number_format($totals['total_amount'], 2) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pd-stat gradient-4">
            <div class="stat-label">Outstanding Payable</div>
            <div class="stat-value"><?= $cur ?> <?= number_format($totals['total_due'], 2) ?></div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="pd-card mb-3">
    <form method="get" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label" style="font-size:.78rem;">Search</label>
            <input type="text" name="q" class="form-control form-control-sm"
                   placeholder="Purchase no…" value="<?= esc($filters['q'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label" style="font-size:.78rem;">Vendor</label>
            <select name="vendor_un_id" class="form-select form-select-sm">
                <option value="">All vendors</option>
                <?php foreach (($vendors ?? []) as $v): ?>
                    <option value="<?= esc($v['un_id']) ?>"
                        <?= ($filters['vendor_un_id'] ?? '') === $v['un_id'] ? 'selected' : '' ?>>
                        <?= esc($v['vendor_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-size:.78rem;">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All</option>
                <option value="draft"    <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="received" <?= ($filters['status'] ?? '') === 'received' ? 'selected' : '' ?>>Received</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="pd-card">
    <div class="table-responsive">
        <table class="table align-middle" style="font-size:.875rem;">
            <thead>
                <tr>
                    <th>Purchase No</th>
                    <th>Date</th>
                    <th>Vendor</th>
                    <th class="text-center">Items</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Due</th>
                    <th>Status</th>
                    <th class="text-end"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($purchases as $p): ?>
                <tr>
                    <td>
                        <a href="<?= site_url('admin/purchases/' . $p['un_id']) ?>" class="fw-semibold text-decoration-none">
                            <?= esc($p['purchase_no']) ?>
                        </a>
                    </td>
                    <td class="text-muted"><?= esc($p['purchase_date']) ?></td>
                    <td><?= esc($p['vendor_name']) ?></td>
                    <td class="text-center"><span class="badge bg-secondary-subtle text-secondary"><?= (int) $p['item_count'] ?></span></td>
                    <td class="text-end fw-semibold"><?= $cur ?> <?= number_format((float)$p['total_amount'], 2) ?></td>
                    <td class="text-end <?= (float)$p['due_amount'] > 0 ? 'text-danger fw-semibold' : 'text-muted' ?>">
                        <?= $cur ?> <?= number_format((float)$p['due_amount'], 2) ?>
                    </td>
                    <td>
                        <span class="badge bg-<?= $p['status'] === 'received' ? 'success' : 'secondary' ?>-subtle
                              text-<?= $p['status'] === 'received' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($p['status']) ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="<?= site_url('admin/purchases/' . $p['un_id']) ?>" class="btn btn-sm btn-light">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($purchases)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">
                        No purchases yet. <a href="<?= site_url('admin/purchases/create') ?>">Create first purchase</a>.
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (($pagination['last_page'] ?? 1) > 1): ?>
    <nav class="mt-3">
        <ul class="pagination pagination-sm mb-0">
            <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                <li class="page-item <?= $i === $pagination['page'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= site_url('admin/purchases') ?>?page=<?= $i ?>&q=<?= esc($filters['q'] ?? '') ?>&status=<?= esc($filters['status'] ?? '') ?>&vendor_un_id=<?= esc($filters['vendor_un_id'] ?? '') ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
