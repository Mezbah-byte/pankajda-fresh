<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<section class="hero" style="padding:80px 0 50px;">
    <div class="container text-center">
        <span class="pill">Get in touch</span>
        <h1>We'd love to hear from you</h1>
        <p class="lead mt-3 mx-auto">Reach out for partnerships, demos, or any questions about our services.</p>
    </div>
</section>
<section class="py-5">
    <div class="container" style="max-width:780px;">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <div class="feature-card p-5">
            <form method="post" action="<?= site_url('contact') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Your name</label>
                        <input type="text" class="form-control form-control-lg" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control form-control-lg" name="email" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Subject</label>
                        <input type="text" class="form-control form-control-lg" name="subject">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Message</label>
                        <textarea class="form-control" name="message" rows="5" required></textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn-pd-primary"><i class="bi bi-send me-2"></i>Send message</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
