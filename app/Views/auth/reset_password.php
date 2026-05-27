<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Pankaj Da ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0; padding: 0;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: #F2F6FA;
        }
        .card {
            background: #fff; border-radius: 20px;
            box-shadow: 0 2px 24px rgba(93,135,255,.1);
            padding: 48px 44px; width: 100%; max-width: 420px;
        }
        .icon-wrap {
            width: 64px; height: 64px;
            background: linear-gradient(135deg,#5D87FF,#7c9dff);
            border-radius: 16px; display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; color: #fff; margin: 0 auto 24px;
        }
        h2 { font-size: 1.5rem; font-weight: 800; color: #2A3547; margin-bottom: 6px; text-align: center; }
        p { color: #5A6A85; font-size: .875rem; text-align: center; margin-bottom: 28px; }
        label { display: block; font-size: .82rem; font-weight: 600; color: #2A3547; margin-bottom: 6px; }
        input[type=password] {
            width: 100%; border: 1px solid #E5EAF2; border-radius: 8px;
            padding: 11px 14px; font-size: .875rem; color: #2A3547;
            font-family: 'Inter', sans-serif; outline: none;
            transition: border-color .15s, box-shadow .15s; background: #fff;
        }
        input[type=password]:focus { border-color: #5D87FF; box-shadow: 0 0 0 3px rgba(93,135,255,.15); }
        .mb { margin-bottom: 20px; }
        .hint { font-size:.75rem;color:#5A6A85;margin-top:4px; }
        .btn {
            width: 100%; background: #5D87FF; color: #fff; border: 0; border-radius: 8px;
            padding: 13px; font-size: .95rem; font-weight: 600;
            font-family: 'Inter', sans-serif; cursor: pointer;
            transition: background .15s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 20px;
        }
        .btn:hover { background: #4a70e8; }
        .alert-danger { background: #FDECEA; color: #e85347; border-radius: 8px; padding: 12px 16px; font-size: .82rem; margin-bottom: 20px; }
        .back-link { display: block; text-align: center; margin-top: 24px; font-size: .82rem; color: #5A6A85; text-decoration: none; }
        .back-link:hover { color: #5D87FF; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon-wrap"><i class="bi bi-key"></i></div>
    <h2>Set New Password</h2>
    <p>Choose a strong password for your account.</p>

    <?php if (session('errors')): ?>
        <div class="alert-danger"><ul style="margin:0;padding-left:16px;"><?php foreach (session('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('reset-password') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= esc($token) ?>">
        <input type="hidden" name="email" value="<?= esc($email) ?>">
        <div class="mb">
            <label>New Password</label>
            <input type="password" name="password" placeholder="Min 8 characters" required minlength="8" autocomplete="new-password">
            <div class="hint">Minimum 8 characters.</div>
        </div>
        <div class="mb">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" placeholder="Repeat password" required autocomplete="new-password">
        </div>
        <button type="submit" class="btn"><i class="bi bi-check-circle"></i>Reset Password</button>
    </form>

    <a href="<?= site_url('login') ?>" class="back-link"><i class="bi bi-arrow-left me-1"></i>Back to Login</a>
</div>
</body>
</html>
