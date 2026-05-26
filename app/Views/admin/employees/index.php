<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Employees</h4>
        <p class="text-muted small m-0">
            <?= number_format($totals['count']) ?> employees &middot;
            <?= number_format($totals['active']) ?> active &middot;
            Monthly payroll <span class="fw-semibold">৳ <?= number_format($totals['monthly_payroll'], 0) ?></span>
        </p>
    </div>
    <a href="<?= site_url('admin/employees/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Employee
    </a>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-4"><input type="text" class="form-control" name="q" placeholder="Search name, code, phone, email..." value="<?= esc($filters['q'] ?? '') ?>"></div>
        <div class="col-md-3">
            <select name="company_un_id" class="form-select">
                <option value="">All companies</option>
                <?php foreach ($companies as $c): ?>
                    <option value="<?= esc($c['un_id']) ?>" <?= ($filters['company_un_id'] ?? '') === $c['un_id'] ? 'selected' : '' ?>><?= esc($c['company_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All status</option>
                <option value="active"   <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Employee</th><th>Designation</th><th>Department</th><th>Phone</th><th class="text-end">Salary</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                <?php foreach (($employees ?? []) as $e): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;font-weight:700;"><?= esc(strtoupper(substr($e['name'], 0, 1))) ?></div>
                                <div>
                                    <a href="<?= site_url('admin/employees/' . $e['un_id']) ?>" class="fw-semibold text-decoration-none"><?= esc($e['name']) ?></a>
                                    <div class="small text-muted"><?= esc($e['employee_code'] ?? short_un_id($e['un_id'])) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= esc($e['designation'] ?? '-') ?></td>
                        <td><span class="badge bg-light text-dark"><?= esc($e['department'] ?? '-') ?></span></td>
                        <td><?= esc($e['phone'] ?? '-') ?></td>
                        <td class="text-end fw-semibold">৳ <?= number_format((float) $e['salary'], 0) ?></td>
                        <td><span class="badge <?= ($e['status'] ?? '') === 'active' ? 'bg-success' : 'bg-secondary' ?>"><?= esc(ucfirst($e['status'] ?? 'active')) ?></span></td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/employees/' . $e['un_id']) ?>" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/employees/' . $e['un_id'] . '/edit') ?>" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($employees)): ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-person-badge" style="font-size:2.5rem;color:#cbcae3;"></i>
                        <p class="mt-2 mb-0">No employees yet.</p>
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
