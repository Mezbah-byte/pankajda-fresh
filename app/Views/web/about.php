<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<section class="hero" style="padding:80px 0 50px;">
    <div class="container">
        <span class="pill">About us</span>
        <h1>Built for businesses that build things</h1>
        <p class="lead mt-3">Pankaj Da ERP started as an internal tool for managing a multi-business operation across visa services, vegetable imports, trading and farms. We've turned years of operational know-how into a clean, modern product.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="feature-card">
                    <div class="icon"><i class="bi bi-bullseye"></i></div>
                    <h4 class="fw-bold">Our Mission</h4>
                    <p class="text-muted">Help small and mid-sized businesses operate with the discipline and visibility of a large enterprise — without the cost or complexity.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="feature-card">
                    <div class="icon" style="background:linear-gradient(135deg,#11998e,#38ef7d);"><i class="bi bi-eye"></i></div>
                    <h4 class="fw-bold">Our Vision</h4>
                    <p class="text-muted">A future where every business owner has clear, real-time insight into their finances, operations and growth — directly from their phone.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
