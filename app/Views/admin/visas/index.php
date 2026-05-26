<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Visas</h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li>Visas</li>
        </ul>
    </div>
    <a href="<?= site_url('admin/visas/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Visa
    </a>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" class="form-control" name="q" placeholder="Search visa, beneficiary, passport…" value="<?= esc($filters['q'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <select name="company_un_id" class="form-select">
                <option value="">All companies</option>
                <?php foreach (($companies ?? []) as $c): ?>
                    <option value="<?= esc($c['un_id']) ?>" <?= ($filters['company_un_id'] ?? '') === $c['un_id'] ? 'selected' : '' ?>><?= esc($c['company_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="payment_status" class="form-select">
                <option value="">All payment status</option>
                <option value="paid"    <?= ($filters['payment_status'] ?? '') === 'paid'    ? 'selected' : '' ?>>Paid</option>
                <option value="partial" <?= ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' ?>>Partial</option>
                <option value="due"     <?= ($filters['payment_status'] ?? '') === 'due'     ? 'selected' : '' ?>>Due</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Visa</th>
                    <th>Beneficiary</th>
                    <th>Country</th>
                    <th class="text-end">Cost</th>
                    <th class="text-end">Paid</th>
                    <th class="text-end">Due</th>
                    <th>Status</th>
                    <th>Expiry</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($visas ?? []) as $v): ?>
                    <tr>
                        <td>
                            <a href="<?= site_url('admin/visas/' . $v['un_id']) ?>" class="fw-semibold text-decoration-none" style="color:var(--mz-primary);"><?= esc($v['visa_name']) ?></a>
                            <?php if (! empty($v['visa_number'])): ?>
                                <div class="text-muted" style="font-size:.75rem;"><?= esc($v['visa_number']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= esc($v['beneficiary_name'] ?? '-') ?>
                            <?php if (! empty($v['passport_no'])): ?>
                                <div class="text-muted" style="font-size:.75rem;">PP: <?= esc($v['passport_no']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($v['country'] ?? '-') ?></td>
                        <td class="text-end">৳ <?= number_format((float) $v['visa_cost'], 0) ?></td>
                        <td class="text-end" style="color:#02a98f;">৳ <?= number_format((float) $v['paid_amount'], 0) ?></td>
                        <td class="text-end" style="color:#FA896B;">৳ <?= number_format((float) $v['due_amount'], 0) ?></td>
                        <td>
                            <?php $st = $v['payment_status']; ?>
                            <?php if ($st === 'paid'): ?>
                                <span class="badge-success-soft">Paid</span>
                            <?php elseif ($st === 'partial'): ?>
                                <span class="badge-warning-soft">Partial</span>
                            <?php else: ?>
                                <span class="badge-danger-soft">Due</span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($v['visa_expiry_date'] ?? '-') ?></td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/visas/' . $v['un_id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/visas/' . $v['un_id'] . '/edit') ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($visas)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-passport" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
                            <span class="text-muted">No visas yet. Click "Add Visa" to get started.</span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (! empty($pagination) && $pagination['last_page'] > 1): ?>
        <nav class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted" style="font-size:.82rem;">Showing <?= count($visas) ?> of <?= $pagination['total'] ?></div>
            <ul class="pagination pagination-sm m-0">
                <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                    <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($filters['q'] ?? '') ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
