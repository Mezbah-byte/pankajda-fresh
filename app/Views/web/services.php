<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<section class="hero" style="padding:80px 0 50px;">
    <div class="container text-center">
        <span class="pill">What we offer</span>
        <h1>Services tailored for your industry</h1>
        <p class="lead mt-3 mx-auto">From visa processing to farm project tracking, we cover the complete lifecycle of your business operations.</p>
    </div>
</section>
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php
            $services = [
                ['i'=>'bi-passport','c'=>'#6a11cb','t'=>'Visa Services','d'=>'End-to-end visa application, cost tracking, payment management and expiry monitoring.'],
                ['i'=>'bi-truck','c'=>'#11998e','t'=>'Import & Export','d'=>'Container tracking from arrival through customs, damage assessment and sales distribution.'],
                ['i'=>'bi-shop','c'=>'#f7971e','t'=>'Vegetable & Fruit Trading','d'=>'Wholesale trading with cash and credit sales, customer due tracking, and invoice generation.'],
                ['i'=>'bi-tree','c'=>'#ee0979','t'=>'Farm Project Management','d'=>'Plan, execute and track farm projects with workers, seeds, costs and profit calculations.'],
                ['i'=>'bi-cash-stack','c'=>'#00c6ff','t'=>'Accounts & Expense','d'=>'Office expenses, categorization, monthly reports and complete financial visibility.'],
                ['i'=>'bi-graph-up-arrow','c'=>'#ff5858','t'=>'Reports & Analytics','d'=>'Daily, monthly, company-wise reports with PDF/Excel export and printable invoices.'],
            ];
            foreach ($services as $s): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon" style="background:linear-gradient(135deg, <?= $s['c'] ?>, <?= $s['c'] ?>aa);"><i class="bi <?= $s['i'] ?>"></i></div>
                        <h5 class="fw-bold"><?= esc($s['t']) ?></h5>
                        <p class="text-muted mb-0"><?= esc($s['d']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
