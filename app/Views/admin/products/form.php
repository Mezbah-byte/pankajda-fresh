<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header">
    <div>
        <h4><?= esc($title) ?></h4>
        <ul class="mz-breadcrumb"><li>Catalog</li><li><a href="<?= site_url('admin/products') ?>">Products</a></li><li><?= esc($title) ?></li></ul>
    </div>
</div>

<?php if (session('errors')): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach (session('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="pd-card">
    <form method="post" action="<?= esc($action) ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="product_name" value="<?= esc(old('product_name', $product['product_name'] ?? '')) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">SKU</label>
                <input type="text" class="form-control" name="sku" value="<?= esc(old('sku', $product['sku'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Unit</label>
                <input type="text" class="form-control" name="unit" list="unitList" value="<?= esc(old('unit', $product['unit'] ?? 'pcs')) ?>">
                <datalist id="unitList"><option value="pcs"><option value="kg"><option value="litre"><option value="dozen"><option value="box"></datalist>
            </div>
            <div class="col-md-4">
                <label class="form-label">Category</label>
                <input type="text" class="form-control" name="category" list="catList" value="<?= esc(old('category', $product['category'] ?? '')) ?>">
                <datalist id="catList">
                    <?php foreach (($categories ?? []) as $cat): ?><option value="<?= esc($cat) ?>"><?php endforeach; ?>
                </datalist>
            </div>
            <div class="col-md-4">
                <label class="form-label">Sale Price (৳) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" class="form-control" name="sale_price" value="<?= esc(old('sale_price', $product['sale_price'] ?? '0')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Cost Price (৳)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="cost_price" value="<?= esc(old('cost_price', $product['cost_price'] ?? '0')) ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"><?= esc(old('description', $product['description'] ?? '')) ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="active" <?= (old('status', $product['status']??'active'))==='active'?'selected':'' ?>>Active</option>
                    <option value="inactive" <?= (old('status', $product['status']??''))==='inactive'?'selected':'' ?>>Inactive</option>
                </select>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Product</button>
            <a href="<?= site_url('admin/products') ?>" class="btn btn-light">Cancel</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
