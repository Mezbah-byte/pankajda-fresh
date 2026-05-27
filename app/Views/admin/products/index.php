<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Products</h4>
        <ul class="mz-breadcrumb"><li>Catalog</li><li>Products</li></ul>
    </div>
    <a href="<?= site_url('admin/products/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>New Product</a>
</div>

<?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-4"><input type="text" class="form-control" name="q" placeholder="Search product…" value="<?= esc($filters['q']??'') ?>"></div>
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
                <tr><th>Product</th><th>SKU</th><th>Category</th><th>Unit</th><th class="text-end">Sale Price</th><th class="text-end">Cost Price</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach (($products ?? []) as $p): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= esc($p['product_name']) ?></div>
                            <?php if (!empty($p['description'])): ?><div class="text-muted" style="font-size:.75rem;"><?= esc(mb_strimwidth($p['description'],0,60,'…')) ?></div><?php endif; ?>
                        </td>
                        <td class="text-muted"><?= esc($p['sku']??'-') ?></td>
                        <td><span class="badge-secondary-soft"><?= esc(ucfirst($p['category']??'general')) ?></span></td>
                        <td><?= esc($p['unit']??'pcs') ?></td>
                        <td class="text-end fw-semibold" style="color:var(--mz-primary);">৳ <?= number_format((float)($p['sale_price']??0),2) ?></td>
                        <td class="text-end text-muted">৳ <?= number_format((float)($p['cost_price']??0),2) ?></td>
                        <td><span class="badge bg-<?= ($p['status']??'active')==='active'?'success':'secondary' ?>-subtle text-<?= ($p['status']??'active')==='active'?'success':'secondary' ?>"><?= ucfirst($p['status']??'active') ?></span></td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/products/' . $p['un_id'] . '/edit') ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= site_url('admin/products/' . $p['un_id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete product?');">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                    <tr><td colspan="8" class="text-center py-5"><i class="bi bi-box2" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i><span class="text-muted">No products found.</span></td></tr>
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
