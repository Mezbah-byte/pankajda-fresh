<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Visa Pipeline</h4>
        <ul class="mz-breadcrumb"><li>Visas</li><li>Pipeline View</li></ul>
    </div>
    <a href="<?= site_url('admin/visas') ?>" class="btn btn-light"><i class="bi bi-list-ul me-1"></i>List View</a>
</div>

<!-- Stage counts -->
<div class="row g-3 mb-4">
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
    foreach ($stages as $key => $stage):
    ?>
        <div class="col-md-3 col-6">
            <div class="pd-stat <?= $stageGradients[$key] ?? 'gradient-1' ?>">
                <div class="stat-icon"><i class="bi <?= $stageIcons[$key] ?? 'bi-circle' ?>"></i></div>
                <div class="stat-label"><?= esc($stage['label']) ?></div>
                <div class="stat-value"><?= $counts[$key] ?? 0 ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Kanban-style columns -->
<div class="d-flex gap-3 overflow-auto pb-3" style="align-items:flex-start;">
    <?php foreach ($stages as $key => $stage): ?>
        <?php if (empty($recent[$key])) continue; ?>
        <div style="min-width:260px;flex:0 0 260px;">
            <div class="d-flex align-items-center justify-content-between mb-2 px-2">
                <span class="badge bg-<?= esc($stage['color']) ?>-subtle text-<?= esc($stage['color']) ?> fw-semibold" style="font-size:.82rem;"><?= esc($stage['label']) ?></span>
                <span class="text-muted" style="font-size:.78rem;"><?= $counts[$key] ?? 0 ?> total</span>
            </div>
            <?php foreach ($recent[$key] as $visa): ?>
                <a href="<?= site_url('admin/visas/' . $visa['un_id']) ?>" class="d-block text-decoration-none mb-2">
                    <div class="pd-card" style="border:1px solid var(--mz-border)!important;padding:14px;">
                        <div class="fw-semibold" style="font-size:.9rem;"><?= esc($visa['applicant_name'] ?? $visa['un_id']) ?></div>
                        <div class="text-muted" style="font-size:.75rem;"><?= esc($visa['visa_type'] ?? '') ?> · <?= esc($visa['country'] ?? '') ?></div>
                        <div class="text-muted mt-1" style="font-size:.72rem;">Updated: <?= esc(date('d M', strtotime($visa['updated_at']))) ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
            <?php if (($counts[$key]??0) > count($recent[$key])): ?>
                <a href="<?= site_url('admin/visas?status=' . $key) ?>" class="btn btn-sm btn-light w-100" style="font-size:.78rem;">
                    +<?= ($counts[$key]??0) - count($recent[$key]) ?> more
                </a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <?php if (empty($recent)): ?>
        <div class="text-center text-muted py-5 w-100">
            <i class="bi bi-kanban" style="font-size:3rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
            No visa applications found.
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
