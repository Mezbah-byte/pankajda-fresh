<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $isEdit = ! empty($user); ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($title) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Settings</li>
            <li><a href="<?= site_url('admin/users') ?>" class="text-muted text-decoration-none">Users</a></li>
            <li><?= esc($title) ?></li>
        </ul>
    </div>
    <a href="<?= site_url('admin/users') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger mb-4">
        <ul class="mb-0"><?php foreach ((array) $errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= esc($action) ?>">
    <?= csrf_field() ?>
    <div class="pd-card">
        <h6 class="fw-bold mb-4" style="color:var(--mz-text-primary);">User Details</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" value="<?= esc(old('name', $user['name'] ?? '')) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" value="<?= esc(old('email', $user['email'] ?? '')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                    <?php foreach (['super_admin' => 'Super Admin', 'admin' => 'Admin', 'manager' => 'Manager', 'accountant' => 'Accountant', 'staff' => 'Staff'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= old('role', $user['role'] ?? 'staff') === $val ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($isEdit): ?>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= old('status', $user['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= old('status', $user['status'] ?? 'active') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            <?php endif; ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Phone</label>
                <input type="text" class="form-control" name="phone" value="<?= esc(old('phone', $user['phone'] ?? '')) ?>">
            </div>
        </div>

        <?php if (! $isEdit): ?>
            <hr class="my-4">
            <h6 class="fw-bold mb-3" style="color:var(--mz-text-primary);">Set Password</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="password" minlength="8" required autocomplete="new-password">
                    <div class="form-text">Minimum 8 characters.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="confirm_password" minlength="8" required autocomplete="new-password">
                </div>
            </div>
        <?php else: ?>
            <div class="mt-3 p-3" style="background:#F6F9FF;border-radius:8px;border:1px dashed #C5D4F5;">
                <i class="bi bi-shield-lock me-2 text-primary"></i>
                <span style="font-size:.875rem;color:var(--mz-text-primary);">To change the password, go to the user's <a href="<?= site_url('admin/users/' . ($user['un_id'] ?? '')) ?>" class="fw-semibold">profile page</a>.</span>
            </div>
        <?php endif; ?>

        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary"><i class="bi bi-check-circle me-2"></i><?= $isEdit ? 'Update User' : 'Create User' ?></button>
            <a href="<?= site_url('admin/users') ?>" class="btn btn-light">Cancel</a>
        </div>
    </div>
</form>

<?= $this->endSection() ?>
