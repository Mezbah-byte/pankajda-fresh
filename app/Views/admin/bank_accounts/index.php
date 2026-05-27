<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Bank Accounts</h4>
        <ul class="mz-breadcrumb"><li>Finance</li><li>Bank Accounts</li></ul>
    </div>
    <a href="<?= site_url('admin/bank-accounts/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>New Account</a>
</div>

<?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-5"><input type="text" class="form-control" name="q" placeholder="Search account…" value="<?= esc($filters['q'] ?? '') ?>"></div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="active" <?= ($filters['status']??'')==='active'?'selected':'' ?>>Active</option>
                <option value="inactive" <?= ($filters['status']??'')==='inactive'?'selected':'' ?>>Inactive</option>
            </select>
        </div>
        <div class="col-md-4"><button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
    </form>

    <div class="row g-3 mb-4">
        <?php foreach (($accounts ?? []) as $acc): ?>
            <div class="col-md-4">
                <div class="pd-card border" style="border:1px solid var(--mz-border)!important;">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <div class="fw-bold" style="font-size:1rem;"><?= esc($acc['account_name']) ?></div>
                            <div class="text-muted" style="font-size:.8rem;"><?= esc($acc['bank_name']) ?></div>
                        </div>
                        <span class="badge bg-<?= ($acc['status']??'active')==='active'?'success':'secondary' ?>-subtle text-<?= ($acc['status']??'active')==='active'?'success':'secondary' ?>">
                            <?= ucfirst($acc['status']??'active') ?>
                        </span>
                    </div>
                    <div class="text-muted" style="font-size:.78rem;margin-bottom:4px;">A/C: <?= esc($acc['account_number']) ?></div>
                    <?php if (!empty($acc['branch'])): ?><div class="text-muted" style="font-size:.78rem;margin-bottom:4px;">Branch: <?= esc($acc['branch']) ?></div><?php endif; ?>
                    <div class="fw-bold mt-2" style="font-size:1.3rem;color:var(--mz-primary);">৳ <?= number_format((float)($acc['balance']??0), 2) ?></div>
                    <div class="d-flex gap-1 mt-3">
                        <a href="<?= site_url('admin/bank-accounts/' . $acc['un_id']) ?>" class="btn btn-sm btn-light flex-fill"><i class="bi bi-eye me-1"></i>View</a>
                        <a href="<?= site_url('admin/bank-accounts/' . $acc['un_id'] . '/edit') ?>" class="btn btn-sm btn-light flex-fill"><i class="bi bi-pencil me-1"></i>Edit</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($accounts)): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-bank" style="font-size:3rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
                <span class="text-muted">No bank accounts configured.</span>
            </div>
        <?php endif; ?>
    </div>

    <?php if (! empty($pagination) && $pagination['last_page'] > 1): ?>
        <nav class="d-flex justify-content-end mt-3">
            <ul class="pagination pagination-sm m-0">
                <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                    <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a></li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
