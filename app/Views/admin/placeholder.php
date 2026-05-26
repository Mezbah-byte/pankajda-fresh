<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="pd-card text-center" style="padding: 80px 30px;">
    <div class="mx-auto rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
         style="width: 96px; height: 96px; background: linear-gradient(135deg, #5e60ce, #6930c3);">
        <i class="bi <?= esc($icon ?? 'bi-hourglass-split') ?>" style="font-size: 2.6rem; color: #fff;"></i>
    </div>
    <h2 class="fw-bold mb-3"><?= esc($module ?? 'Module') ?> coming soon</h2>
    <p class="text-muted mx-auto" style="max-width: 540px;">
        The <strong><?= esc($module ?? '') ?></strong> module is on the roadmap. The database schema and demo data
        are already in place — controllers, services, and views will land in the next build session.
    </p>
    <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-primary mt-3">
        <i class="bi bi-arrow-left me-2"></i>Back to dashboard
    </a>
</div>

<?= $this->endSection() ?>
