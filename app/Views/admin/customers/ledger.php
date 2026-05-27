<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$customer        = $ledger['customer'];
$transactions    = $ledger['transactions'];
$openingBalance  = $ledger['opening_balance'];
$totalDebit      = $ledger['total_debit'];
$totalCredit     = $ledger['total_credit'];
$closingBalance  = $ledger['closing_balance'];
$customerUnId    = $customer['un_id'];
?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Account Ledger — <?= esc($customer['customer_name']) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/customers') ?>" class="text-muted text-decoration-none">Customers</a></li>
            <li><a href="<?= site_url('admin/customers/' . $customerUnId) ?>" class="text-muted text-decoration-none"><?= esc($customer['customer_name']) ?></a></li>
            <li>Ledger</li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/customers/' . $customerUnId . '/ledger/print?date_from=' . urlencode($from) . '&date_to=' . urlencode($to)) ?>"
           target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-printer me-2"></i>Print Statement
        </a>
        <a href="<?= site_url('admin/customers/' . $customerUnId) ?>" class="btn btn-light">
            <i class="bi bi-arrow-left me-2"></i>Back to Customer
        </a>
    </div>
</div>

<!-- Date Range Filter -->
<div class="pd-card mb-3">
    <form method="get" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;color:var(--mz-text-muted);">From Date</label>
            <input type="date" name="date_from" class="form-control" value="<?= esc($from) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;color:var(--mz-text-muted);">To Date</label>
            <input type="date" name="date_to" class="form-control" value="<?= esc($to) ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
        <div class="col-md-2">
            <a href="<?= site_url('admin/customers/' . $customerUnId . '/ledger') ?>" class="btn btn-light w-100">Reset</a>
        </div>
    </form>
</div>

<!-- Summary Stat Cards -->
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-xl-3">
        <div class="pd-card py-3 px-4">
            <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Opening Balance</div>
            <div class="fw-bold" style="font-size:1.3rem;">৳ <?= number_format($openingBalance, 2) ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="pd-card py-3 px-4">
            <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Total Sales (Debit)</div>
            <div class="fw-bold" style="font-size:1.3rem;color:#FA896B;">৳ <?= number_format($totalDebit, 2) ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="pd-card py-3 px-4">
            <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Total Received (Credit)</div>
            <div class="fw-bold" style="font-size:1.3rem;color:#02a98f;">৳ <?= number_format($totalCredit, 2) ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="pd-card py-3 px-4" style="<?= $closingBalance > 0 ? 'border-left:4px solid #FA896B;' : 'border-left:4px solid #02a98f;' ?>">
            <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Closing Balance</div>
            <div class="fw-bold" style="font-size:1.3rem;color:<?= $closingBalance > 0 ? '#FA896B' : '#02a98f' ?>;">
                ৳ <?= number_format(abs($closingBalance), 2) ?>
                <?= $closingBalance > 0 ? '<small class="fw-normal" style="font-size:.7rem;">(Due)</small>' : '<small class="fw-normal" style="font-size:.7rem;">(Clear)</small>' ?>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Table -->
<div class="pd-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold m-0">Transactions
            <span class="badge bg-secondary ms-2" style="font-size:.7rem;"><?= count($transactions) ?></span>
        </h6>
        <small class="text-muted">Period: <?= esc($from) ?> to <?= esc($to) ?></small>
    </div>

    <div class="table-responsive">
        <table class="table align-middle" style="font-size:.875rem;">
            <thead>
                <tr>
                    <th style="width:110px;">Date</th>
                    <th style="width:100px;">Type</th>
                    <th>Reference</th>
                    <th>Description</th>
                    <th class="text-end" style="width:130px;">Debit (৳)</th>
                    <th class="text-end" style="width:130px;">Credit (৳)</th>
                    <th class="text-end" style="width:140px;">Balance (৳)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Opening Balance Row -->
                <tr style="background:#f8f9fa;">
                    <td colspan="4" class="text-muted fw-semibold" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.3px;">
                        Opening Balance (before <?= esc($from) ?>)
                    </td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end fw-bold" style="color:<?= $openingBalance > 0 ? '#FA896B' : ($openingBalance < 0 ? '#02a98f' : 'inherit') ?>;">
                        ৳ <?= number_format($openingBalance, 2) ?>
                    </td>
                </tr>

                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.4;"></i>
                            No transactions found for this period.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $t): ?>
                        <?php
                            $isSale    = $t['type'] === 'sale';
                            $isPayment = $t['type'] === 'payment';
                            $rowBg     = $isSale ? 'rgba(250,137,107,.06)' : 'rgba(2,169,143,.06)';
                            $balColor  = $t['balance'] > 0 ? '#FA896B' : ($t['balance'] < 0 ? '#02a98f' : '#6c757d');
                        ?>
                        <tr style="background:<?= $rowBg ?>;">
                            <td><?= esc($t['date']) ?></td>
                            <td>
                                <?php if ($isSale): ?>
                                    <span class="badge-danger-soft" style="font-size:.72rem;">Sale</span>
                                <?php else: ?>
                                    <span class="badge-success-soft" style="font-size:.72rem;">Payment</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted" style="font-size:.82rem;"><?= esc($t['reference'] ?: '—') ?></td>
                            <td><?= esc($t['description']) ?></td>
                            <td class="text-end fw-semibold" style="color:#FA896B;">
                                <?= $t['debit'] > 0 ? '৳ ' . number_format($t['debit'], 2) : '—' ?>
                            </td>
                            <td class="text-end fw-semibold" style="color:#02a98f;">
                                <?= $t['credit'] > 0 ? '৳ ' . number_format($t['credit'], 2) : '—' ?>
                            </td>
                            <td class="text-end fw-bold" style="color:<?= $balColor ?>;">
                                ৳ <?= number_format($t['balance'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Totals Row -->
                <?php if (! empty($transactions)): ?>
                    <tr style="background:#f1f3f5;border-top:2px solid #dee2e6;">
                        <td colspan="4" class="fw-bold text-end text-muted" style="font-size:.82rem;text-transform:uppercase;letter-spacing:.3px;">Totals</td>
                        <td class="text-end fw-bold" style="color:#FA896B;">৳ <?= number_format($totalDebit, 2) ?></td>
                        <td class="text-end fw-bold" style="color:#02a98f;">৳ <?= number_format($totalCredit, 2) ?></td>
                        <td class="text-end fw-bold" style="color:<?= $closingBalance > 0 ? '#FA896B' : '#02a98f' ?>;">
                            ৳ <?= number_format($closingBalance, 2) ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
