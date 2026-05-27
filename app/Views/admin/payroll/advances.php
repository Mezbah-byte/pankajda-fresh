<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Employee Advances</h4>
        <ul class="mz-breadcrumb"><li>HR</li><li><a href="<?= site_url('admin/payroll') ?>">Payroll</a></li><li>Advances</li></ul>
    </div>
</div>

<?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>

<div class="row g-3">
    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-semibold mb-3">Add Advance</h6>
            <?php if (session('errors')): ?><div class="alert alert-danger py-2"><ul class="mb-0 small"><?php foreach (session('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
            <form method="post" action="<?= site_url('admin/payroll/advances') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Employee <span class="text-danger">*</span></label>
                    <select class="form-select" name="employee_un_id" required>
                        <option value="">Select Employee</option>
                        <?php foreach (($employees ?? []) as $emp): ?>
                            <option value="<?= esc($emp['un_id']) ?>"><?= esc($emp['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount (৳) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" class="form-control" name="amount" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="advance_date" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" rows="2"></textarea>
                </div>
                <button class="btn btn-primary w-100"><i class="bi bi-plus-circle me-1"></i>Record Advance</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="pd-card">
            <form method="get" class="row g-2 mb-4">
                <div class="col-md-7">
                    <select name="employee_un_id" class="form-select">
                        <option value="">All Employees</option>
                        <?php foreach (($employees ?? []) as $emp): ?>
                            <option value="<?= esc($emp['un_id']) ?>" <?= ($employee_un_id??'')===$emp['un_id']?'selected':'' ?>><?= esc($emp['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5"><button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Employee</th><th>Date</th><th class="text-end">Amount</th><th>Status</th><th>Notes</th></tr></thead>
                    <tbody>
                        <?php foreach (($advances ?? []) as $adv): ?>
                            <tr>
                                <td class="fw-semibold"><?= esc($adv['employee_name'] ?? $adv['employee_un_id']) ?></td>
                                <td><?= esc($adv['advance_date']) ?></td>
                                <td class="text-end fw-semibold text-warning">৳ <?= number_format((float)$adv['amount'],2) ?></td>
                                <td>
                                    <span class="badge bg-<?= ($adv['status']??'pending')==='deducted'?'success':'warning' ?>-subtle text-<?= ($adv['status']??'pending')==='deducted'?'success':'warning' ?>">
                                        <?= ucfirst($adv['status']??'pending') ?>
                                    </span>
                                </td>
                                <td class="text-muted"><?= esc($adv['notes']??'-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($advances)): ?><tr><td colspan="5" class="text-center text-muted py-4">No advance records.</td></tr><?php endif; ?>
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
    </div>
</div>

<?= $this->endSection() ?>
