<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
function expiryBadgeSm(?string $date): string {
    if (!$date) return '<span class="badge bg-secondary" style="font-size:.7rem;">-</span>';
    $days = (int) ceil((strtotime($date) - time()) / 86400);
    if ($days < 0)   return '<span class="badge bg-danger"   style="font-size:.7rem;">Expired</span>';
    if ($days <= 30) return '<span class="badge bg-danger"   style="font-size:.7rem;">' . $days . 'd</span>';
    if ($days <= 90) return '<span class="badge bg-warning text-dark" style="font-size:.7rem;">' . $days . 'd</span>';
    return '<span class="badge bg-success" style="font-size:.7rem;">' . $days . 'd</span>';
}
$t  = $totals     ?? [];
$sc = $stage_counts ?? [];
$sl = $stages_list  ?? [];
?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-passport me-2"></i>Visas</h4>
        <ul class="mz-breadcrumb"><li>Business</li><li>Visas</li></ul>
    </div>
    <a href="<?= site_url('admin/visas/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Visa
    </a>
</div>

<!-- ── Summary Stats ──────────────────────────────────────────────── -->
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:1.8rem;font-weight:700;color:var(--mz-primary);"><?= $t['total'] ?? 0 ?></div>
            <div class="text-muted" style="font-size:.8rem;">Total Visas</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:1.8rem;font-weight:700;color:#13DEB9;"><?= ($sc['approved'] ?? 0) + ($sc['delivered'] ?? 0) ?></div>
            <div class="text-muted" style="font-size:.8rem;">Approved / Delivered</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:1.8rem;font-weight:700;color:#FFAE1F;"><?= ($sc['processing'] ?? 0) + ($sc['biometrics'] ?? 0) ?></div>
            <div class="text-muted" style="font-size:.8rem;">In Progress</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:1.4rem;font-weight:700;color:#FA896B;">৳ <?= number_format($t['due'] ?? 0, 0) ?></div>
            <div class="text-muted" style="font-size:.8rem;">Outstanding Due</div>
        </div>
    </div>
</div>

<!-- ── View Tabs ──────────────────────────────────────────────────── -->
<ul class="nav nav-tabs mb-0" style="border-bottom:0;">
    <li class="nav-item">
        <a class="nav-link active" href="<?= site_url('admin/visas') ?>">
            <i class="bi bi-list-ul me-1"></i>List View
            <span class="badge bg-primary ms-1" style="font-size:.7rem;"><?= $t['total'] ?? 0 ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= site_url('admin/visas/pipeline') ?>">
            <i class="bi bi-kanban me-1"></i>Pipeline
        </a>
    </li>
</ul>

