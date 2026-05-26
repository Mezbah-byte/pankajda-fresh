<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Expenses</h4>
        <p class="text-muted small m-0">
            <?= number_format($totals['count']) ?> records &middot;
            All time <span class="fw-semibold">৳ <?= number_format($totals['total'], 0) ?></span> &middot;
            This month <span class="fw-semibold text-danger">৳ <?= number_format($totals['month'], 0) ?></span>
        </p>
    </div>
    <a href="<?= site_url('admin/expenses/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add Expense</a>
</div>

<div class="row g-3 mb-3">
    <?php foreach (array_slice($byCategory ?? [], 0, 4) as $cat): ?>
        <div class="col-md-3">
            <div class="pd-stat gradient-<?= rand(1,6) ?>">
                <div class="stat-label"><?= esc(ucfirst($cat['category'])) ?></div>
                <div class="stat-value">৳ <?= number_format((float) $cat['total'], 0) ?></div>
                <div class="small text-muted"><?= number_format((int) $cat['count']) ?> entries</div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-3"><input type="text" class="form-control" name="q" placeholder="Search title..." value="<?= esc($filters['q'] ?? '') ?>"></div>
        <div class="col-md-2">
            <select name="category" class="form-select">
                <option value="">All categories</option>
                <?php foreach (($categories ?? []) as $cat): ?>
                    <option value="<?= esc($cat) ?>" <?= ($filters['category'] ?? '') === $cat ? 'selected' : '' ?>><?= esc(ucfirst($cat)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><input type="date" class="form-control" name="date_from" placeholder="From" value="<?= esc($filters['date_from'] ?? '') ?>"></div>
        <div class="col-md-2"><input type="date" class="form-control" name="date_to" placeholder="To" value="<?= esc($filters['date_to'] ?? '') ?>"></div>
        <div class="col-md-2"><button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Date</th><th>Title</th><th>Category</th><th>Method</th><th>Reference</th><th class="text-end">Amount</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                <?php foreach (($expenses ?? []) as $e): ?>
                    <tr>
                        <td><?= esc($e['expense_date']) ?></td>
                        <td class="fw-semibold"><?= esc($e['expense_title']) ?></td>
                        <td><span class="badge bg-light text-dark"><?= esc(ucfirst($e['category'] ?? 'office')) ?></span></td>
                        <td class="small text-muted"><?= esc(str_replace('_',' ', $e['payment_method'] ?? 'cash')) ?></td>
                        <td class="small text-muted"><?= esc($e['reference_no'] ?? '-') ?></td>
                        <td class="text-end fw-semibold text-danger">৳ <?= number_format((float) $e['amount'], 2) ?></td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/expenses/' . $e['un_id']) ?>" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/expenses/' . $e['un_id'] . '/edit') ?>" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= site_url('admin/expenses/' . $e['un_id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this expense?');">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($expenses)): ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-cash-coin" style="font-size:2.5rem;color:#cbcae3;"></i>
                        <p class="mt-2 mb-0">No expenses recorded yet.</p>
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
