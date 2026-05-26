<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Customer Dues</h4>
        <p class="text-muted small m-0">
            <?= number_format($data['count']) ?> customers with outstanding balance &middot;
            Total <span class="text-danger fw-semibold">৳ <?= number_format($data['total_due'], 0) ?></span>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="?export=csv" class="btn btn-light"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV</a>
        <button class="btn btn-light" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
        <a href="<?= site_url('admin/reports') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="pd-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Customer</th><th>Phone</th><th>City</th><th class="text-end">Opening</th><th class="text-end">Credit Limit</th><th class="text-end">Current Due</th></tr></thead>
            <tbody>
                <?php foreach ($data['rows'] as $r): ?>
                    <tr>
                        <td><a href="<?= site_url('admin/customers/' . $r['un_id']) ?>" class="fw-semibold text-decoration-none"><?= esc($r['customer_name']) ?></a></td>
                        <td><?= esc($r['phone'] ?? '-') ?></td>
                        <td><?= esc($r['city'] ?? '-') ?></td>
                        <td class="text-end">৳ <?= number_format((float) $r['opening_balance'], 0) ?></td>
                        <td class="text-end">৳ <?= number_format((float) $r['credit_limit'], 0) ?></td>
                        <td class="text-end fw-bold text-danger">৳ <?= number_format((float) $r['current_due'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($data['rows'])): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No customers with outstanding dues.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
