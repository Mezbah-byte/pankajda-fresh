<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-passport me-2"></i>Visas</h4>
        <ul class="mz-breadcrumb"><li>Business</li><li>Visas</li><li>Pipeline</li></ul>
    </div>
    <a href="<?= site_url('admin/visas/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Visa
    </a>
</div>

<?php
$stageIcons = [
    'applied'             => 'bi-file-earmark-text',
    'documents_submitted' => 'bi-folder-check',
    'biometrics'          => 'bi-fingerprint',
    'processing'          => 'bi-hourglass-split',
    'approved'            => 'bi-patch-check-fill',
    'rejected'            => 'bi-x-circle-fill',
    'delivered'           => 'bi-bag-check-fill',
];
$stageGradients = [
    'applied'             => 'gradient-3',
    'documents_submitted' => 'gradient-1',
    'biometrics'          => 'gradient-2',
    'processing'          => 'gradient-4',
    'approved'            => 'gradient-2',
    'rejected'            => 'gradient-4',
    'delivered'           => 'gradient-1',
];
$total = array_sum($counts);
?>

<!-- ── Stage Count Cards ──────────────────────────────────────────── -->
<div class="row g-3 mb-3">
    <?php $newKey = \App\Services\VisaPipelineService::NEW_KEY; ?>
    <div class="col-md-3 col-6">
        <a href="<?= site_url('admin/visas') ?>" class="text-decoration-none">
            <div class="pd-stat gradient-3">
                <div class="stat-icon"><i class="bi bi-plus-circle"></i></div>
                <div class="stat-label">New / Not Started</div>
                <div class="stat-value"><?= $counts[$newKey] ?? 0 ?></div>
            </div>
        </a>
    </div>
    <?php foreach ($stages as $key => $stage): ?>
        <div class="col-md-3 col-6">
            <a href="<?= site_url('admin/visas?status=' . $key) ?>" class="text-decoration-none">
                <div class="pd-stat <?= $stageGradients[$key] ?? 'gradient-1' ?>">
                    <div class="stat-icon"><i class="bi <?= $stageIcons[$key] ?? 'bi-circle' ?>"></i></div>
                    <div class="stat-label"><?= esc($stage['label']) ?></div>
                    <div class="stat-value"><?= $counts[$key] ?? 0 ?></div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<!-- ── View Tabs ──────────────────────────────────────────────────── -->
<ul class="nav nav-tabs mb-0" style="border-bottom:0;">
    <li class="nav-item">
        <a class="nav-link" href="<?= site_url('admin/visas') ?>">
            <i class="bi bi-list-ul me-1"></i>List View
            <span class="badge bg-secondary ms-1" style="font-size:.7rem;"><?= $total ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="<?= site_url('admin/visas/pipeline') ?>">
            <i class="bi bi-kanban me-1"></i>Pipeline
        </a>
    </li>
</ul>

