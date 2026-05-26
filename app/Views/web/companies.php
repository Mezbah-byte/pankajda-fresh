<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<section class="hero" style="padding:80px 0 50px;">
    <div class="container text-center">
        <span class="pill">Our group</span>
        <h1>The companies under our umbrella</h1>
        <p class="lead mt-3 mx-auto">A diverse group of businesses operating across services, imports, agriculture and trading.</p>
    </div>
</section>
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php foreach (($companies ?? []) as $c): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon"><i class="bi bi-building-fill"></i></div>
                        <h5 class="fw-bold"><?= esc($c['company_name']) ?></h5>
                        <p class="text-muted mb-2"><?= esc($c['company_type'] ?? 'Business') ?></p>
                        <?php if (! empty($c['city'])): ?><p class="small text-muted"><i class="bi bi-geo-alt"></i> <?= esc($c['city']) ?></p><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($companies)): ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-buildings" style="font-size:3rem;color:#cbcae3;"></i>
                    <p class="mt-3">Companies will be listed here once added.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
