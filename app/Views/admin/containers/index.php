<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Containers / Imports</h4>
        <p class="text-muted small m-0">
            <?= number_format($totals['count']) ?> containers &middot;
            <?= number_format($totals['cleared']) ?> cleared &middot;
            Total cost <span class="fw-semibold">৳ <?= number_format($totals['cost'], 0) ?></span>
        </p>
    </div>
    <a href="<?= site_url('admin/containers/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Container
    </a>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-4"><input type="text" class="form-control" name="q" placeholder="Search container, BL, product..." value="<?= esc($filters['q'] ?? '') ?>"></div>
        <div class="col-md-3">
            <select name="customs_status" class="form-select">
                <option value="">All customs status</option>
                <option value="pending" <?= ($filters['customs_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="cleared" <?= ($filters['customs_status'] ?? '') === 'cleared' ? 'selected' : '' ?>>Cleared</option>
                <option value="held"    <?= ($filters['customs_status'] ?? '') === 'held' ? 'selected' : '' ?>>Held</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All status</option>
                <option value="in_transit" <?= ($filters['status'] ?? '') === 'in_transit' ? 'selected' : '' ?>>In Transit</option>
                <option value="received"   <?= ($filters['status'] ?? '') === 'received' ? 'selected' : '' ?>>Received</option>
                <option value="sold"       <?= ($filters['status'] ?? '') === 'sold' ? 'selected' : '' ?>>Sold</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Container</th><th>Product</th><th>Origin</th><th>Arrival</th><th>Customs</th><th class="text-end">Total / Damaged</th><th class="text-end">Cost</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                <?php foreach (($containers ?? []) as $c): ?>
                    <tr>
                        <td>
                            <a href="<?= site_url('admin/containers/' . $c['un_id']) ?>" class="fw-semibold text-decoration-none"><?= esc($c['container_number']) ?></a>
                            <?php if (! empty($c['bl_number'])): ?><div class="small text-muted">BL: <?= esc($c['bl_number']) ?></div><?php endif; ?>
                        </td>
                        <td><?= esc($c['product_name'] ?? '-') ?></td>
                        <td><?= esc($c['origin_country'] ?? '-') ?></td>
                        <td><?= esc($c['arrival_date'] ?? '-') ?></td>
                        <td>
                            <?php $cs = $c['customs_status'] ?? 'pending'; ?>
                            <span class="badge <?= $cs === 'cleared' ? 'bg-success' : ($cs === 'held' ? 'bg-danger' : 'bg-warning') ?>"><?= esc(ucfirst($cs)) ?></span>
                        </td>
                        <td class="text-end small"><?= number_format((float) $c['total_products'], 0) ?> <?= esc($c['unit'] ?? '') ?> / <span class="text-danger"><?= number_format((float) $c['damaged_products'], 0) ?></span></td>
                        <td class="text-end">৳ <?= number_format((float) $c['cost_total'], 0) ?></td>
                        <td><span class="badge bg-light text-dark"><?= esc(str_replace('_', ' ', ucfirst($c['status'] ?? ''))) ?></span></td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/containers/' . $c['un_id']) ?>" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/containers/' . $c['un_id'] . '/edit') ?>" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($containers)): ?>
                    <tr><td colspan="9" class="text-center py-5 text-muted">
                        <i class="bi bi-box-seam" style="font-size:2.5rem;color:#cbcae3;"></i>
                        <p class="mt-2 mb-0">No containers yet.</p>
                    </td></tr>
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
