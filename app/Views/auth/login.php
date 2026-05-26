<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pankaj Da ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh; display: flex; align-items: center;
            background:
                radial-gradient(circle at 15% 25%, rgba(94,96,206,.18), transparent 40%),
                radial-gradient(circle at 85% 75%, rgba(46,196,182,.18), transparent 40%),
                #f7f8fc;
            font-family: 'Inter', -apple-system, sans-serif;
        }
        .login-card {
            background: #fff; border-radius: 20px;
            box-shadow: 0 24px 60px rgba(33,41,70,.1);
            padding: 44px; max-width: 440px; width: 100%;
            margin: 24px auto;
        }
        .brand { font-weight: 800; font-size: 1.4rem; color: #1f1d3a; }
        .brand i { color: #2ec4b6; }
        .form-control-lg { border-radius: 10px; padding: .75rem 1rem; }
        .btn-primary {
            background: linear-gradient(135deg, #5e60ce, #6930c3);
            border: 0; padding: .8rem; border-radius: 10px; font-weight: 600;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand mb-2"><i class="bi bi-gem me-2"></i>Pankaj Da ERP</div>
            <p class="text-muted small mb-0">Sign in to your account</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger small"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('login') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control form-control-lg" name="email" required autofocus value="<?= esc(old('email', 'admin@pankajda.example')) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" class="form-control form-control-lg" name="password" required>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label small" for="remember">Remember me</label>
                </div>
                <a href="#" class="small">Forgot password?</a>
            </div>
            <button class="btn btn-primary w-100">Sign in</button>
        </form>

        <div class="text-center mt-4 small text-muted">
            <a href="<?= site_url() ?>" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Back to home</a>
        </div>
    </div>
</div>
</body>
</html>
