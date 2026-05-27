<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header">
    <div>
        <h4><?= esc($title) ?></h4>
        <ul class="mz-breadcrumb"><li>Operations</li><li><a href="<?= site_url('admin/stock') ?>">Stock</a></li><li><?= esc($title) ?></li></ul>
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
                <label class="form-label">Item Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="item_name" value="<?= esc(old('item_name', $item['item_name'] ?? '')) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <input type="text" class="form-control" name="category" list="categoryList" value="<?= esc(old('category', $item['category'] ?? '')) ?>">
                <datalist id="categoryList">
                    <?php foreach (($categories ?? []) as $cat): ?><option value="<?= esc($cat) ?>"><?php endforeach; ?>
                </datalist>
            </div>
            <div class="col-md-3">
                <label class="form-label">Unit <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="unit" list="unitList" value="<?= esc(old('unit', $item['unit'] ?? 'pcs')) ?>" required>
                <datalist id="unitList">
                    <option value="pcs"><option value="kg"><option value="litre"><option value="dozen"><option value="box"><option value="bag"><option value="meter">
                </datalist>
            </div>
            <div class="col-md-4">
                <label class="form-label">Current Quantity <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" class="form-control" name="current_qty" value="<?= esc(old('current_qty', $item['current_qty'] ?? '0')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Minimum Quantity (alert below)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="min_qty" value="<?= esc(old('min_qty', $item['min_qty'] ?? '0')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Unit Cost (৳)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="unit_cost" value="<?= esc(old('unit_cost', $item['unit_cost'] ?? '0')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="active" <?= (old('status', $item['status']??'active'))==='active'?'selected':'' ?>>Active</option>
                    <option value="inactive" <?= (old('status', $item['status']??''))==='inactive'?'selected':'' ?>>Inactive</option>
                </select>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Item</button>
            <a href="<?= site_url('admin/stock') ?>" class="btn btn-light">Cancel</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
