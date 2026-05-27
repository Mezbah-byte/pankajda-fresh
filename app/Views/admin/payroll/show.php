<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Payroll Slip</h4>
        <ul class="mz-breadcrumb"><li>HR</li><li><a href="<?= site_url('admin/payroll') ?>">Payroll</a></li><li><?= esc($record['pay_period']) ?></li></ul>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-light"><i class="bi bi-printer me-1"></i>Print</button>
        <?php if (($record['status']??'draft') !== 'paid'): ?>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#markPaidModal">
                <i class="bi bi-check-circle me-1"></i>Mark Paid
            </button>
        <?php endif; ?>
        <form method="post" action="<?= site_url('admin/payroll/' . $record['un_id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this record?');">
            <?= csrf_field() ?>
            <button class="btn btn-light text-danger"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
    </div>
</div>

<?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>

<div class="pd-card" id="payslipContent">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 pb-3" style="border-bottom:2px solid var(--mz-primary);">
        <div>
            <?php if (!empty($company)): ?>
                <h5 class="mb-1 fw-bold"><?= esc($company['company_name']) ?></h5>
                <div class="text-muted" style="font-size:.82rem;"><?= esc($company['address']??'') ?></div>
            <?php else: ?>
                <h5 class="mb-1 fw-bold">Payroll Slip</h5>
            <?php endif; ?>
        </div>
        <div class="text-end">
            <div class="fw-bold" style="font-size:1.1rem;color:var(--mz-primary);">Period: <?= esc($record['pay_period']) ?></div>
            <div class="text-muted" style="font-size:.82rem;">Generated: <?= date('d M Y', strtotime($record['created_at'])) ?></div>
            <?php
            $statusColors = ['draft'=>'secondary','approved'=>'primary','paid'=>'success'];
            $sc = $statusColors[$record['status']??'draft'] ?? 'secondary';
            ?>
            <span class="badge bg-<?= $sc ?>-subtle text-<?= $sc ?> mt-1"><?= ucfirst($record['status']??'draft') ?></span>
        </div>
    </div>

    <!-- Employee Info -->
    <?php if ($employee): ?>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <h6 class="text-uppercase text-muted mb-2" style="font-size:.72rem;letter-spacing:.6px;">Employee</h6>
            <table class="table table-borderless table-sm mb-0">
                <tr><td class="text-muted" style="width:40%">Name</td><td class="fw-semibold"><?= esc($employee['full_name']) ?></td></tr>
                <tr><td class="text-muted">Designation</td><td><?= esc($employee['designation']??'-') ?></td></tr>
                <tr><td class="text-muted">Department</td><td><?= esc($employee['department']??'-') ?></td></tr>
                <tr><td class="text-muted">Join Date</td><td><?= esc($employee['join_date']??'-') ?></td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <?php if ($record['paid_at']??null): ?>
            <h6 class="text-uppercase text-muted mb-2" style="font-size:.72rem;letter-spacing:.6px;">Payment</h6>
            <table class="table table-borderless table-sm mb-0">
                <tr><td class="text-muted" style="width:40%">Paid On</td><td class="fw-semibold"><?= esc($record['paid_at']) ?></td></tr>
                <tr><td class="text-muted">Method</td><td><?= esc($record['payment_method']??'-') ?></td></tr>
                <tr><td class="text-muted">Reference</td><td><?= esc($record['payment_reference']??'-') ?></td></tr>
            </table>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Earnings & Deductions -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-primary);">Earnings</h6>
            <table class="table table-sm">
                <tr><td>Basic Salary</td><td class="text-end fw-semibold">৳ <?= number_format((float)($record['basic_salary']??0),2) ?></td></tr>
                <?php if ((float)($record['house_allowance']??0)>0): ?><tr><td class="text-muted">House Allowance</td><td class="text-end">৳ <?= number_format((float)$record['house_allowance'],2) ?></td></tr><?php endif; ?>
                <?php if ((float)($record['transport_allowance']??0)>0): ?><tr><td class="text-muted">Transport</td><td class="text-end">৳ <?= number_format((float)$record['transport_allowance'],2) ?></td></tr><?php endif; ?>
                <?php if ((float)($record['medical_allowance']??0)>0): ?><tr><td class="text-muted">Medical</td><td class="text-end">৳ <?= number_format((float)$record['medical_allowance'],2) ?></td></tr><?php endif; ?>
                <?php if ((float)($record['other_allowances']??0)>0): ?><tr><td class="text-muted">Other Allowances</td><td class="text-end">৳ <?= number_format((float)$record['other_allowances'],2) ?></td></tr><?php endif; ?>
                <?php if ((float)($record['overtime_pay']??0)>0): ?><tr><td class="text-muted">Overtime</td><td class="text-end">৳ <?= number_format((float)$record['overtime_pay'],2) ?></td></tr><?php endif; ?>
                <tr style="border-top:2px solid var(--mz-border);"><td class="fw-bold">Gross Pay</td><td class="text-end fw-bold text-success">৳ <?= number_format((float)($record['gross_pay']??0),2) ?></td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6 class="fw-semibold mb-3 text-danger">Deductions</h6>
            <table class="table table-sm">
                <?php if ((float)($record['advance_deduction']??0)>0): ?><tr><td class="text-muted">Advance</td><td class="text-end">৳ <?= number_format((float)$record['advance_deduction'],2) ?></td></tr><?php endif; ?>
                <?php if ((float)($record['tax_deduction']??0)>0): ?><tr><td class="text-muted">Tax</td><td class="text-end">৳ <?= number_format((float)$record['tax_deduction'],2) ?></td></tr><?php endif; ?>
                <?php if ((float)($record['other_deductions']??0)>0): ?><tr><td class="text-muted">Other</td><td class="text-end">৳ <?= number_format((float)$record['other_deductions'],2) ?></td></tr><?php endif; ?>
                <tr style="border-top:2px solid var(--mz-border);"><td class="fw-bold">Total Deductions</td><td class="text-end fw-bold text-danger">৳ <?= number_format((float)($record['total_deductions']??0),2) ?></td></tr>
            </table>
            <div class="mt-3 p-3 rounded" style="background:linear-gradient(135deg,var(--mz-primary),#7c9dff);color:#fff;">
                <div style="font-size:.78rem;opacity:.8;">NET PAY</div>
                <div style="font-size:1.8rem;font-weight:700;">৳ <?= number_format((float)($record['net_pay']??0),2) ?></div>
            </div>
        </div>
    </div>

    <?php if (!empty($record['notes'])): ?>
        <div class="text-muted" style="font-size:.82rem;border-top:1px solid var(--mz-border);padding-top:12px;margin-top:4px;">Notes: <?= esc($record['notes']) ?></div>
    <?php endif; ?>
</div>

<!-- Mark Paid Modal -->
<?php if (($record['status']??'draft') !== 'paid'): ?>
<div class="modal fade" id="markPaidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Mark as Paid</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="post" action="<?= site_url('admin/payroll/' . $record['un_id'] . '/paid') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" name="payment_method">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                            <option value="mobile_banking">Mobile Banking</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Reference</label>
                        <input type="text" class="form-control" name="payment_reference" placeholder="Transaction ID / Reference">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Paid Date</label>
                        <input type="date" class="form-control" name="paid_at" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Confirm Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
