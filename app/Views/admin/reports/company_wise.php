<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Company-wise Report</h4>
        <ul class="mz-breadcrumb">
            <li><a href="<?= site_url('admin/reports') ?>" class="text-muted text-decoration-none">Reports</a></li>
            <li>Company-wise</li>
        </ul>
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
            <thead>
                <tr>
                    <th>Company</th>
                    <th class="text-end">Sales</th>
                    <th class="text-end">Sales Due</th>
                    <th class="text-end">Expenses</th>
                    <th class="text-end">Visa Cost</th>
                    <th class="text-end">Visa Due</th>
                    <th class="text-end">Net</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['rows'] as $r): ?>
                    <tr>
                        <td>
                            <a href="<?= site_url('admin/companies/' . $r['un_id']) ?>" class="fw-semibold text-decoration-none" style="color:var(--mz-primary);"><?= esc($r['company_name']) ?></a>
                        </td>
                        <td class="text-end">৳ <?= number_format((float) $r['sales'], 0) ?></td>
                        <td class="text-end" style="color:#FA896B;">৳ <?= number_format((float) $r['sales_due'], 0) ?></td>
                        <td class="text-end">৳ <?= number_format((float) $r['expenses'], 0) ?></td>
                        <td class="text-end">৳ <?= number_format((float) $r['visa_cost'], 0) ?></td>
                        <td class="text-end" style="color:#FA896B;">৳ <?= number_format((float) $r['visa_due'], 0) ?></td>
                        <td class="text-end fw-bold" style="color:<?= ((float) $r['net']) >= 0 ? '#02a98f' : '#FA896B' ?>;">
                            ৳ <?= number_format((float) $r['net'], 0) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($data['rows'])): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">No companies yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
