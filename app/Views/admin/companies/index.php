<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Companies</h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li>Companies</li>
        </ul>
    </div>
    <a href="<?= site_url('admin/companies/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Company
    </a>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="text" class="form-control" name="q" placeholder="Search by name, email, phone…" value="<?= esc($filters['q'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                <option value="active"   <?= ($filters['status'] ?? '') === 'active'   ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="pending"  <?= ($filters['status'] ?? '') === 'pending'  ? 'selected' : '' ?>>Pending</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
        <div class="col-md-2">
            <a href="<?= site_url('admin/companies') ?>" class="btn btn-light w-100">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Currency</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($companies ?? []) as $c): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-2 fw-bold"
                                     style="width:38px;height:38px;background:#ECF2FF;color:#5D87FF;font-size:.9rem;flex-shrink:0;">
                                    <?= esc(strtoupper(substr($c['company_name'], 0, 1))) ?>
                                </div>
                                <div>
                                    <div class="fw-semibold"><?= esc($c['company_name']) ?></div>
                                    <div class="text-muted" style="font-size:.75rem;"><?= esc(short_un_id($c['un_id'])) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="text-muted"><?= esc($c['company_type'] ?? '-') ?></span></td>
                        <td><?= esc($c['phone'] ?? '-') ?></td>
                        <td><?= esc($c['email'] ?? '-') ?></td>
                        <td><span class="badge-secondary-soft"><?= esc($c['currency'] ?? 'BDT') ?></span></td>
                        <td>
                            <?php $st = $c['status'] ?? 'active'; ?>
                            <?php if ($st === 'active'): ?>
                                <span class="badge-success-soft">Active</span>
                            <?php elseif ($st === 'pending'): ?>
                                <span class="badge-warning-soft">Pending</span>
                            <?php else: ?>
                                <span class="badge-secondary-soft"><?= esc(ucfirst($st)) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/companies/' . $c['un_id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/companies/' . $c['un_id'] . '/edit') ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= site_url('admin/companies/' . $c['un_id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this company?');">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($companies)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-buildings" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
                            <span class="text-muted">No companies yet. Click "Add Company" to get started.</span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (! empty($pagination) && $pagination['last_page'] > 1): ?>
        <nav class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted" style="font-size:.82rem;">Showing <?= count($companies) ?> of <?= $pagination['total'] ?></div>
            <ul class="pagination pagination-sm m-0">
                <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                    <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($filters['q'] ?? '') ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
