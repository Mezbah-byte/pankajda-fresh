<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Farm Projects</h4>
        <ul class="mz-breadcrumb">
            <li>Operations</li>
            <li>Farm Projects</li>
        </ul>
    </div>
    <a href="<?= site_url('admin/farm-projects/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>New Project
    </a>
</div>

<!-- Summary bar -->
<div class="pd-card py-3 px-4 mb-3" style="background:linear-gradient(135deg,#E6FFFA,#f0fdf8);">
    <div class="d-flex flex-wrap gap-4">
        <div>
            <span class="text-muted" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">Projects</span>
            <div class="fw-bold" style="font-size:1.1rem;color:var(--mz-text-primary);"><?= number_format($totals['count']) ?></div>
        </div>
        <div>
            <span class="text-muted" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">Total Cost</span>
            <div class="fw-bold" style="font-size:1.1rem;color:var(--mz-text-primary);">৳ <?= number_format($totals['cost'], 0) ?></div>
        </div>
        <div>
            <span class="text-muted" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">Total Sales</span>
            <div class="fw-bold" style="font-size:1.1rem;color:var(--mz-text-primary);">৳ <?= number_format($totals['sale'], 0) ?></div>
        </div>
        <div>
            <span class="text-muted" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">Profit</span>
            <div class="fw-bold" style="font-size:1.1rem;color:<?= $totals['profit'] >= 0 ? '#02a98f' : '#FA896B' ?>;">৳ <?= number_format($totals['profit'], 0) ?></div>
        </div>
    </div>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="text" class="form-control" name="q" placeholder="Search project, crop…" value="<?= esc($filters['q'] ?? '') ?>">
        </div>
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
                <option value="active"    <?= ($filters['status'] ?? '') === 'active'    ? 'selected' : '' ?>>Active</option>
                <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr><th>Project</th><th>Crop</th><th>Land</th><th class="text-end">Cost</th><th class="text-end">Sale</th><th class="text-end">Profit</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach (($projects ?? []) as $p): ?>
                    <tr>
                        <td>
                            <a href="<?= site_url('admin/farm-projects/' . $p['un_id']) ?>" class="fw-semibold text-decoration-none" style="color:var(--mz-primary);"><?= esc($p['project_name']) ?></a>
                        </td>
                        <td><?= esc($p['crop_name'] ?? '-') ?></td>
                        <td style="font-size:.82rem;"><?= number_format((float) $p['land_size'], 2) ?> <?= esc($p['land_unit'] ?? '') ?></td>
                        <td class="text-end">৳ <?= number_format((float) $p['total_cost'], 0) ?></td>
                        <td class="text-end" style="color:#02a98f;">৳ <?= number_format((float) $p['sale_amount'], 0) ?></td>
                        <td class="text-end fw-semibold" style="color:<?= ((float) $p['profit']) >= 0 ? '#02a98f' : '#FA896B' ?>;">
                            ৳ <?= number_format((float) $p['profit'], 0) ?>
                        </td>
                        <td><span class="badge-secondary-soft"><?= esc(ucfirst($p['status'] ?? 'active')) ?></span></td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/farm-projects/' . $p['un_id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/farm-projects/' . $p['un_id'] . '/edit') ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($projects)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-tree" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
                            <span class="text-muted">No farm projects yet.</span>
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
                    <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a></li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
