<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Companies</h4>
        <p class="text-muted small m-0">Manage all your business entities in one place</p>
    </div>
    <a href="<?= site_url('admin/companies/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Company
    </a>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-5"><input type="text" class="form-control" name="q" placeholder="Search by name, email, phone..." value="<?= esc($filters['q'] ?? '') ?>"></div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
        <div class="col-md-2"><a href="<?= site_url('admin/companies') ?>" class="btn btn-link text-decoration-none w-100">Reset</a></div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Company</th><th>Type</th><th>Phone</th><th>Email</th><th>Currency</th><th>Status</th><th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($companies ?? []) as $c): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;font-weight:700;"><?= esc(strtoupper(substr($c['company_name'], 0, 1))) ?></div>
                                <div>
                                    <div class="fw-semibold"><?= esc($c['company_name']) ?></div>
                                    <div class="small text-muted"><?= esc(short_un_id($c['un_id'])) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="text-muted"><?= esc($c['company_type'] ?? '-') ?></span></td>
                        <td><?= esc($c['phone'] ?? '-') ?></td>
                        <td><?= esc($c['email'] ?? '-') ?></td>
                        <td><span class="badge bg-light text-dark"><?= esc($c['currency'] ?? 'BDT') ?></span></td>
                        <td>
                            <?php $st = $c['status'] ?? 'active'; ?>
                            <span class="badge badge-status <?= $st === 'active' ? 'bg-success' : ($st === 'pending' ? 'bg-warning' : 'bg-secondary') ?>"><?= esc(ucfirst($st)) ?></span>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/companies/' . $c['un_id']) ?>" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/companies/' . $c['un_id'] . '/edit') ?>" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= site_url('admin/companies/' . $c['un_id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this company?');">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($companies)): ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-buildings" style="font-size:2.5rem;color:#cbcae3;"></i>
                        <p class="mt-2 mb-0">No companies yet. Click "Add Company" to get started.</p>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (! empty($pagination) && $pagination['last_page'] > 1): ?>
        <nav class="d-flex justify-content-between align-items-center mt-3">
            <div class="small text-muted">Showing <?= count($companies) ?> of <?= $pagination['total'] ?></div>
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
