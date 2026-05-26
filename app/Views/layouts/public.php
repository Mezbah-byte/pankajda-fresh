<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= esc($description ?? 'Pankaj Da Business Management System - Modern ERP for visa, import, trading and farm businesses.') ?>">
    <title><?= esc($title ?? 'Pankaj Da Business') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --pd-primary: #5e60ce;
            --pd-primary-2: #6930c3;
            --pd-accent: #2ec4b6;
            --pd-dark: #1f1d3a;
        }
        * { font-family: 'Inter', -apple-system, sans-serif; }
        body { color: #2b2740; }
        .navbar { background: rgba(255,255,255,.96); backdrop-filter: blur(8px); padding: 14px 0; box-shadow: 0 1px 0 rgba(0,0,0,.04); }
        .navbar-brand { font-weight: 800; font-size: 1.25rem; color: var(--pd-dark) !important; }
        .navbar-brand i { color: var(--pd-accent); }
        .nav-link { font-weight: 500; color: #4d4d6f !important; padding: .5rem 1rem !important; }
        .nav-link:hover, .nav-link.active { color: var(--pd-primary) !important; }
        .btn-pd-primary {
            background: linear-gradient(135deg, var(--pd-primary), var(--pd-primary-2));
            color: #fff; border: 0; padding: .65rem 1.5rem; border-radius: 10px; font-weight: 600;
            transition: transform .15s, box-shadow .15s;
        }
        .btn-pd-primary:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 10px 22px rgba(94,96,206,.35); }
        .hero {
            position: relative; padding: 110px 0 90px;
            background:
                radial-gradient(circle at 12% 20%, rgba(94,96,206,.18), transparent 40%),
                radial-gradient(circle at 90% 70%, rgba(46,196,182,.16), transparent 40%),
                #fbfbff;
            overflow: hidden;
        }
        .hero h1 { font-size: 3.2rem; font-weight: 800; color: var(--pd-dark); line-height: 1.15; }
        .hero p.lead { font-size: 1.15rem; color: #5b5980; max-width: 540px; }
        .pill {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 6px 14px; background: rgba(94,96,206,.1);
            color: var(--pd-primary); font-weight: 600; font-size: .82rem;
            border-radius: 999px; margin-bottom: 18px;
        }
        .feature-card {
            background: #fff; border-radius: 16px; padding: 28px;
            box-shadow: 0 6px 30px rgba(33,41,70,.07); height: 100%;
            border: 1px solid #f0f0f5; transition: transform .2s, box-shadow .2s;
        }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 14px 40px rgba(33,41,70,.1); }
        .feature-card .icon {
            width: 56px; height: 56px; border-radius: 14px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.6rem; color: #fff; margin-bottom: 18px;
            background: linear-gradient(135deg, var(--pd-primary), var(--pd-primary-2));
        }
        .section-title { font-size: 2.2rem; font-weight: 800; color: var(--pd-dark); }
        .section-sub { color: #6e6c8e; max-width: 620px; margin: 0 auto 40px; }
        footer { background: var(--pd-dark); color: #b8b6d6; padding: 50px 0 28px; margin-top: 80px; }
        footer h6 { color: #fff; font-weight: 700; margin-bottom: 14px; }
        footer a { color: #b8b6d6; text-decoration: none; display: block; padding: 4px 0; font-size: .9rem; }
        footer a:hover { color: #fff; }
        footer .copyright { border-top: 1px solid rgba(255,255,255,.08); margin-top: 30px; padding-top: 20px; font-size: .85rem; }
    </style>
    <?= $this->renderSection('head') ?>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url() ?>"><i class="bi bi-gem me-2"></i>Pankaj Da</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <i class="bi bi-list fs-3"></i>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link <?= url_is('/') ? 'active' : '' ?>" href="<?= site_url() ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link <?= url_is('about') ? 'active' : '' ?>" href="<?= site_url('about') ?>">About</a></li>
                <li class="nav-item"><a class="nav-link <?= url_is('services') ? 'active' : '' ?>" href="<?= site_url('services') ?>">Services</a></li>
                <li class="nav-item"><a class="nav-link <?= url_is('companies') ? 'active' : '' ?>" href="<?= site_url('companies') ?>">Companies</a></li>
                <li class="nav-item"><a class="nav-link <?= url_is('contact') ? 'active' : '' ?>" href="<?= site_url('contact') ?>">Contact</a></li>
            </ul>
            <div class="d-flex gap-2">
                <a href="<?= site_url('login') ?>" class="btn btn-link text-decoration-none">Login</a>
                <a href="<?= site_url('login') ?>" class="btn btn-pd-primary">Get Started</a>
            </div>
        </div>
    </div>
</nav>

<?= $this->renderSection('content') ?>

<footer>
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h6 class="text-white"><i class="bi bi-gem me-2 text-info"></i>Pankaj Da Business</h6>
                <p class="small mt-3" style="color:#9d9bbf;">Modern ERP solution for visa, import-export, trading and farm businesses.</p>
            </div>
            <div class="col-6 col-md-2"><h6>Company</h6><a href="<?= site_url('about') ?>">About</a><a href="<?= site_url('services') ?>">Services</a><a href="<?= site_url('contact') ?>">Contact</a></div>
            <div class="col-6 col-md-2"><h6>Solutions</h6><a href="#">Visa</a><a href="#">Import</a><a href="#">Trading</a><a href="#">Farm</a></div>
            <div class="col-md-4"><h6>Get in touch</h6><p class="small"><i class="bi bi-geo-alt me-2"></i>Dhaka, Bangladesh<br><i class="bi bi-envelope me-2"></i>info@pankajda.example</p></div>
        </div>
        <div class="copyright text-center">&copy; <?= date('Y') ?> Pankaj Da Business. All rights reserved.</div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
