<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="pill"><i class="bi bi-stars"></i> Modern ERP for Modern Business</span>
                <h1>Run your <span style="background:linear-gradient(135deg,#5e60ce,#2ec4b6);-webkit-background-clip:text;background-clip:text;color:transparent;">entire business</span> from one place</h1>
                <p class="lead mt-3">Pankaj Da ERP unifies visa, import, trading, farm projects and accounts into a single, beautiful dashboard. Built for growing businesses.</p>
                <div class="mt-4 d-flex gap-3 flex-wrap">
                    <a href="<?= site_url('login') ?>" class="btn-pd-primary"><i class="bi bi-rocket me-2"></i>Get Started</a>
                    <a href="<?= site_url('services') ?>" class="btn btn-light btn-lg" style="border-radius:10px;font-weight:600;">View Services</a>
                </div>
                <div class="d-flex gap-4 mt-4 small text-muted">
                    <span><i class="bi bi-check2-circle text-success"></i> Multi-Company</span>
                    <span><i class="bi bi-check2-circle text-success"></i> Real-time Reports</span>
                    <span><i class="bi bi-check2-circle text-success"></i> Mobile Friendly</span>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div style="background:#fff;border-radius:18px;box-shadow:0 24px 60px rgba(33,41,70,.12);padding:24px;transform:rotate(-1.5deg);">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="bg-danger rounded-circle d-inline-block" style="width:10px;height:10px"></span>
                        <span class="bg-warning rounded-circle d-inline-block" style="width:10px;height:10px"></span>
                        <span class="bg-success rounded-circle d-inline-block" style="width:10px;height:10px"></span>
                    </div>
                    <div class="row g-3">
                        <div class="col-6"><div style="background:linear-gradient(135deg,#6a11cb,#2575fc);color:#fff;padding:20px;border-radius:12px;"><div class="small opacity-75">Total Sales</div><div class="h3 mb-0 fw-bold">৳ 12.4L</div></div></div>
                        <div class="col-6"><div style="background:linear-gradient(135deg,#11998e,#38ef7d);color:#fff;padding:20px;border-radius:12px;"><div class="small opacity-75">Active Visas</div><div class="h3 mb-0 fw-bold">128</div></div></div>
                        <div class="col-6"><div style="background:linear-gradient(135deg,#f7971e,#ffd200);color:#fff;padding:20px;border-radius:12px;"><div class="small opacity-75">Containers</div><div class="h3 mb-0 fw-bold">42</div></div></div>
                        <div class="col-6"><div style="background:linear-gradient(135deg,#ee0979,#ff6a00);color:#fff;padding:20px;border-radius:12px;"><div class="small opacity-75">Customers</div><div class="h3 mb-0 fw-bold">316</div></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Everything your business needs</h2>
            <p class="section-sub">A complete suite of tools designed for visa agencies, importers, traders and farm operators.</p>
        </div>
        <div class="row g-4">
            <?php
            $features = [
                ['icon' => 'bi-buildings', 'title' => 'Multi-Company', 'desc' => 'Manage multiple companies from a single dashboard with separate reports.'],
                ['icon' => 'bi-passport', 'title' => 'Visa Management', 'desc' => 'Track visa cost, payments, dues and expiry dates with full history.'],
                ['icon' => 'bi-box-seam', 'title' => 'Container Imports', 'desc' => 'Container → custom → damage → sales lifecycle, all in one place.'],
                ['icon' => 'bi-cart-check', 'title' => 'Cash & Credit Sales', 'desc' => 'Generate invoices, track dues and collect payments effortlessly.'],
                ['icon' => 'bi-tree', 'title' => 'Farm Projects', 'desc' => 'Workers, seeds, costs and profits — full farming project lifecycle.'],
                ['icon' => 'bi-graph-up', 'title' => 'Reports & Analytics', 'desc' => 'Daily, monthly and company-wise reports with PDF/Excel export.'],
            ];
            foreach ($features as $f): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon"><i class="bi <?= $f['icon'] ?>"></i></div>
                        <h5 class="fw-bold"><?= esc($f['title']) ?></h5>
                        <p class="text-muted mb-0"><?= esc($f['desc']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5" style="background:linear-gradient(135deg,#5e60ce,#6930c3);color:#fff;border-radius:24px;margin:0 24px;">
    <div class="container text-center py-3">
        <h2 class="fw-bold mb-3">Ready to transform your business?</h2>
        <p class="opacity-75 mb-4">Join growing businesses already running on Pankaj Da ERP.</p>
        <a href="<?= site_url('login') ?>" class="btn btn-light btn-lg" style="border-radius:10px;font-weight:600;color:#5e60ce;">Get Started Free</a>
    </div>
</section>

<?= $this->endSection() ?>
