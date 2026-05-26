<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Customer Dues</h4>
        <ul class="mz-breadcrumb">
            <li><a href="<?= site_url('admin/reports') ?>" class="text-muted text-decoration-none">Reports</a></li>
            <li>Customer Dues</li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="?export=csv" class="btn btn-light"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV</a>
        <button class="btn btn-light" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
        <a href="<?= site_url('admin/reports') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<!-- Summary -->
<div class="pd-card py-3 px-4 mb-3" style="background:linear-gradient(135deg,#FDECEA,#fff5f3);">
    <div class="d-flex flex-wrap gap-4">
        <div>
            <span class="text-muted" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">Customers with Dues</span>
            <div class="fw-bold" style="font-size:1.1rem;color:var(--mz-text-primary);"><?= number_format($data['count']) ?></div>
        </div>
        <div>
            <span class="text-muted" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">Total Outstanding</span>
            <div class="fw-bold" style="font-size:1.1rem;color:#FA896B;">৳ <?= number_format($data['total_due'], 0) ?></div>
        </div>
    </div>
</div>

<div class="pd-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr><th>Customer</th><th>Phone</th><th>City</th><th class="text-end">Opening</th><th class="text-end">Credit Limit</th><th class="text-end">Current Due</th></tr>
            </thead>
            <tbody>
                <?php foreach ($data['rows'] as $r): ?>
                    <tr>
                        <td>
                            <a href="<?= site_url('admin/customers/' . $r['un_id']) ?>" class="fw-semibold text-decoration-none" style="color:var(--mz-primary);"><?= esc($r['customer_name']) ?></a>
                        </td>
                        <td><?= esc($r['phone'] ?? '-') ?></td>
                        <td><?= esc($r['city'] ?? '-') ?></td>
                        <td class="text-end">৳ <?= number_format((float) $r['opening_balance'], 0) ?></td>
                        <td class="text-end">৳ <?= number_format((float) $r['credit_limit'], 0) ?></td>
                        <td class="text-end fw-bold" style="color:#FA896B;">৳ <?= number_format((float) $r['current_due'], 2) ?></td>
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
