<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Sales & Invoices</h4>
        <p class="text-muted small m-0">
            <?= number_format($totals['count']) ?> sales &middot;
            Total <span class="fw-semibold">৳ <?= number_format($totals['total'], 0) ?></span> &middot;
            Due <span class="text-danger fw-semibold">৳ <?= number_format($totals['due'], 0) ?></span>
        </p>
    </div>
    <a href="<?= site_url('admin/sales/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>New Sale
    </a>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-3"><input type="text" class="form-control" name="q" placeholder="Search invoice #..." value="<?= esc($filters['q'] ?? '') ?>"></div>
        <div class="col-md-3">
            <select name="customer_un_id" class="form-select">
                <option value="">All customers</option>
                <?php foreach ($customers ?? [] as $cu): ?>
                    <option value="<?= esc($cu['un_id']) ?>" <?= ($filters['customer_un_id'] ?? '') === $cu['un_id'] ? 'selected' : '' ?>><?= esc($cu['customer_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="sale_type" class="form-select">
                <option value="">All types</option>
                <option value="cash"   <?= ($filters['sale_type'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash</option>
                <option value="credit" <?= ($filters['sale_type'] ?? '') === 'credit' ? 'selected' : '' ?>>Credit</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="payment_status" class="form-select">
                <option value="">All status</option>
                <option value="paid"    <?= ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                <option value="partial" <?= ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' ?>>Partial</option>
                <option value="due"     <?= ($filters['payment_status'] ?? '') === 'due' ? 'selected' : '' ?>>Due</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Invoice</th><th>Date</th><th>Customer</th><th>Type</th><th class="text-end">Total</th><th class="text-end">Paid</th><th class="text-end">Due</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                <?php foreach (($sales ?? []) as $s): ?>
                    <tr>
                        <td><a href="<?= site_url('admin/sales/' . $s['un_id']) ?>" class="fw-semibold text-decoration-none"><?= esc($s['invoice_no']) ?></a></td>
                        <td><?= esc($s['sale_date']) ?></td>
                        <td><?= esc(short_un_id($s['customer_un_id'] ?? '')) ?></td>
                        <td><span class="badge bg-light text-dark"><?= esc(ucfirst($s['sale_type'])) ?></span></td>
                        <td class="text-end fw-semibold">৳ <?= number_format((float) $s['total_amount'], 0) ?></td>
                        <td class="text-end text-success">৳ <?= number_format((float) $s['paid_amount'], 0) ?></td>
                        <td class="text-end <?= (float) $s['due_amount'] > 0 ? 'text-danger fw-semibold' : '' ?>">৳ <?= number_format((float) $s['due_amount'], 0) ?></td>
                        <td>
                            <?php $st = $s['payment_status']; ?>
                            <span class="badge badge-status <?= $st === 'paid' ? 'bg-success' : ($st === 'partial' ? 'bg-warning' : 'bg-danger') ?>"><?= esc(ucfirst($st)) ?></span>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/sales/' . $s['un_id']) ?>" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/sales/' . $s['un_id'] . '/invoice') ?>" target="_blank" class="btn btn-sm btn-light"><i class="bi bi-printer"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($sales)): ?>
                    <tr><td colspan="9" class="text-center py-5 text-muted">
                        <i class="bi bi-cart" style="font-size:2.5rem;color:#cbcae3;"></i>
                        <p class="mt-2 mb-0">No sales yet.</p>
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
