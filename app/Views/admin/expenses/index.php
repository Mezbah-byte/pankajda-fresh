<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Expenses</h4>
        <ul class="mz-breadcrumb">
            <li>Operations</li>
            <li>Expenses</li>
        </ul>
    </div>
    <a href="<?= site_url('admin/expenses/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add Expense</a>
</div>

<!-- Summary bar -->
<div class="pd-card py-3 px-4 mb-3" style="background:linear-gradient(135deg,#FDECEA,#fff5f3);">
    <div class="d-flex flex-wrap gap-4">
        <div>
            <span class="text-muted" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">Records</span>
            <div class="fw-bold" style="font-size:1.1rem;color:var(--mz-text-primary);"><?= number_format($totals['count']) ?></div>
        </div>
        <div>
            <span class="text-muted" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">All Time</span>
            <div class="fw-bold" style="font-size:1.1rem;color:var(--mz-text-primary);">৳ <?= number_format($totals['total'], 0) ?></div>
        </div>
        <div>
            <span class="text-muted" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">This Month</span>
            <div class="fw-bold" style="font-size:1.1rem;color:#FA896B;">৳ <?= number_format($totals['month'], 0) ?></div>
        </div>
    </div>
</div>

<!-- Category mini stat cards -->
<?php if (! empty($byCategory)): ?>
<div class="row g-3 mb-3">
    <?php
    $catGrads = ['gradient-1','gradient-3','gradient-4','gradient-2'];
    foreach (array_slice($byCategory ?? [], 0, 4) as $i => $cat):
    ?>
        <div class="col-md-3">
            <div class="pd-stat <?= $catGrads[$i % 4] ?>">
                <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                <div class="stat-label"><?= esc(ucfirst($cat['category'])) ?></div>
                <div class="stat-value" style="font-size:1.3rem;">৳ <?= number_format((float) $cat['total'], 0) ?></div>
                <div class="text-muted" style="font-size:.75rem;margin-top:4px;"><?= number_format((int) $cat['count']) ?> entries</div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="text" class="form-control" name="q" placeholder="Search title…" value="<?= esc($filters['q'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <select name="category" class="form-select">
                <option value="">All categories</option>
                <?php foreach (($categories ?? []) as $cat): ?>
                    <option value="<?= esc($cat) ?>" <?= ($filters['category'] ?? '') === $cat ? 'selected' : '' ?>><?= esc(ucfirst($cat)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><input type="date" class="form-control" name="date_from" value="<?= esc($filters['date_from'] ?? '') ?>"></div>
        <div class="col-md-2"><input type="date" class="form-control" name="date_to" value="<?= esc($filters['date_to'] ?? '') ?>"></div>
        <div class="col-md-3">
            <button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr><th>Date</th><th>Title</th><th>Category</th><th>Method</th><th>Reference</th><th class="text-end">Amount</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach (($expenses ?? []) as $e): ?>
                    <tr>
                        <td><?= esc($e['expense_date']) ?></td>
                        <td class="fw-semibold"><?= esc($e['expense_title']) ?></td>
                        <td><span class="badge-secondary-soft"><?= esc(ucfirst($e['category'] ?? 'office')) ?></span></td>
                        <td class="text-muted" style="font-size:.82rem;"><?= esc(str_replace('_',' ', $e['payment_method'] ?? 'cash')) ?></td>
                        <td class="text-muted" style="font-size:.82rem;"><?= esc($e['reference_no'] ?? '-') ?></td>
                        <td class="text-end fw-semibold" style="color:#FA896B;">৳ <?= number_format((float) $e['amount'], 2) ?></td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/expenses/' . $e['un_id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/expenses/' . $e['un_id'] . '/edit') ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= site_url('admin/expenses/' . $e['un_id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this expense?');">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($expenses)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-cash-coin" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
                            <span class="text-muted">No expenses recorded yet.</span>
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
