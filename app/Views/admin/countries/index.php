<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-globe me-2"></i>Countries</h4>
        <ul class="mz-breadcrumb">
            <li>Admin</li>
            <li><a href="<?= site_url('admin/settings') ?>" class="text-muted text-decoration-none">Settings</a></li>
            <li>Countries</li>
        </ul>
    </div>
    <a href="<?= site_url('admin/settings') ?>" class="btn btn-light"><i class="bi bi-gear me-2"></i>Settings</a>
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
            <h6 class="fw-bold mb-4">Add Country</h6>
            <form method="post" action="<?= site_url('admin/countries') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Country Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name"
                           value="<?= esc(old('name')) ?>" placeholder="e.g. Bangladesh" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">ISO Code <span class="text-muted">(optional)</span></label>
                    <input type="text" class="form-control" name="iso_code" maxlength="3"
                           value="<?= esc(old('iso_code')) ?>" placeholder="BD, SA, AE…" style="text-transform:uppercase;">
                    <div class="form-text">2-3 letter ISO 3166-1 code.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sort Order</label>
                    <input type="number" class="form-control" name="sort_order" value="<?= esc(old('sort_order', 0)) ?>" min="0">
                </div>
                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                <button class="btn btn-primary w-100"><i class="bi bi-plus-circle me-2"></i>Add Country</button>
            </form>
            <hr class="my-3">
            <div class="text-muted" style="font-size:.8rem;">
                <i class="bi bi-info-circle me-1"></i>
                Seed default countries: <code>php spark db:seed CountriesSeeder</code>
            </div>
        </div>
    </div>

    <!-- List -->
    <div class="col-md-8">
        <div class="pd-card">
            <!-- Filters -->
            <form method="get" class="row g-2 mb-4">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="q"
                           placeholder="Search country name…"
                           value="<?= esc($filters['q'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select name="is_active" class="form-select">
                        <option value="">All status</option>
                        <option value="1" <?= ($filters['is_active'] ?? '') === '1' ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= ($filters['is_active'] ?? '') === '0' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                </div>
                <div class="col-md-1">
                    <a href="<?= site_url('admin/countries') ?>" class="btn btn-light w-100" title="Reset"><i class="bi bi-x-circle"></i></a>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted" style="font-size:.85rem;"><?= $pagination['total'] ?? 0 ?> countries total</span>
            </div>

            <?php if (empty($countries)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-globe" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
                    <p class="text-muted">No countries found.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle table-hover" style="font-size:.875rem;">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Name</th>
                                <th style="width:80px;">ISO</th>
                                <th style="width:100px;">Order</th>
                                <th style="width:80px;">Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $offset = (($pagination['page'] ?? 1) - 1) * ($pagination['per_page'] ?? 30); ?>
                            <?php foreach ($countries as $i => $c): ?>
                                <tr>
                                    <td class="text-muted"><?= $offset + $i + 1 ?></td>
                                    <td class="fw-semibold"><?= esc($c['name']) ?></td>
                                    <td>
                                        <?php if ($c['iso_code']): ?>
                                            <span class="badge-secondary-soft"><?= esc($c['iso_code']) ?></span>
                                        <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                                    </td>
                                    <td class="text-muted"><?= esc($c['sort_order']) ?></td>
                                    <td>
                                        <form method="post" action="<?= site_url('admin/countries/' . $c['un_id'] . '/toggle') ?>" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm border-0 <?= $c['is_active'] ? 'badge-success-soft' : 'badge-secondary-soft' ?>"
                                                    title="Toggle"><?= $c['is_active'] ? 'Active' : 'Off' ?></button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= site_url('admin/countries/' . $c['un_id'] . '/edit') ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <form method="post" action="<?= site_url('admin/countries/' . $c['un_id'] . '/delete') ?>"
                                              class="d-inline" onsubmit="return confirm('Delete \'<?= esc(addslashes($c['name'])) ?>\'?')">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($pagination) && $pagination['last_page'] > 1): ?>
                    <nav class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted" style="font-size:.82rem;">Showing <?= count($countries) ?> of <?= $pagination['total'] ?></div>
                        <ul class="pagination pagination-sm m-0">
                            <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                                <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($filters['q'] ?? '') ?>&is_active=<?= urlencode($filters['is_active'] ?? '') ?>"><?= $p ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
