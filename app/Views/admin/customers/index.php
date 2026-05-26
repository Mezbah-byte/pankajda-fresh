<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Customers</h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li>Customers</li>
        </ul>
    </div>
    <a href="<?= site_url('admin/customers/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Customer
    </a>
</div>

<!-- Summary bar -->
<div class="pd-card py-3 px-4 mb-3" style="background:linear-gradient(135deg,#ECF2FF,#f0f4ff);">
    <div class="d-flex flex-wrap gap-4">
        <div>
            <span class="text-muted" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">Total Customers</span>
            <div class="fw-bold" style="font-size:1.1rem;color:var(--mz-text-primary);"><?= number_format($totals['total'] ?? 0) ?></div>
        </div>
        <div>
            <span class="text-muted" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">Total Due</span>
            <div class="fw-bold" style="font-size:1.1rem;color:#FA896B;">৳ <?= number_format($totals['total_due'] ?? 0, 0) ?></div>
        </div>
    </div>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-6">
            <input type="text" class="form-control" name="q" placeholder="Search by name, phone, email, code…" value="<?= esc($filters['q'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <div class="form-check pt-2">
                <input class="form-check-input" type="checkbox" name="has_due" value="1" id="hasDue" <?= ! empty($filters['has_due']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="hasDue" style="font-size:.875rem;">Show only customers with due</label>
            </div>
        </div>
        <div class="col-md-3">
            <button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr><th>Customer</th><th>Phone</th><th>Email</th><th>City</th><th class="text-end">Due</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach (($customers ?? []) as $c): ?>
                    <tr>
                        <td>
                            <a href="<?= site_url('admin/customers/' . $c['un_id']) ?>" class="fw-semibold text-decoration-none" style="color:var(--mz-primary);"><?= esc($c['customer_name']) ?></a>
                            <div class="text-muted" style="font-size:.75rem;"><?= esc($c['customer_code'] ?? short_un_id($c['un_id'])) ?></div>
                        </td>
                        <td><?= esc($c['phone'] ?? '-') ?></td>
                        <td><?= esc($c['email'] ?? '-') ?></td>
                        <td><?= esc($c['city'] ?? '-') ?></td>
                        <td class="text-end">
                            <?php if ((float) $c['current_due'] > 0): ?>
                                <span class="fw-semibold" style="color:#FA896B;">৳ <?= number_format((float) $c['current_due'], 0) ?></span>
                            <?php else: ?>
                                <span class="text-muted">৳ 0</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (($c['status'] ?? '') === 'active'): ?>
                                <span class="badge-success-soft">Active</span>
                            <?php else: ?>
                                <span class="badge-secondary-soft"><?= esc(ucfirst($c['status'] ?? 'active')) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/customers/' . $c['un_id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/customers/' . $c['un_id'] . '/edit') ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-people" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
                            <span class="text-muted">No customers yet.</span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (! empty($pagination) && $pagination['last_page'] > 1): ?>
        <nav class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted" style="font-size:.82rem;">Showing <?= count($customers) ?> of <?= $pagination['total'] ?></div>
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
