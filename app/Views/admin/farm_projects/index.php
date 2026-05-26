<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Farm Projects</h4>
        <p class="text-muted small m-0">
            <?= number_format($totals['count']) ?> projects &middot;
            Cost ৳ <?= number_format($totals['cost'], 0) ?> &middot;
            Sales ৳ <?= number_format($totals['sale'], 0) ?> &middot;
            Profit <span class="<?= $totals['profit'] >= 0 ? 'text-success' : 'text-danger' ?> fw-semibold">৳ <?= number_format($totals['profit'], 0) ?></span>
        </p>
    </div>
    <a href="<?= site_url('admin/farm-projects/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>New Project
    </a>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-5"><input type="text" class="form-control" name="q" placeholder="Search project, crop..." value="<?= esc($filters['q'] ?? '') ?>"></div>
        <div class="col-md-3">
            <select name="company_un_id" class="form-select">
                <option value="">All companies</option>
                <?php foreach ($companies as $c): ?>
                    <option value="<?= esc($c['un_id']) ?>" <?= ($filters['company_un_id'] ?? '') === $c['un_id'] ? 'selected' : '' ?>><?= esc($c['company_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All status</option>
                <option value="active"    <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Project</th><th>Crop</th><th>Land</th><th class="text-end">Cost</th><th class="text-end">Sale</th><th class="text-end">Profit</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                <?php foreach (($projects ?? []) as $p): ?>
                    <tr>
                        <td><a href="<?= site_url('admin/farm-projects/' . $p['un_id']) ?>" class="fw-semibold text-decoration-none"><?= esc($p['project_name']) ?></a></td>
                        <td><?= esc($p['crop_name'] ?? '-') ?></td>
                        <td class="small"><?= number_format((float) $p['land_size'], 2) ?> <?= esc($p['land_unit'] ?? '') ?></td>
                        <td class="text-end">৳ <?= number_format((float) $p['total_cost'], 0) ?></td>
                        <td class="text-end text-success">৳ <?= number_format((float) $p['sale_amount'], 0) ?></td>
                        <td class="text-end fw-semibold <?= ((float) $p['profit']) >= 0 ? 'text-success' : 'text-danger' ?>">৳ <?= number_format((float) $p['profit'], 0) ?></td>
                        <td><span class="badge bg-light text-dark"><?= esc(ucfirst($p['status'] ?? 'active')) ?></span></td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/farm-projects/' . $p['un_id']) ?>" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/farm-projects/' . $p['un_id'] . '/edit') ?>" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($projects)): ?>
                    <tr><td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-tree" style="font-size:2.5rem;color:#cbcae3;"></i>
                        <p class="mt-2 mb-0">No farm projects yet.</p>
                    </td></tr>
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
