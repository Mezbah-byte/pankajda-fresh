<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Stock / Inventory</h4>
        <ul class="mz-breadcrumb"><li>Operations</li><li>Stock</li></ul>
    </div>
    <a href="<?= site_url('admin/stock/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>New Item</a>
</div>

<?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>

<!-- Summary -->
<?php if (! empty($summary)): ?>
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="pd-stat gradient-1"><div class="stat-icon"><i class="bi bi-box-seam"></i></div><div class="stat-label">Total Items</div><div class="stat-value"><?= number_format($summary['total_items']) ?></div></div></div>
    <div class="col-md-3"><div class="pd-stat gradient-2"><div class="stat-icon"><i class="bi bi-currency-dollar"></i></div><div class="stat-label">Stock Value</div><div class="stat-value">৳ <?= number_format($summary['total_value'],0) ?></div></div></div>
    <div class="col-md-3"><div class="pd-stat <?= $summary['low_stock_count']>0 ? 'gradient-4' : 'gradient-3' ?>"><div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div><div class="stat-label">Low Stock</div><div class="stat-value"><?= $summary['low_stock_count'] ?></div></div></div>
    <div class="col-md-3">
        <div class="pd-card border h-100 d-flex flex-column justify-content-center p-3">
            <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Low Stock Alerts</div>
            <?php foreach (array_slice($low_stock??[], 0, 3) as $ls): ?>
                <div class="d-flex justify-content-between align-items-center py-1" style="border-bottom:1px solid var(--mz-border);">
                    <span style="font-size:.8rem;"><?= esc($ls['item_name']) ?></span>
                    <span class="badge bg-danger-subtle text-danger"><?= $ls['current_qty'] ?> <?= esc($ls['unit']) ?></span>
                </div>
            <?php endforeach; ?>
            <?php if (empty($low_stock)): ?><span class="text-success" style="font-size:.82rem;"><i class="bi bi-check-circle me-1"></i>All stocked</span><?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-4"><input type="text" class="form-control" name="q" placeholder="Search item…" value="<?= esc($filters['q']??'') ?>"></div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php foreach (($categories ?? []) as $cat): ?>
                    <option value="<?= esc($cat) ?>" <?= ($filters['category']??'')===$cat?'selected':'' ?>><?= esc(ucfirst($cat)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="active" <?= ($filters['status']??'')==='active'?'selected':'' ?>>Active</option>
                <option value="inactive" <?= ($filters['status']??'')==='inactive'?'selected':'' ?>>Inactive</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr><th>Item</th><th>Category</th><th>Unit</th><th class="text-end">Qty</th><th class="text-end">Min Qty</th><th class="text-end">Unit Cost</th><th class="text-end">Value</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach (($items ?? []) as $item): ?>
                    <?php $isLow = (float)($item['current_qty']??0) <= (float)($item['min_qty']??0); ?>
                    <tr <?= $isLow ? 'class="table-warning"' : '' ?>>
                        <td>
                            <div class="fw-semibold"><?= esc($item['item_name']) ?></div>
                            <?php if ($isLow): ?><div style="font-size:.72rem;color:#FA896B;"><i class="bi bi-exclamation-triangle me-1"></i>Low Stock</div><?php endif; ?>
                        </td>
                        <td><span class="badge-secondary-soft"><?= esc(ucfirst($item['category']??'general')) ?></span></td>
                        <td><?= esc($item['unit']??'pcs') ?></td>
                        <td class="text-end fw-semibold <?= $isLow?'text-danger':'' ?>"><?= number_format((float)($item['current_qty']??0),2) ?></td>
                        <td class="text-end text-muted"><?= number_format((float)($item['min_qty']??0),2) ?></td>
                        <td class="text-end">৳ <?= number_format((float)($item['unit_cost']??0),2) ?></td>
                        <td class="text-end fw-semibold">৳ <?= number_format(((float)($item['current_qty']??0))*((float)($item['unit_cost']??0)),2) ?></td>
                        <td><span class="badge bg-<?= ($item['status']??'active')==='active'?'success':'secondary' ?>-subtle text-<?= ($item['status']??'active')==='active'?'success':'secondary' ?>"><?= ucfirst($item['status']??'active') ?></span></td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/stock/' . $item['un_id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/stock/' . $item['un_id'] . '/edit') ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                    <tr><td colspan="9" class="text-center py-5"><i class="bi bi-box-seam" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i><span class="text-muted">No stock items found.</span></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (! empty($pagination) && $pagination['last_page'] > 1): ?>
        <nav class="d-flex justify-content-end mt-3">
            <ul class="pagination pagination-sm m-0">
                <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                    <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a></li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
