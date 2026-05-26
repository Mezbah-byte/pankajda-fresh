<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pankaj Da ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0; padding: 0;
            min-height: 100vh;
            display: flex;
            background: #F2F6FA;
        }

        /* ── Left branded panel ── */
        .lp-left {
            width: 50%;
            background: linear-gradient(145deg, #2A3547 0%, #3a4a6b 40%, #5D87FF 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 56px;
            position: relative;
            overflow: hidden;
        }
        .lp-left::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: rgba(93,135,255,.15);
            top: -100px; right: -100px;
        }
        .lp-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(73,190,255,.1);
            bottom: -80px; left: -60px;
        }
        .lp-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 56px;
            position: relative; z-index: 1;
        }
        .lp-brand-icon {
            width: 46px; height: 46px;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.3rem;
        }
        .lp-brand-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
        }
        .lp-tagline {
            color: rgba(255,255,255,.6);
            font-size: .82rem;
        }
        .lp-headline {
            font-size: 2.25rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.25;
            margin-bottom: 16px;
            position: relative; z-index: 1;
        }
        .lp-subline {
            color: rgba(255,255,255,.65);
            font-size: .95rem;
            line-height: 1.6;
            margin-bottom: 40px;
            position: relative; z-index: 1;
        }
        .lp-features {
            list-style: none;
            padding: 0; margin: 0;
            position: relative; z-index: 1;
        }
        .lp-features li {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,.8);
            font-size: .875rem;
            margin-bottom: 14px;
        }
        .lp-features li .feat-icon {
            width: 30px; height: 30px;
            border-radius: 8px;
            background: rgba(255,255,255,.12);
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem;
            flex-shrink: 0;
        }
        .lp-decor {
            position: absolute;
            bottom: 40px; right: 56px;
            opacity: .06;
            font-size: 9rem;
            z-index: 0;
            pointer-events: none;
        }

        /* ── Right form panel ── */
        .lp-right {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 32px;
            background: #F2F6FA;
        }
        .lp-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 2px 24px rgba(93,135,255,.1);
            padding: 48px 44px;
            width: 100%;
            max-width: 440px;
        }
        .lp-card-header {
            margin-bottom: 36px;
        }
        .lp-card-header h2 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #2A3547;
            margin-bottom: 6px;
        }
        .lp-card-header p {
            color: #5A6A85;
            font-size: .875rem;
            margin: 0;
        }

        .lp-label {
            display: block;
            font-size: .82rem;
            font-weight: 600;
            color: #2A3547;
            margin-bottom: 6px;
        }
        .lp-input {
            width: 100%;
            border: 1px solid #E5EAF2;
            border-radius: 8px;
            padding: 11px 14px;
            font-size: .875rem;
            color: #2A3547;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            background: #fff;
        }
        .lp-input:focus {
            border-color: #5D87FF;
            box-shadow: 0 0 0 3px rgba(93,135,255,.15);
        }
        .lp-input::placeholder { color: #aab4c4; }

        .lp-btn {
            width: 100%;
            background: #5D87FF;
            color: #fff;
            border: 0;
            border-radius: 8px;
            padding: 13px;
            font-size: .95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: background .15s, transform .1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .lp-btn:hover { background: #4a70e8; transform: translateY(-1px); }
        .lp-btn:active { transform: translateY(0); }

        .lp-alert {
            background: #FDECEA;
            color: #e85347;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: .82rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .lp-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 28px 0 20px;
        }
        .lp-divider::before, .lp-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E5EAF2;
        }
        .lp-divider span { font-size: .75rem; color: #5A6A85; white-space: nowrap; }

        .lp-back-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            font-size: .82rem;
            color: #5A6A85;
            text-decoration: none;
            transition: color .15s;
        }
        .lp-back-link:hover { color: #5D87FF; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .lp-left { display: none; }
            .lp-right { width: 100%; }
        }
        @media (max-width: 480px) {
            .lp-right { padding: 24px 16px; }
            .lp-card { padding: 32px 24px; border-radius: 16px; }
        }
    </style>
</head>
<body>

    <!-- Left branded panel -->
    <div class="lp-left">
        <div class="lp-brand">
            <div class="lp-brand-icon"><i class="bi bi-gem"></i></div>
            <div>
                <div class="lp-brand-name">Pankaj Da ERP</div>
                <div class="lp-tagline">Business Management System</div>
            </div>
        </div>

        <h1 class="lp-headline">Manage your<br>business smarter</h1>
        <p class="lp-subline">
            One platform for companies, visas, containers, customers,
            sales, employees, farm projects, expenses and reports.
        </p>

        <ul class="lp-features">
            <li>
                <div class="feat-icon"><i class="bi bi-buildings"></i></div>
                <span>Multi-company management</span>
            </li>
            <li>
                <div class="feat-icon"><i class="bi bi-cart-check"></i></div>
                <span>Sales & invoicing with payment tracking</span>
            </li>
            <li>
                <div class="feat-icon"><i class="bi bi-graph-up"></i></div>
                <span>Real-time profit & loss reports</span>
            </li>
            <li>
                <div class="feat-icon"><i class="bi bi-shield-check"></i></div>
                <span>Secure & role-based access</span>
            </li>
        </ul>

        <i class="bi bi-gem lp-decor"></i>
    </div>

    <!-- Right form panel -->
    <div class="lp-right">
        <div class="lp-card">
            <div class="lp-card-header">
                <h2>Welcome back!</h2>
                <p>Sign in to your ERP account to continue.</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="lp-alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('login') ?>">
                <?= csrf_field() ?>

                <div style="margin-bottom:18px;">
                    <label class="lp-label" for="email">Email address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="lp-input"
                        placeholder="admin@example.com"
                        required
                        autofocus
                        value="<?= esc(old('email', 'admin@pankajda.example')) ?>"
                    >
                </div>

                <div style="margin-bottom:20px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <label class="lp-label" for="password" style="margin:0;">Password</label>
                        <a href="#" style="font-size:.78rem;color:#5D87FF;text-decoration:none;">Forgot password?</a>
                    </div>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="lp-input"
                        placeholder="••••••••"
                        required
                    >
                </div>

                <div style="display:flex;align-items:center;gap:8px;margin-bottom:28px;">
                    <input type="checkbox" id="remember" name="remember" style="width:15px;height:15px;accent-color:#5D87FF;cursor:pointer;">
                    <label for="remember" style="font-size:.82rem;color:#5A6A85;cursor:pointer;margin:0;">Remember me for 30 days</label>
                </div>

                <button type="submit" class="lp-btn">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Sign in
                </button>
            </form>

            <a href="<?= site_url() ?>" class="lp-back-link">
                <i class="bi bi-arrow-left me-1"></i>Back to website
            </a>
        </div>
    </div>

</body>
</html>
