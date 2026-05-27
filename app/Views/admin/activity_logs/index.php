<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Activity Log</h4>
        <ul class="mz-breadcrumb"><li>Settings</li><li>Activity Log</li></ul>
    </div>
</div>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-3"><input type="text" class="form-control" name="action" placeholder="Filter action…" value="<?= esc($filters['action']??'') ?>"></div>
        <div class="col-md-3">
            <select name="user_un_id" class="form-select">
                <option value="">All Users</option>
                <?php foreach (($users ?? []) as $u): ?>
                    <option value="<?= esc($u['un_id']) ?>" <?= ($filters['user_un_id']??'')===$u['un_id']?'selected':'' ?>><?= esc($u['name'] ?? $u['email'] ?? $u['un_id']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><input type="date" class="form-control" name="date_from" value="<?= esc($filters['date_from']??'') ?>" placeholder="From"></div>
        <div class="col-md-2"><input type="date" class="form-control" name="date_to" value="<?= esc($filters['date_to']??'') ?>" placeholder="To"></div>
        <div class="col-md-2"><button class="btn btn-light w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
    </form>

    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr><th>Time</th><th>User</th><th>Action</th><th>Entity</th><th>ID</th><th>IP</th></tr>
            </thead>
            <tbody>
                <?php foreach (($logs ?? []) as $log): ?>
                    <tr>
                        <td class="text-muted" style="font-size:.78rem;white-space:nowrap;"><?= esc(date('d M Y H:i', strtotime($log['created_at']))) ?></td>
                        <td style="font-size:.82rem;"><?= esc($log['user_un_id'] ? mb_strimwidth($log['user_un_id'],0,16,'…') : 'System') ?></td>
                        <td>
                            <code style="font-size:.78rem;background:var(--mz-bg);padding:2px 6px;border-radius:4px;"><?= esc($log['action']) ?></code>
                        </td>
                        <td class="text-muted" style="font-size:.82rem;"><?= esc($log['entity_type'] ?? '-') ?></td>
                        <td class="text-muted" style="font-size:.75rem;"><?= esc(mb_strimwidth($log['entity_un_id']??'', 0, 12, '…')) ?></td>
                        <td class="text-muted" style="font-size:.75rem;"><?= esc($log['ip_address'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-journal-text" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>No activity logs.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
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
