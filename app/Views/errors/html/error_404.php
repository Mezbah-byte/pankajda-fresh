<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Page not found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh; display:flex; align-items:center; background:#f7f8fc;
            font-family:'Inter',-apple-system,sans-serif;
        }
        .display-1 { color:#5e60ce; font-weight:800; font-size:8rem; line-height:1; }
        .small-debug { font-family: monospace; font-size: .8rem; color:#666; background:#fff; padding:12px; border-radius:8px; max-width:800px; margin: 16px auto; text-align:left; white-space:pre-wrap; }
    </style>
</head>
<body>
<div class="container text-center">
    <div class="display-1">404</div>
    <h3 class="mb-3">Page not found</h3>
    <p class="text-muted">The page you're looking for doesn't exist or has been moved.</p>
    <?php if (ENVIRONMENT !== 'production' && isset($message) && $message !== ''): ?>
        <div class="small-debug"><?= esc($message) ?></div>
    <?php endif; ?>
    <a href="/" class="btn btn-primary mt-3">Back to home</a>
</div>
</body>
</html>
