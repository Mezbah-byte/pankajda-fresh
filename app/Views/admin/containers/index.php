<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Containers / Imports</h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li>Containers</li>
        </ul>
    </div>
    <a href="<?= site_url('admin/containers/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Container
    </a>
</div>

<!-- Summary bar -->
<div class="pd-card py-3 px-4 mb-3" style="background:linear-gradient(135deg,#ECF2FF,#f0f4ff);">
    <div class="d-flex flex-wrap gap-4">
        <div>
            <span class="text-muted" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">Total</span>
            <div class="fw-bold" style="font-size:1.1rem;color:var(--mz-text-primary);"><?= number_format($totals['count']) ?></div>
        </div>
        <div>
            <span class="text-muted" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">Cleared</span>
            <div class="fw-bold" style="font-size:1.1rem;color:#02a98f;"><?= number_format($totals['cleared']) ?></div>
        </div>
        <div>
            <span class="text-muted" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">Total Cost</span>
            <div class="fw-bold" style="font-size:1.1rem;color:var(--mz-text-primary);">৳ <?= number_format($totals['cost'], 0) ?></div>
        </div>
    </div>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" class="form-control" name="q" placeholder="Search container, BL, product…" value="<?= esc($filters['q'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <select name="customs_status" class="form-select">
                <option value="">All customs status</option>
                <option value="pending" <?= ($filters['customs_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="cleared" <?= ($filters['customs_status'] ?? '') === 'cleared' ? 'selected' : '' ?>>Cleared</option>
                <option value="held"    <?= ($filters['customs_status'] ?? '') === 'held'    ? 'selected' : '' ?>>Held</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All status</option>
                <option value="in_transit" <?= ($filters['status'] ?? '') === 'in_transit' ? 'selected' : '' ?>>In Transit</option>
                <option value="received"   <?= ($filters['status'] ?? '') === 'received'   ? 'selected' : '' ?>>Received</option>
                <option value="sold"       <?= ($filters['status'] ?? '') === 'sold'       ? 'selected' : '' ?>>Sold</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr><th>Container</th><th>Product</th><th>Origin</th><th>Arrival</th><th>Customs</th><th class="text-end">Qty / Damaged</th><th class="text-end">Cost</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach (($containers ?? []) as $c): ?>
                    <tr>
                        <td>
                            <a href="<?= site_url('admin/containers/' . $c['un_id']) ?>" class="fw-semibold text-decoration-none" style="color:var(--mz-primary);"><?= esc($c['container_number']) ?></a>
                            <?php if (! empty($c['bl_number'])): ?>
                                <div class="text-muted" style="font-size:.75rem;">BL: <?= esc($c['bl_number']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($c['product_name'] ?? '-') ?></td>
                        <td><?= esc($c['origin_country'] ?? '-') ?></td>
                        <td><?= esc($c['arrival_date'] ?? '-') ?></td>
                        <td>
                            <?php $cs = $c['customs_status'] ?? 'pending'; ?>
                            <?php if ($cs === 'cleared'): ?>
                                <span class="badge-success-soft">Cleared</span>
                            <?php elseif ($cs === 'held'): ?>
                                <span class="badge-danger-soft">Held</span>
                            <?php else: ?>
                                <span class="badge-warning-soft">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end" style="font-size:.82rem;">
                            <?= number_format((float) $c['total_products'], 0) ?> <?= esc($c['unit'] ?? '') ?>
                            / <span style="color:#FA896B;"><?= number_format((float) $c['damaged_products'], 0) ?></span>
                        </td>
                        <td class="text-end">৳ <?= number_format((float) $c['cost_total'], 0) ?></td>
                        <td><span class="badge-secondary-soft"><?= esc(str_replace('_', ' ', ucfirst($c['status'] ?? ''))) ?></span></td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/containers/' . $c['un_id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/containers/' . $c['un_id'] . '/edit') ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($containers)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-box-seam" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
                            <span class="text-muted">No containers yet.</span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (! empty($pagination) && $pagination['last_page'] > 1): ?>
        <nav class="d-flex justify-content-end mt-3">
            <ul class="pagination pagination-sm m-0">
                <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                    <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
