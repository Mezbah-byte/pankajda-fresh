<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4>Notifications</h4>
        <ul class="mz-breadcrumb">
            <li>Insights</li>
            <li>Notifications</li>
        </ul>
    </div>
    <button class="btn btn-light" id="btnMarkAll">
        <i class="bi bi-check2-all me-1"></i>Mark all read
    </button>
</div>

<div class="pd-card p-0" style="overflow:hidden;">
    <?php if (empty($notifications)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:12px;"></i>
            No notifications yet.
        </div>
    <?php else: ?>
        <ul class="list-group list-group-flush" id="notifList">
        <?php foreach ($notifications as $n): ?>
            <li class="list-group-item d-flex align-items-start gap-3 py-3 px-4 <?= $n['read_at'] ? '' : '' ?>"
                id="ntf-<?= esc($n['un_id']) ?>"
                style="<?= ! $n['read_at'] ? 'background:#F9FAFB;border-left:3px solid #5D87FF;' : 'border-left:3px solid transparent;' ?>">
                <div class="flex-shrink-0 mt-1">
                    <?php if ($n['read_at']): ?>
                        <div style="width:8px;height:8px;border-radius:50%;background:#E5EAF2;margin-top:4px;"></div>
                    <?php else: ?>
                        <div style="width:8px;height:8px;border-radius:50%;background:#5D87FF;margin-top:4px;"></div>
                    <?php endif; ?>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold" style="font-size:.875rem;"><?= esc($n['title']) ?></div>
                    <?php if ($n['body']): ?>
                        <div class="text-muted" style="font-size:.82rem;margin-top:2px;"><?= esc($n['body']) ?></div>
                    <?php endif; ?>
                    <div class="text-muted mt-1" style="font-size:.72rem;">
                        <span class="badge-secondary-soft"><?= esc($n['type']) ?></span>
                        &middot; <?= esc(date('d M Y H:i', strtotime($n['created_at']))) ?>
                    </div>
                </div>
                <div class="flex-shrink-0 d-flex gap-1">
                    <?php if ($n['link']): ?>
                        <a href="<?= esc($n['link']) ?>" class="btn btn-sm btn-light" title="View">
                            <i class="bi bi-arrow-right-circle"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (! $n['read_at']): ?>
                        <button class="btn btn-sm btn-light btn-mark-read" data-id="<?= esc($n['un_id']) ?>" title="Mark read">
                            <i class="bi bi-check2"></i>
                        </button>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-light text-danger btn-dismiss" data-id="<?= esc($n['un_id']) ?>" title="Dismiss">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>

        <?php if ($pagination['last_page'] > 1): ?>
            <div class="d-flex justify-content-center py-3 border-top" style="border-color:var(--mz-border)!important;">
                <ul class="pagination pagination-sm mb-0">
                    <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                        <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.btn-mark-read').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        fetch(`<?= site_url('admin/notifications') ?>/${id}/read`, {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest',
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': '<?= csrf_hash() ?>'}
        }).then(() => {
            const li = document.getElementById('ntf-' + id);
            li.style.background = '';
            li.style.borderLeft = '3px solid transparent';
            btn.remove();
            const dot = li.querySelector('div[style*="5D87FF"]');
            if (dot) dot.style.background = '#E5EAF2';
        });
    });
});

document.querySelectorAll('.btn-dismiss').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        fetch(`<?= site_url('admin/notifications') ?>/${id}/dismiss`, {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest',
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': '<?= csrf_hash() ?>'}
        }).then(() => {
            document.getElementById('ntf-' + id)?.remove();
        });
    });
});

document.getElementById('btnMarkAll')?.addEventListener('click', () => {
    fetch('<?= site_url('admin/notifications/read-all') ?>', {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest',
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '<?= csrf_hash() ?>'}
    }).then(() => location.reload());
});
</script>
<?= $this->endSection() ?>
