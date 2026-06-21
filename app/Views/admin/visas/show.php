<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
// Expiry helpers
function expiryBadge(?string $date): string {
    if (!$date) return '<span class="badge bg-secondary">No Date</span>';
    $days = (int) ceil((strtotime($date) - time()) / 86400);
    if ($days < 0)   return '<span class="badge bg-danger">Expired ' . abs($days) . 'd ago</span>';
    if ($days <= 30) return '<span class="badge bg-danger">' . $days . ' days left</span>';
    if ($days <= 90) return '<span class="badge bg-warning text-dark">' . $days . ' days left</span>';
    return '<span class="badge bg-success">' . $days . ' days left</span>';
}
function expiryProgress(?string $issueDate, ?string $expiryDate): int {
    if (!$issueDate || !$expiryDate) return 0;
    $total = strtotime($expiryDate) - strtotime($issueDate);
    $elapsed = time() - strtotime($issueDate);
    if ($total <= 0) return 100;
    return min(100, max(0, (int) ($elapsed / $total * 100)));
}
$profit = (float)($visa['profit'] ?? 0);
$purchasePrice = (float)($visa['purchase_price'] ?? 0);
$sellingPrice  = (float)($visa['selling_price']  ?? 0);
$extraCostsTotal = (float)($visa['extra_costs'] ?? 0);
?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="d-flex align-items-center gap-2">
            <i class="bi bi-passport text-primary"></i>
            <?= esc($visa['visa_name']) ?>
        </h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/visas') ?>" class="text-muted text-decoration-none">Visas</a></li>
            <li><?= esc($visa['visa_name']) ?></li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/visas/' . $visa['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/visas') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<?php if ($flash = session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3"><i class="bi bi-check-circle me-2"></i><?= esc($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if ($flash = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3"><i class="bi bi-exclamation-triangle me-2"></i><?= esc($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<!-- Expiry Banner Row -->
