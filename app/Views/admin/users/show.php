<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($user['name'] ?? $user['email']) ?></h4>
        <ul class="mz-breadcrumb"><li>Settings</li><li><a href="<?= site_url('admin/users') ?>">Users</a></li><li><?= esc($user['email']) ?></li></ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/users/' . $user['un_id'] . '/edit') ?>" class="btn btn-light"><i class="bi bi-pencil me-1"></i>Edit</a>
    </div>
</div>

<?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>

<div class="row g-3">
    <div class="col-md-4">
        <div class="pd-card text-center">
            <div class="mx-auto mb-3" style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--mz-primary),#7c9dff);display:flex;align-items:center;justify-content:center;font-size:2rem;color:#fff;font-weight:700;">
                <?= strtoupper(mb_substr($user['name']??$user['email'], 0, 1)) ?>
            </div>
            <div class="fw-bold" style="font-size:1.1rem;"><?= esc($user['name'] ?? '-') ?></div>
            <div class="text-muted" style="font-size:.85rem;"><?= esc($user['email']) ?></div>
            <div class="mt-2">
                <span class="badge bg-<?= ($user['role']??'viewer')==='admin'?'danger':($user['role']??'')==='manager'?'warning':'secondary' ?>-subtle text-<?= ($user['role']??'viewer')==='admin'?'danger':($user['role']??'')==='manager'?'warning':'secondary' ?> me-1">
                    <?= ucfirst($user['role']??'viewer') ?>
                </span>
                <span class="badge bg-<?= ($user['status']??'active')==='active'?'success':'secondary' ?>-subtle text-<?= ($user['status']??'active')==='active'?'success':'secondary' ?>">
                    <?= ucfirst($user['status']??'active') ?>
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="pd-card">
            <h6 class="text-uppercase text-muted mb-3" style="font-size:.72rem;letter-spacing:.6px;">Account Details</h6>
            <table class="table table-borderless table-sm">
                <tr><td class="text-muted" style="width:35%">Email</td><td><?= esc($user['email']) ?></td></tr>
                <tr><td class="text-muted">Name</td><td><?= esc($user['name'] ?? '-') ?></td></tr>
                <tr><td class="text-muted">Role</td><td><?= ucfirst($user['role']??'-') ?></td></tr>
                <tr><td class="text-muted">Status</td><td><?= ucfirst($user['status']??'active') ?></td></tr>
                <tr><td class="text-muted">Last Login</td><td><?= $user['last_login_at'] ? esc(date('d M Y H:i', strtotime($user['last_login_at']))) : 'Never' ?></td></tr>
                <tr><td class="text-muted">Joined</td><td><?= esc(date('d M Y', strtotime($user['created_at']))) ?></td></tr>
                <tr><td class="text-muted">User ID</td><td class="text-muted" style="font-size:.78rem;"><?= esc($user['un_id']) ?></td></tr>
            </table>
            <div class="mt-3 d-flex gap-2">
                <a href="<?= site_url('admin/users/' . $user['un_id'] . '/edit') ?>" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit User</a>
                <?php if ($user['un_id'] !== session('user_un_id')): ?>
                    <form method="post" action="<?= site_url('admin/users/' . $user['un_id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this user?');">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash me-1"></i>Delete</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
