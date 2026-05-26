<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Customers</h4>
        <p class="text-muted small m-0">
            <?= number_format($totals['total'] ?? 0) ?> customers &middot;
            Total due <span class="text-danger fw-semibold">৳ <?= number_format($totals['total_due'] ?? 0, 0) ?></span>
        </p>
    </div>
    <a href="<?= site_url('admin/customers/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Customer
    </a>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-6"><input type="text" class="form-control" name="q" placeholder="Search by name, phone, email, code..." value="<?= esc($filters['q'] ?? '') ?>"></div>
        <div class="col-md-3">
            <div class="form-check pt-2">
                <input class="form-check-input" type="checkbox" name="has_due" value="1" id="hasDue" <?= ! empty($filters['has_due']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="hasDue">Show only customers with due</label>
            </div>
        </div>
        <div class="col-md-3"><button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Customer</th><th>Phone</th><th>Email</th><th>City</th><th class="text-end">Due</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                <?php foreach (($customers ?? []) as $c): ?>
                    <tr>
                        <td>
                            <a href="<?= site_url('admin/customers/' . $c['un_id']) ?>" class="fw-semibold text-decoration-none"><?= esc($c['customer_name']) ?></a>
                            <div class="small text-muted"><?= esc($c['customer_code'] ?? short_un_id($c['un_id'])) ?></div>
                        </td>
                        <td><?= esc($c['phone'] ?? '-') ?></td>
                        <td><?= esc($c['email'] ?? '-') ?></td>
                        <td><?= esc($c['city'] ?? '-') ?></td>
                        <td class="text-end <?= ((float) $c['current_due'] > 0) ? 'text-danger fw-semibold' : '' ?>">৳ <?= number_format((float) $c['current_due'], 0) ?></td>
                        <td>
                            <span class="badge badge-status <?= ($c['status'] ?? '') === 'active' ? 'bg-success' : 'bg-secondary' ?>"><?= esc(ucfirst($c['status'] ?? 'active')) ?></span>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/customers/' . $c['un_id']) ?>" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/customers/' . $c['un_id'] . '/edit') ?>" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($customers)): ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-people" style="font-size:2.5rem;color:#cbcae3;"></i>
                        <p class="mt-2 mb-0">No customers yet.</p>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (! empty($pagination) && $pagination['last_page'] > 1): ?>
        <nav class="d-flex justify-content-between align-items-center mt-3">
            <div class="small text-muted">Showing <?= count($customers) ?> of <?= $pagination['total'] ?></div>
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