<div class="row g-3 mb-3">
    <!-- Visa Expiry -->
    <div class="col-md-6">
        <?php
        $vDays = $visa['visa_expiry_date'] ? (int) ceil((strtotime($visa['visa_expiry_date']) - time()) / 86400) : null;
        $vProg = expiryProgress($visa['visa_issue_date'] ?? null, $visa['visa_expiry_date'] ?? null);
        $vColor = ($vDays === null) ? 'secondary' : ($vDays < 0 ? 'danger' : ($vDays <= 30 ? 'danger' : ($vDays <= 90 ? 'warning' : 'success')));
        ?>
        <div class="pd-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-2 p-2" style="background:rgba(13,110,253,.1);"><i class="bi bi-passport text-primary fs-5"></i></div>
                    <div>
                        <small class="text-muted d-block">Visa Expiry</small>
                        <strong><?= $visa['visa_expiry_date'] ? date('d M Y', strtotime($visa['visa_expiry_date'])) : 'Not set' ?></strong>
                    </div>
                </div>
                <?= expiryBadge($visa['visa_expiry_date'] ?? null) ?>
            </div>
            <?php if ($visa['visa_expiry_date']): ?>
            <div class="progress mt-2" style="height:6px;">
                <div class="progress-bar bg-<?= $vColor ?>" style="width:<?= $vProg ?>%"></div>
            </div>
            <div class="d-flex justify-content-between mt-1">
                <small class="text-muted"><?= $visa['visa_issue_date'] ? date('d M Y', strtotime($visa['visa_issue_date'])) : '' ?></small>
                <small class="text-muted"><?= date('d M Y', strtotime($visa['visa_expiry_date'])) ?></small>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Work Permit Expiry -->
    <div class="col-md-6">
        <?php
        $wpDays = isset($visa['work_permit_expiry_date']) && $visa['work_permit_expiry_date']
            ? (int) ceil((strtotime($visa['work_permit_expiry_date']) - time()) / 86400)
            : null;
        $wpProg  = expiryProgress($visa['work_permit_issue_date'] ?? null, $visa['work_permit_expiry_date'] ?? null);
        $wpColor = ($wpDays === null) ? 'secondary' : ($wpDays < 0 ? 'danger' : ($wpDays <= 30 ? 'danger' : ($wpDays <= 90 ? 'warning' : 'success')));
        ?>
        <div class="pd-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-2 p-2" style="background:rgba(255,193,7,.12);"><i class="bi bi-award text-warning fs-5"></i></div>
                    <div>
                        <small class="text-muted d-block">Work Permit Expiry</small>
                        <strong><?= !empty($visa['work_permit_expiry_date']) ? date('d M Y', strtotime($visa['work_permit_expiry_date'])) : 'Not set' ?></strong>
                    </div>
                </div>
                <?= expiryBadge($visa['work_permit_expiry_date'] ?? null) ?>
            </div>
            <?php if (!empty($visa['work_permit_expiry_date'])): ?>
            <div class="progress mt-2" style="height:6px;">
                <div class="progress-bar bg-<?= $wpColor ?>" style="width:<?= $wpProg ?>%"></div>
            </div>
            <div class="d-flex justify-content-between mt-1">
                <small class="text-muted"><?= !empty($visa['work_permit_issue_date']) ? date('d M Y', strtotime($visa['work_permit_issue_date'])) : '' ?></small>
                <small class="text-muted"><?= date('d M Y', strtotime($visa['work_permit_expiry_date'])) ?></small>
            </div>
            <?php else: ?>
            <div class="text-muted mt-2"><small>Work permit number: <?= esc($visa['work_permit_number'] ?? 'Not set') ?></small></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Left column: main info + payments + extra costs -->
    <div class="col-md-8">

        <!-- Visa Details Card -->
        <div class="pd-card mb-3">
            <h6 class="fw-semibold mb-3 text-uppercase" style="color:var(--mz-text-muted);font-size:.72rem;letter-spacing:.5px;">Visa Details</h6>
            <div class="row g-3">
                <div class="col-sm-6">
                    <small class="text-muted d-block">Beneficiary</small>
                    <strong><?= esc($visa['beneficiary_name'] ?? '-') ?></strong>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Passport No</small>
                    <strong><?= esc($visa['passport_no'] ?? '-') ?></strong>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Visa Number</small>
                    <strong><?= esc($visa['visa_number'] ?? '-') ?></strong>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Work Permit No</small>
                    <strong><?= esc($visa['work_permit_number'] ?? '-') ?></strong>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted d-block">Destination</small>
                    <strong><?= esc($visa['country'] ?? '-') ?></strong>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted d-block">From Country</small>
                    <strong><?= esc($visa['from_country'] ?? '-') ?></strong>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted d-block">Category</small>
                    <strong><?= esc($visa['category'] ?? '-') ?></strong>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Company</small>
                    <strong><?= esc($company['company_name'] ?? '-') ?></strong>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Status</small>
                    <?php $st = $visa['status'] ?? 'active'; ?>
                    <span class="badge <?= $st === 'active' ? 'bg-success' : ($st === 'expired' ? 'bg-danger' : 'bg-secondary') ?>">
                        <?= ucfirst($st) ?>
                    </span>
                </div>
                <?php if ($visa['notes']): ?>
                <div class="col-12">
                    <small class="text-muted d-block">Notes</small>
                    <p class="mb-0"><?= nl2br(esc($visa['notes'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment History -->
        <div class="pd-card mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-cash-coin me-2 text-success"></i>Payment History</h6>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#addPayment">
                    <i class="bi bi-plus-circle me-1"></i>Add Payment
                </button>
            </div>

            <div class="collapse" id="addPayment">
                <form method="post" action="<?= site_url('admin/visas/' . $visa['un_id'] . '/payments') ?>" class="p-3 mb-3 rounded" style="background:var(--mz-bg-soft,#f8f9fa);">
                    <?= csrf_field() ?>
                    <div class="row g-2">
                        <div class="col-md-3"><input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" required></div>
                        <div class="col-md-3">
                            <select name="payment_method" class="form-select">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="mfs">Mobile Banking</option>
                            </select>
                        </div>
                        <div class="col-md-3"><input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
                        <div class="col-md-3"><input type="text" name="reference_no" class="form-control" placeholder="Reference"></div>
                        <div class="col-12"><input type="text" name="notes" class="form-control" placeholder="Notes (optional)"></div>
                        <div class="col-12"><button class="btn btn-primary btn-sm"><i class="bi bi-check2 me-1"></i>Record Payment</button></div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead><tr><th>Date</th><th>Method</th><th>Reference</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                        <?php foreach (($payments ?? []) as $p): ?>
                            <tr>
                                <td><?= esc($p['payment_date']) ?></td>
                                <td><span class="badge-secondary-soft"><?= esc($p['payment_method']) ?></span></td>
                                <td><?= esc($p['reference_no'] ?? '-') ?></td>
                                <td class="text-end fw-semibold text-success">৳ <?= number_format((float) $p['amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($payments)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">No payments yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Extra Costs -->
        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-receipt me-2 text-warning"></i>Extra Costs</h6>
                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="collapse" data-bs-target="#addExtraCost">
                    <i class="bi bi-plus-circle me-1"></i>Add Cost
                </button>
            </div>

            <div class="collapse" id="addExtraCost">
                <form method="post" action="<?= site_url('admin/visas/' . $visa['un_id'] . '/extra-costs') ?>" class="p-3 mb-3 rounded" style="background:rgba(255,193,7,.07);">
                    <?= csrf_field() ?>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Description</label>
                            <input type="text" name="description" class="form-control" placeholder="e.g. Medical test, courier…" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Amount (৳)</label>
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-warning btn-sm w-100"><i class="bi bi-plus me-1"></i>Add</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead><tr><th>#</th><th>Description</th><th class="text-end">Amount</th><th></th></tr></thead>
                    <tbody>
                        <?php $i = 1; foreach (($extra_costs ?? []) as $ec): ?>
                            <tr>
                                <td class="text-muted"><?= $i++ ?></td>
                                <td><?= esc($ec['description']) ?></td>
                                <td class="text-end fw-semibold text-warning">৳ <?= number_format((float) $ec['amount'], 2) ?></td>
                                <td class="text-end">
                                    <form method="post" action="<?= site_url('admin/visas/' . $visa['un_id'] . '/extra-costs/' . $ec['un_id'] . '/delete') ?>" onsubmit="return confirm('Remove this cost?')">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($extra_costs)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">No extra costs added.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($extra_costs)): ?>
                    <tfoot>
                        <tr class="table-warning">
                            <td colspan="2" class="fw-bold">Total Extra Costs</td>
                            <td class="text-end fw-bold">৳ <?= number_format($extraCostsTotal, 2) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-md-4">

        <!-- P&L Summary -->
        <div class="pd-card mb-3">
            <h6 class="fw-semibold mb-3 text-uppercase" style="color:var(--mz-text-muted);font-size:.72rem;letter-spacing:.5px;">Financial Summary</h6>

            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted"><i class="bi bi-arrow-up-circle me-1 text-danger"></i>Visa Cost</span>
                <span class="fw-semibold">৳ <?= number_format($purchasePrice, 2) ?></span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted"><i class="bi bi-arrow-down-circle me-1 text-success"></i>Selling Price</span>
                <span class="fw-semibold text-success">৳ <?= number_format($sellingPrice, 2) ?></span>
            </div>
            <?php if ($extraCostsTotal > 0): ?>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted"><i class="bi bi-receipt me-1 text-warning"></i>Extra Costs</span>
                <span class="fw-semibold text-warning">− ৳ <?= number_format($extraCostsTotal, 2) ?></span>
            </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between py-3 mt-1 rounded px-2" style="background:<?= $profit >= 0 ? 'rgba(25,135,84,.1)' : 'rgba(220,53,69,.1)' ?>;">
                <span class="fw-bold">Profit</span>
                <span class="fw-bold fs-5" style="color:<?= $profit >= 0 ? '#198754' : '#dc3545' ?>;">
                    <?= $profit < 0 ? '− ' : '' ?>৳ <?= number_format(abs($profit), 2) ?>
                </span>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="pd-card mb-3">
            <h6 class="fw-semibold mb-3 text-uppercase" style="color:var(--mz-text-muted);font-size:.72rem;letter-spacing:.5px;">Client Payment</h6>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Customer Payment</span>
                <span class="fw-semibold">৳ <?= number_format((float) $visa['visa_cost'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Paid</span>
                <span class="fw-semibold text-success">৳ <?= number_format((float) $visa['paid_amount'], 2) ?></span>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between mb-3">
                <span class="fw-bold">Due</span>
                <span class="fw-bold fs-5" style="color:#FA896B;">৳ <?= number_format((float) $visa['due_amount'], 2) ?></span>
            </div>
            <?php $pst = $visa['payment_status']; ?>
            <div class="text-center">
                <?php if ($pst === 'paid'): ?>
                    <span class="badge-success-soft px-4 py-2">Fully Paid</span>
                <?php elseif ($pst === 'partial'): ?>
                    <span class="badge-warning-soft px-4 py-2">Partially Paid</span>
                <?php else: ?>
                    <span class="badge-danger-soft px-4 py-2">Payment Due</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pipeline -->
        <div class="pd-card mb-3">
            <h6 class="fw-semibold mb-3"><i class="bi bi-diagram-3 me-2"></i>Pipeline Timeline</h6>
            <?= $this->include('admin/visas/_timeline') ?>
        </div>
        <?= $this->include('admin/visas/_stage_form') ?>

        <!-- Quick Info -->
        <div class="pd-card mt-3">
            <h6 class="fw-semibold mb-3 text-uppercase" style="color:var(--mz-text-muted);font-size:.72rem;letter-spacing:.5px;">Quick Info</h6>
            <div class="row g-2 text-center">
                <div class="col-6">
                    <div class="rounded p-2" style="background:var(--mz-bg-soft,#f8f9fa);">
                        <small class="text-muted d-block">Issue Date</small>
                        <strong class="small"><?= $visa['visa_issue_date'] ? date('d M Y', strtotime($visa['visa_issue_date'])) : '-' ?></strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="rounded p-2" style="background:var(--mz-bg-soft,#f8f9fa);">
                        <small class="text-muted d-block">Expiry Date</small>
                        <strong class="small"><?= $visa['visa_expiry_date'] ? date('d M Y', strtotime($visa['visa_expiry_date'])) : '-' ?></strong>
                    </div>
                </div>
                <?php if (!empty($visa['work_permit_number'])): ?>
                <div class="col-12">
                    <div class="rounded p-2" style="background:rgba(255,193,7,.08);">
                        <small class="text-muted d-block">Work Permit No</small>
                        <strong class="small"><?= esc($visa['work_permit_number']) ?></strong>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
