<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Payroll</h4>
        <ul class="mz-breadcrumb"><li>HR</li><li>Payroll</li></ul>
    </div>
    <a href="<?= site_url('admin/payroll/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Generate Payroll</a>
</div>

<?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>

<!-- Summary -->
<?php if (! empty($summary)): ?>
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="pd-stat gradient-1"><div class="stat-label">Total Gross</div><div class="stat-value">৳ <?= number_format((float)($summary['total_gross']??0), 0) ?></div></div></div>
    <div class="col-md-3"><div class="pd-stat gradient-3"><div class="stat-label">Total Deductions</div><div class="stat-value">৳ <?= number_format((float)($summary['total_deductions']??0), 0) ?></div></div></div>
    <div class="col-md-3"><div class="pd-stat gradient-2"><div class="stat-label">Total Net</div><div class="stat-value">৳ <?= number_format((float)($summary['total_net']??0), 0) ?></div></div></div>
    <div class="col-md-3"><div class="pd-stat gradient-4"><div class="stat-label">Records</div><div class="stat-value"><?= number_format((int)($summary['count']??0)) ?></div></div></div>
</div>
<?php endif; ?>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="month" class="form-control" name="period" value="<?= esc($period ?? date('Y-m')) ?>">
        </div>
        <div class="col-md-3">
            <select name="employee_un_id" class="form-select">
                <option value="">All Employees</option>
                <?php foreach (($employees ?? []) as $emp): ?>
                    <option value="<?= esc($emp['un_id']) ?>" <?= ($filters['employee_un_id']??'')===$emp['un_id']?'selected':'' ?>><?= esc($emp['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="draft" <?= ($filters['status']??'')==='draft'?'selected':'' ?>>Draft</option>
                <option value="approved" <?= ($filters['status']??'')==='approved'?'selected':'' ?>>Approved</option>
                <option value="paid" <?= ($filters['status']??'')==='paid'?'selected':'' ?>>Paid</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
        <div class="col-md-2"><a href="<?= site_url('admin/payroll/advances') ?>" class="btn btn-outline-secondary w-100"><i class="bi bi-cash-coin me-1"></i>Advances</a></div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr><th>Employee</th><th>Period</th><th class="text-end">Basic</th><th class="text-end">Allowances</th><th class="text-end">Deductions</th><th class="text-end">Net Pay</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach (($records ?? []) as $rec): ?>
                    <?php
                    $statusColors = ['draft'=>'secondary','approved'=>'primary','paid'=>'success'];
                    $sc = $statusColors[$rec['status']??'draft'] ?? 'secondary';
                    ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($rec['employee_name'] ?? $rec['employee_un_id']) ?></td>
                        <td><?= esc($rec['pay_period']) ?></td>
                        <td class="text-end">৳ <?= number_format((float)($rec['basic_salary']??0), 2) ?></td>
                        <td class="text-end text-success">৳ <?= number_format((float)($rec['total_allowances']??0), 2) ?></td>
                        <td class="text-end text-danger">৳ <?= number_format((float)($rec['total_deductions']??0), 2) ?></td>
                        <td class="text-end fw-bold" style="color:var(--mz-primary);">৳ <?= number_format((float)($rec['net_pay']??0), 2) ?></td>
                        <td><span class="badge bg-<?= $sc ?>-subtle text-<?= $sc ?>"><?= ucfirst($rec['status']??'draft') ?></span></td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/payroll/' . $rec['un_id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                            <form method="post" action="<?= site_url('admin/payroll/' . $rec['un_id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete payroll record?');">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($records)): ?>
                    <tr><td colspan="8" class="text-center py-5"><i class="bi bi-wallet2" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i><span class="text-muted">No payroll records for this period.</span></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (! empty($pagination) && $pagination['last_page'] > 1): ?>
        <nav class="d-flex justify-content-end mt-3">
            <ul class="pagination pagination-sm m-0">
                <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                    <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $p ?>&period=<?= esc($period) ?>"><?= $p ?></a></li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