<?php
// Build full column list: "New" bucket first, then pipeline stages
$newKey = \App\Services\VisaPipelineService::NEW_KEY;
$allColumns = [
    $newKey => ['label' => 'New', 'color' => 'dark', 'icon' => 'bi-plus-circle', 'link' => null],
];
foreach ($stages as $k => $s) {
    $allColumns[$k] = array_merge($s, ['icon' => $stageIcons[$k] ?? 'bi-circle', 'link' => site_url('admin/visas?status=' . $k)]);
}
?>
<div class="pd-card" style="border-top-left-radius:0;overflow-x:auto;">
    <!-- Kanban Board -->
    <div class="d-flex gap-3 pb-2" style="align-items:flex-start;min-width:max-content;">
        <?php foreach ($allColumns as $key => $col):
            $columnVisas    = $recent[$key] ?? [];
            $total_in_stage = $counts[$key] ?? 0;
            $more           = $total_in_stage - count($columnVisas);
            $color          = $col['color'];
            $isNew          = $key === $newKey;
        ?>
            <div style="width:240px;flex-shrink:0;">
                <!-- Column header -->
                <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi <?= esc($col['icon']) ?> text-<?= esc($color) ?>" style="font-size:.95rem;"></i>
                        <span class="fw-semibold" style="font-size:.85rem;"><?= esc($col['label']) ?></span>
                        <?php if ($isNew): ?><span class="badge bg-secondary-subtle text-secondary" style="font-size:.65rem;">Not Started</span><?php endif; ?>
                    </div>
                    <span class="badge bg-<?= esc($color) ?>-subtle text-<?= esc($color) ?>" style="font-size:.75rem;"><?= $total_in_stage ?></span>
                </div>

                <!-- Cards -->
                <?php if (empty($columnVisas)): ?>
                    <div class="rounded-2 text-center py-4" style="background:var(--mz-bg-soft,#f8f9fa);border:2px dashed #dee2e6;">
                        <i class="bi bi-inbox text-muted" style="font-size:1.3rem;"></i>
                        <div class="text-muted mt-1" style="font-size:.75rem;">None</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($columnVisas as $v): ?>
                        <a href="<?= site_url('admin/visas/' . $v['un_id']) ?>" class="d-block text-decoration-none mb-2">
                            <div class="rounded-2 p-3" style="background:var(--mz-card-bg,#fff);border:1px solid var(--mz-border,#dee2e6);transition:box-shadow .15s;"
                                 onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.08)'"
                                 onmouseout="this.style.boxShadow=''">
                                <div class="fw-semibold" style="font-size:.875rem;color:var(--mz-text-primary);"><?= esc($v['visa_name']) ?></div>
                                <?php if (!empty($v['beneficiary_name'])): ?>
                                    <div class="text-muted mt-1" style="font-size:.75rem;">
                                        <i class="bi bi-person me-1"></i><?= esc($v['beneficiary_name']) ?>
                                        <?php if (!empty($v['passport_no'])): ?> · <?= esc($v['passport_no']) ?><?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($v['country'])): ?>
                                    <div class="text-muted" style="font-size:.75rem;">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <?= esc($v['from_country'] ? $v['from_country'] . ' → ' . $v['country'] : $v['country']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($v['company_name'])): ?>
                                    <div class="text-muted" style="font-size:.72rem;"><i class="bi bi-building me-1"></i><?= esc($v['company_name']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($v['visa_expiry_date'])): ?>
                                    <?php $daysLeft = (int) ceil((strtotime($v['visa_expiry_date']) - time()) / 86400); ?>
                                    <div class="mt-1" style="font-size:.72rem;">
                                        <i class="bi bi-calendar-x me-1"></i>
                                        <span class="<?= $daysLeft < 0 ? 'text-danger' : ($daysLeft <= 30 ? 'text-danger' : ($daysLeft <= 90 ? 'text-warning' : 'text-muted')) ?>">
                                            Exp: <?= esc(date('d M y', strtotime($v['visa_expiry_date']))) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between align-items-center mt-2 pt-2" style="border-top:1px solid var(--mz-border,#f0f0f0);">
                                    <?php $ps = $v['payment_status'] ?? 'due'; ?>
                                    <span class="badge <?= $ps === 'paid' ? 'bg-success' : ($ps === 'partial' ? 'bg-warning text-dark' : 'bg-danger') ?>" style="font-size:.65rem;">
                                        <?= $ps === 'paid' ? 'Paid' : ($ps === 'partial' ? 'Partial' : '৳' . number_format((float)($v['due_amount'] ?? 0), 0) . ' due') ?>
                                    </span>
                                    <span class="text-muted" style="font-size:.7rem;"><?= esc(date('d M', strtotime($v['updated_at'] ?? 'now'))) ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>

                    <?php if ($more > 0): ?>
                        <a href="<?= $isNew ? site_url('admin/visas') : site_url('admin/visas?status=' . $key) ?>"
                           class="btn btn-sm btn-light w-100" style="font-size:.78rem;border-style:dashed;">
                            <i class="bi bi-plus me-1"></i>+<?= $more ?> more
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?= $this->endSection() ?>
