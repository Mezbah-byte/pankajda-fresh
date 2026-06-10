<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-tags me-2"></i>Company Types</h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/companies') ?>" class="text-muted text-decoration-none">Companies</a></li>
            <li>Company Types</li>
        </ul>
    </div>
    <a href="<?= site_url('admin/companies') ?>" class="btn btn-light"><i class="bi bi-buildings me-2"></i>Companies</a>
</div>

<?php if ($flash = session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-3"><?= esc($flash) ?></div>
<?php endif; ?>
<?php if ($flash = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-3"><?= esc($flash) ?></div>
<?php endif; ?>
<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger mb-3"><ul class="mb-0"><?php foreach ((array)$errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="row g-3">
    <!-- Add form -->
    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-bold mb-4">Add Company Type</h6>
            <form method="post" action="<?= site_url('admin/company-types') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name"
                           value="<?= esc(old('name')) ?>"
                           placeholder="e.g. Trading, Import / Export…" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sort Order</label>
                    <input type="number" class="form-control" name="sort_order" value="<?= esc(old('sort_order', 0)) ?>" min="0">
                    <div class="form-text">Lower = appears first.</div>
                </div>
                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                <button class="btn btn-primary w-100"><i class="bi bi-plus-circle me-2"></i>Add Type</button>
            </form>
        </div>
    </div>

    <!-- List -->
    <div class="col-md-8">
        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold m-0">All Company Types <span class="badge bg-secondary ms-2"><?= count($types) ?></span></h6>
            </div>

            <?php if (empty($types)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-tags" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
                    <p class="text-muted">No company types yet. Add one on the left.</p>
                    <p class="text-muted small">Tip: run <code>php spark db:seed CompanyTypesSeeder</code> to load defaults.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Name</th>
                                <th style="width:100px;">Order</th>
                                <th style="width:80px;">Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($types as $i => $t): ?>
                                <tr id="row-<?= esc($t['un_id']) ?>">
                                    <td class="text-muted"><?= $i + 1 ?></td>
                                    <td>
                                        <!-- Inline edit -->
                                        <form method="post" action="<?= site_url('admin/company-types/' . $t['un_id']) ?>" class="d-flex align-items-center gap-2">
                                            <?= csrf_field() ?>
                                            <input type="text" class="form-control form-control-sm" name="name"
                                                   value="<?= esc($t['name']) ?>" style="max-width:200px;" required>
                                            <input type="hidden" name="sort_order" value="<?= esc($t['sort_order']) ?>">
                                            <input type="hidden" name="is_active" value="<?= esc($t['is_active']) ?>">
                                            <button class="btn btn-sm btn-outline-primary" title="Save"><i class="bi bi-check2"></i></button>
                                        </form>
                                    </td>
                                    <td>
                                        <form method="post" action="<?= site_url('admin/company-types/' . $t['un_id']) ?>" class="d-flex align-items-center gap-1">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="name" value="<?= esc($t['name']) ?>">
                                            <input type="number" class="form-control form-control-sm" name="sort_order"
                                                   value="<?= esc($t['sort_order']) ?>" style="width:60px;" min="0" onchange="this.form.submit()">
                                            <input type="hidden" name="is_active" value="<?= esc($t['is_active']) ?>">
                                        </form>
                                    </td>
                                    <td>
                                        <form method="post" action="<?= site_url('admin/company-types/' . $t['un_id']) ?>">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="name" value="<?= esc($t['name']) ?>">
                                            <input type="hidden" name="sort_order" value="<?= esc($t['sort_order']) ?>">
                                            <input type="hidden" name="is_active" value="<?= $t['is_active'] ? 0 : 1 ?>">
                                            <button class="btn btn-sm <?= $t['is_active'] ? 'badge-success-soft border-0' : 'badge-secondary-soft border-0' ?>"
                                                    style="cursor:pointer;" title="Toggle active">
                                                <?= $t['is_active'] ? 'Active' : 'Off' ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <form method="post" action="<?= site_url('admin/company-types/' . $t['un_id'] . '/delete') ?>"
                                              class="d-inline" onsubmit="return confirm('Delete type \'<?= esc(addslashes($t['name'])) ?>\'?')">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
