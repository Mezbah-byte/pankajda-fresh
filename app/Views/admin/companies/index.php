<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Companies</h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li>Companies</li>
        </ul>
    </div>
    <a href="<?= site_url('admin/companies/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Company
    </a>
</div>

<!-- ── Summary Cards ─────────────────────────────────────────────── -->
<?php $t = $totals ?? []; ?>
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:2rem;font-weight:700;color:var(--mz-primary);"><?= $t['total'] ?? 0 ?></div>
            <div class="text-muted" style="font-size:.8rem;">Total Companies</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:2rem;font-weight:700;color:#13DEB9;"><?= $t['active'] ?? 0 ?></div>
            <div class="text-muted" style="font-size:.8rem;">Active</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:2rem;font-weight:700;color:#FFAE1F;"><?= $t['inactive'] ?? 0 ?></div>
            <div class="text-muted" style="font-size:.8rem;">Inactive</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:2rem;font-weight:700;color:#5D87FF;"><?= max(0, ($t['total'] ?? 0) - ($t['active'] ?? 0) - ($t['inactive'] ?? 0)) ?></div>
            <div class="text-muted" style="font-size:.8rem;">Pending</div>
        </div>
    </div>
</div>

<?php if ($flash = session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-3"><?= esc($flash) ?></div>
<?php endif; ?>
<?php if ($flash = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-3"><?= esc($flash) ?></div>
<?php endif; ?>

<div class="pd-card">
    <!-- Filters -->
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" class="form-control" name="q"
                   placeholder="Search name, email, phone, trade license…"
                   value="<?= esc($filters['q'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                <option value="active"   <?= ($filters['status'] ?? '') === 'active'   ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="pending"  <?= ($filters['status'] ?? '') === 'pending'  ? 'selected' : '' ?>>Pending</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="company_type" class="form-select">
                <option value="">All types</option>
                <?php foreach (($company_types ?? []) as $ct): ?>
                    <option value="<?= esc($ct) ?>" <?= ($filters['company_type'] ?? '') === $ct ? 'selected' : '' ?>><?= esc($ct) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
        <div class="col-md-1">
            <a href="<?= site_url('admin/companies') ?>" class="btn btn-light w-100" title="Reset"><i class="bi bi-x-circle"></i></a>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Contact</th>
                    <th>Phone / Email</th>
                    <th>Currency</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($companies ?? []) as $c): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <?php if (! empty($c['logo_path'])): ?>
                                    <img src="<?= base_url($c['logo_path']) ?>" alt=""
                                         style="width:36px;height:36px;object-fit:contain;border-radius:6px;border:1px solid #eee;padding:2px;flex-shrink:0;">
                                <?php else: ?>
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-2 fw-bold"
                                         style="width:36px;height:36px;background:#ECF2FF;color:#5D87FF;font-size:.85rem;flex-shrink:0;">
                                        <?= esc(strtoupper(substr($c['company_name'], 0, 1))) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-semibold">
                                        <a href="<?= site_url('admin/companies/' . $c['un_id']) ?>"
                                           class="text-decoration-none" style="color:var(--mz-text-primary);"><?= esc($c['company_name']) ?></a>
                                    </div>
                                    <div class="text-muted" style="font-size:.73rem;"><?= esc(short_un_id($c['un_id'])) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="text-muted" style="font-size:.85rem;"><?= esc($c['company_type'] ?? '-') ?></span></td>
                        <td style="font-size:.85rem;"><?= esc($c['contact_person'] ?? '-') ?></td>
                        <td style="font-size:.85rem;">
                            <?php if ($c['phone'] ?? null): ?><div><?= esc($c['phone']) ?></div><?php endif; ?>
                            <?php if ($c['email'] ?? null): ?><div class="text-muted"><?= esc($c['email']) ?></div><?php endif; ?>
                        </td>
                        <td><span class="badge-secondary-soft"><?= esc($c['currency'] ?? 'BDT') ?></span></td>
                        <td>
                            <?php $st = $c['status'] ?? 'active'; ?>
                            <?php if ($st === 'active'): ?>
                                <span class="badge-success-soft">Active</span>
                            <?php elseif ($st === 'pending'): ?>
                                <span class="badge-warning-soft">Pending</span>
                            <?php else: ?>
                                <span class="badge-secondary-soft"><?= esc(ucfirst($st)) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/companies/' . $c['un_id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/companies/' . $c['un_id'] . '/edit') ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= site_url('admin/companies/' . $c['un_id'] . '/delete') ?>"
                                  class="d-inline" onsubmit="return confirm('Delete this company?');">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($companies)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-buildings" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
                            <span class="text-muted">No companies found.</span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (! empty($pagination) && $pagination['last_page'] > 1): ?>
        <nav class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted" style="font-size:.82rem;">Showing <?= count($companies) ?> of <?= $pagination['total'] ?></div>
            <ul class="pagination pagination-sm m-0">
                <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                    <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($filters['q'] ?? '') ?>&status=<?= urlencode($filters['status'] ?? '') ?>&company_type=<?= urlencode($filters['company_type'] ?? '') ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
