<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Vendors / Suppliers</h4>
        <ul class="mz-breadcrumb"><li>Accounts</li><li>Vendors</li></ul>
    </div>
    <a href="<?= site_url('admin/vendors/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>New Vendor</a>
</div>

<!-- Summary -->
<?php if (! empty($totals)): ?>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="pd-stat gradient-1">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">Total Vendors</div>
            <div class="stat-value"><?= number_format($totals['count'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pd-stat gradient-3">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-label">Total Payable</div>
            <div class="stat-value">৳ <?= number_format($totals['total_payable'] ?? 0, 0) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pd-stat gradient-4">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-label">Total Paid</div>
            <div class="stat-value">৳ <?= number_format($totals['total_paid'] ?? 0, 0) ?></div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-5"><input type="text" class="form-control" name="q" placeholder="Search vendor…" value="<?= esc($filters['q'] ?? '') ?>"></div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <div class="col-md-4"><button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Vendor</th><th>Contact</th><th>Phone</th>
                    <th class="text-end">Payable</th><th class="text-end">Paid</th><th class="text-end">Balance</th>
                    <th>Status</th><th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($vendors ?? []) as $v): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= esc($v['vendor_name']) ?></div>
                            <div class="text-muted" style="font-size:.78rem;"><?= esc($v['email'] ?? '') ?></div>
                        </td>
                        <td><?= esc($v['contact_person'] ?? '-') ?></td>
                        <td><?= esc($v['phone'] ?? '-') ?></td>
                        <td class="text-end">৳ <?= number_format((float) ($v['total_payable'] ?? 0), 2) ?></td>
                        <td class="text-end text-success">৳ <?= number_format((float) ($v['total_paid'] ?? 0), 2) ?></td>
                        <td class="text-end <?= ((float)($v['total_payable']??0)-(float)($v['total_paid']??0)) > 0 ? 'text-danger' : 'text-success' ?>">
                            ৳ <?= number_format(((float)($v['total_payable']??0))-((float)($v['total_paid']??0)), 2) ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= ($v['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>-subtle text-<?= ($v['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($v['status'] ?? 'active') ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/vendors/' . $v['un_id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/vendors/' . $v['un_id'] . '/edit') ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= site_url('admin/vendors/' . $v['un_id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete vendor?');">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($vendors)): ?>
                    <tr><td colspan="8" class="text-center py-5"><i class="bi bi-people" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i><span class="text-muted">No vendors yet.</span></td></tr>
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