<div class="pd-card" style="border-top-left-radius:0;">

    <?php if ($flash = session()->getFlashdata('success')): ?><div class="alert alert-success mb-3"><?= esc($flash) ?></div><?php endif; ?>
    <?php if ($flash = session()->getFlashdata('error')): ?><div class="alert alert-danger mb-3"><?= esc($flash) ?></div><?php endif; ?>

    <!-- Filters -->
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="text" class="form-control" name="q"
                   placeholder="Search name, beneficiary, passport…"
                   value="<?= esc($filters['q'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <select name="company_un_id" class="form-select">
                <option value="">All companies</option>
                <?php foreach (($companies ?? []) as $c): ?>
                    <option value="<?= esc($c['un_id']) ?>"
                        <?= ($filters['company_un_id'] ?? '') === $c['un_id'] ? 'selected' : '' ?>>
                        <?= esc($c['company_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All stages</option>
                <?php foreach ($sl as $key => $info): ?>
                    <option value="<?= esc($key) ?>" <?= ($filters['status'] ?? '') === $key ? 'selected' : '' ?>>
                        <?= esc($info['label']) ?>
                        <?php if ($sc[$key] ?? 0): ?>(<?= $sc[$key] ?>)<?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="payment_status" class="form-select">
                <option value="">All payments</option>
                <option value="paid"    <?= ($filters['payment_status'] ?? '') === 'paid'    ? 'selected' : '' ?>>Paid</option>
                <option value="partial" <?= ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' ?>>Partial</option>
                <option value="due"     <?= ($filters['payment_status'] ?? '') === 'due'     ? 'selected' : '' ?>>Due</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
        <div class="col-md-1">
            <a href="<?= site_url('admin/visas') ?>" class="btn btn-light w-100" title="Reset"><i class="bi bi-x-circle"></i></a>
        </div>
    </form>

    <!-- Active stage filter banner -->
    <?php if (! empty($filters['status']) && isset($sl[$filters['status']])): ?>
        <div class="d-flex align-items-center gap-2 mb-3 px-3 py-2 rounded" style="background:#ECF2FF;">
            <i class="bi bi-funnel-fill" style="color:var(--mz-primary);"></i>
            <span style="font-size:.88rem;">Filtered by stage: <strong><?= esc($sl[$filters['status']]['label']) ?></strong></span>
            <a href="<?= site_url('admin/visas') ?>" class="ms-auto btn btn-sm btn-light">Clear</a>
        </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead>
                <tr>
                    <th>Visa / Beneficiary</th>
                    <th>Stage</th>
                    <th>Route</th>
                    <th class="text-end">Purchase</th>
                    <th class="text-end text-success">Selling</th>
                    <th class="text-end">Profit</th>
                    <th class="text-end">Due</th>
                    <th>Payment</th>
                    <th>Expiry</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($visas ?? []) as $v):
                    $profit = (float)($v['selling_price'] ?? 0) - (float)($v['purchase_price'] ?? 0) - (float)($v['extra_costs'] ?? 0);
                    $stageInfo = $sl[$v['status'] ?? ''] ?? null;
                ?>
                    <tr>
                        <td>
                            <a href="<?= site_url('admin/visas/' . $v['un_id']) ?>"
                               class="fw-semibold text-decoration-none" style="color:var(--mz-primary);"><?= esc($v['visa_name']) ?></a>
                            <div class="text-muted" style="font-size:.78rem;">
                                <?= esc($v['beneficiary_name'] ?? '') ?>
                                <?php if (! empty($v['passport_no'])): ?>&middot; PP: <?= esc($v['passport_no']) ?><?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($stageInfo): ?>
                                <span class="badge bg-<?= esc($stageInfo['color']) ?>-subtle text-<?= esc($stageInfo['color']) ?>" style="font-size:.75rem;"><?= esc($stageInfo['label']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary" style="font-size:.75rem;"><?= esc($v['status'] ?? '-') ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:.82rem;">
                            <?php if (!empty($v['from_country']) || !empty($v['country'])): ?>
                                <span class="text-muted"><?= esc($v['from_country'] ?? '') ?></span>
                                <?php if (!empty($v['from_country']) && !empty($v['country'])): ?><i class="bi bi-arrow-right text-muted mx-1" style="font-size:.7rem;"></i><?php endif; ?>
                                <?= esc($v['country'] ?? '') ?>
                            <?php else: ?><span class="text-muted">-</span><?php endif; ?>
                        </td>
                        <td class="text-end" style="font-size:.88rem;">৳ <?= number_format((float)($v['purchase_price'] ?? 0), 0) ?></td>
                        <td class="text-end text-success" style="font-size:.88rem;">৳ <?= number_format((float)($v['selling_price'] ?? 0), 0) ?></td>
                        <td class="text-end fw-semibold" style="color:<?= $profit >= 0 ? '#198754' : '#dc3545' ?>;font-size:.88rem;">
                            <?= $profit < 0 ? '−' : '' ?>৳ <?= number_format(abs($profit), 0) ?>
                        </td>
                        <td class="text-end" style="color:#FA896B;font-size:.88rem;">৳ <?= number_format((float)($v['due_amount'] ?? 0), 0) ?></td>
                        <td>
                            <?php $ps = $v['payment_status'] ?? 'due'; ?>
                            <?php if ($ps === 'paid'): ?>
                                <span class="badge-success-soft">Paid</span>
                            <?php elseif ($ps === 'partial'): ?>
                                <span class="badge-warning-soft">Partial</span>
                            <?php else: ?>
                                <span class="badge-danger-soft">Due</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:.82rem;">
                            <?php if (!empty($v['visa_expiry_date'])): ?>
                                <div><?= date('d M y', strtotime($v['visa_expiry_date'])) ?></div>
                                <?= expiryBadgeSm($v['visa_expiry_date']) ?>
                            <?php else: ?><span class="text-muted">-</span><?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/visas/' . $v['un_id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/visas/' . $v['un_id'] . '/edit') ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($visas)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="bi bi-passport" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
                            <span class="text-muted">No visas found.</span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (! empty($pagination) && $pagination['last_page'] > 1):
        $qStr = http_build_query(array_filter([
            'q'              => $filters['q'] ?? '',
            'company_un_id'  => $filters['company_un_id'] ?? '',
            'payment_status' => $filters['payment_status'] ?? '',
            'status'         => $filters['status'] ?? '',
        ]));
    ?>
        <nav class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted" style="font-size:.82rem;">Showing <?= count($visas) ?> of <?= $pagination['total'] ?></div>
            <ul class="pagination pagination-sm m-0">
                <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                    <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $p ?>&<?= $qStr ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
