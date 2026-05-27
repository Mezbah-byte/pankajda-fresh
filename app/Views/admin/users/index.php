<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Users</h4>
        <ul class="mz-breadcrumb">
            <li>Settings</li>
            <li>Users</li>
        </ul>
    </div>
    <a href="<?= site_url('admin/users/create') ?>" class="btn btn-primary"><i class="bi bi-person-plus me-2"></i>Add User</a>
</div>

<?php if ($success = session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-3"><?= esc($success) ?></div>
<?php endif; ?>
<?php if ($error = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-3"><?= esc($error) ?></div>
<?php endif; ?>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" class="form-control" name="q" placeholder="Search name or email…" value="<?= esc($filters['q'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <select name="role" class="form-select">
                <option value="">All roles</option>
                <?php foreach (['super_admin', 'admin', 'manager', 'accountant', 'staff'] as $r): ?>
                    <option value="<?= $r ?>" <?= ($filters['role'] ?? '') === $r ? 'selected' : '' ?>><?= esc(ucfirst(str_replace('_', ' ', $r))) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
        <?php if (! empty($filters['q']) || ! empty($filters['role']) || ! empty($filters['status'])): ?>
            <div class="col-md-2">
                <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-secondary w-100">Clear</a>
            </div>
        <?php endif; ?>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($users ?? []) as $u): ?>
                    <?php
                        $roleColors = [
                            'super_admin' => 'badge-danger-soft',
                            'admin'       => 'badge-warning-soft',
                            'manager'     => 'badge-primary-soft',
                            'accountant'  => 'badge-info-soft',
                            'staff'       => 'badge-secondary-soft',
                        ];
                        $roleColor = $roleColors[$u['role']] ?? 'badge-secondary-soft';
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#5D87FF,#ECEDEE);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;color:#fff;flex-shrink:0;">
                                    <?= esc(strtoupper(mb_substr($u['name'] ?? 'U', 0, 1))) ?>
                                </div>
                                <span class="fw-semibold"><?= esc($u['name']) ?></span>
                            </div>
                        </td>
                        <td class="text-muted" style="font-size:.875rem;"><?= esc($u['email']) ?></td>
                        <td><span class="<?= $roleColor ?>"><?= esc(ucfirst(str_replace('_', ' ', $u['role'] ?? 'staff'))) ?></span></td>
                        <td>
                            <?php if (($u['status'] ?? 'active') === 'active'): ?>
                                <span class="badge-success-soft">Active</span>
                            <?php else: ?>
                                <span class="badge-danger-soft">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted" style="font-size:.82rem;">
                            <?= ! empty($u['last_login_at']) ? esc($u['last_login_at']) : '—' ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/users/' . $u['un_id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                            <a href="<?= site_url('admin/users/' . $u['un_id'] . '/edit') ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= site_url('admin/users/' . $u['un_id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this user?');">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-people" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
                            <span class="text-muted">No users found.</span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (! empty($pagination) && $pagination['last_page'] > 1): ?>
        <nav class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Showing page <?= $pagination['page'] ?> of <?= $pagination['last_page'] ?> (<?= number_format($pagination['total']) ?> total)</small>
            <ul class="pagination pagination-sm m-0">
                <?php for ($p = max(1, $pagination['page'] - 3); $p <= min($pagination['last_page'], $pagination['page'] + 3); $p++): ?>
                    <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $p])) ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
