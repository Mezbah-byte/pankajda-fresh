<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header">
    <div>
        <h4>Import Data</h4>
        <ul class="mz-breadcrumb"><li>Settings</li><li>Import</li></ul>
    </div>
</div>

<?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>
<?php if (session('import_errors')): ?>
    <div class="alert alert-warning">
        <strong>Some rows skipped:</strong>
        <ul class="mb-0 mt-2 small"><?php foreach (session('import_errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-md-5">
        <div class="pd-card">
            <h6 class="fw-semibold mb-4">Upload CSV File</h6>
            <form method="post" action="<?= site_url('admin/import/upload') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Module <span class="text-danger">*</span></label>
                    <select class="form-select" name="module" required>
                        <option value="">Select Module</option>
                        <?php foreach ($modules as $key => $label): ?>
                            <option value="<?= esc($key) ?>"><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">CSV File <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="file" accept=".csv" required>
                    <div class="form-text">Max 2MB. UTF-8 encoded CSV with header row.</div>
                </div>
                <button class="btn btn-primary w-100"><i class="bi bi-upload me-2"></i>Import</button>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="pd-card">
            <h6 class="fw-semibold mb-4">Download Templates</h6>
            <p class="text-muted" style="font-size:.85rem;">Download a CSV template for each module, fill in your data, then upload above.</p>
            <div class="row g-2">
                <?php foreach ($modules as $key => $label): ?>
                    <div class="col-md-6">
                        <a href="<?= site_url('admin/import/template/' . $key) ?>" class="d-flex align-items-center gap-2 p-3 rounded text-decoration-none" style="background:var(--mz-bg);border:1px solid var(--mz-border);">
                            <i class="bi bi-filetype-csv" style="font-size:1.5rem;color:var(--mz-primary);flex-shrink:0;"></i>
                            <div>
                                <div class="fw-semibold" style="font-size:.88rem;"><?= esc($label) ?></div>
                                <div class="text-muted" style="font-size:.75rem;">Download template</div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="pd-card mt-3">
            <h6 class="fw-semibold mb-3">Import Guidelines</h6>
            <ul class="mb-0 text-muted" style="font-size:.85rem;">
                <li class="mb-1">First row must be the header (field names — use the template).</li>
                <li class="mb-1">Date fields: <code>YYYY-MM-DD</code> format.</li>
                <li class="mb-1">Numeric fields: use <code>.</code> decimal separator, no currency symbols.</li>
                <li class="mb-1">Duplicate records are skipped and reported.</li>
                <li class="mb-1">Max 1000 rows per upload recommended.</li>
            </ul>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
