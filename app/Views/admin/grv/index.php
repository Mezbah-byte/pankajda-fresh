<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Goods Return Vouchers</h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li>GRV</li>
        </ul>
    </div>
    <a href="<?= site_url('admin/grv/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>New GRV
    </a>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-6">
            <input type="text" class="form-control" name="q" placeholder="Search GRV number…" value="<?= esc($filters['q'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>GRV No</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th class="text-end">Amount</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($grvs ?? []) as $g): ?>
                    <tr>
                        <td>
                            <a href="<?= site_url('admin/grv/' . $g['un_id']) ?>" class="fw-semibold text-decoration-none" style="color:var(--mz-primary);"><?= esc($g['grv_no']) ?></a>
                        </td>
                        <td><?= esc($g['customer_name'] ?? '-') ?></td>
                        <td><?= esc($g['grv_date']) ?></td>
                        <td class="text-end">৳ <?= number_format((float) $g['total_amount'], 2) ?></td>
                        <td>
                            <?php $st = $g['status'] ?? 'draft'; ?>
                            <span class="badge-<?= $st === 'approved' ? 'success' : 'secondary' ?>-soft"><?= esc(ucfirst($st)) ?></span>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/grv/' . $g['un_id']) ?>" class="btn btn-sm btn-light" title="View"><i class="bi bi-eye"></i></a>
                            <form method="post" action="<?= site_url('admin/grv/' . $g['un_id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this GRV?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($grvs)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-arrow-return-left" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
                            <span class="text-muted">No GRVs yet.</span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (! empty($pagination) && $pagination['last_page'] > 1): ?>
        <nav class="d-flex justify-content-end mt-3">
            <ul class="pagination pagination-sm m-0">
                <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                    <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
