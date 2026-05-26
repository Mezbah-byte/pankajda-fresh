<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Notifications</h4>
        <p class="text-muted small m-0"><?= number_format($pagination['total']) ?> total</p>
    </div>
    <button class="btn btn-outline-secondary btn-sm" id="btnMarkAll">
        <i class="bi bi-check2-all me-1"></i>Mark all read
    </button>
</div>

<div class="pd-card p-0">
    <?php if (empty($notifications)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash fs-2 d-block mb-2"></i>
            No notifications yet.
        </div>
    <?php else: ?>
        <ul class="list-group list-group-flush" id="notifList">
        <?php foreach ($notifications as $n): ?>
            <li class="list-group-item d-flex align-items-start gap-3 py-3 <?= $n['read_at'] ? '' : 'bg-light' ?>" id="ntf-<?= esc($n['un_id']) ?>">
                <div class="flex-shrink-0 mt-1">
                    <?php if ($n['read_at']): ?>
                        <i class="bi bi-circle text-muted"></i>
                    <?php else: ?>
                        <i class="bi bi-circle-fill text-primary" style="font-size:.6rem;"></i>
                    <?php endif; ?>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold"><?= esc($n['title']) ?></div>
                    <?php if ($n['body']): ?>
                        <div class="text-muted small"><?= esc($n['body']) ?></div>
                    <?php endif; ?>
                    <div class="text-muted" style="font-size:.75rem;">
                        <?= esc($n['type']) ?>
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
            <div class="d-flex justify-content-center py-3">
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                            <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
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
            li.classList.remove('bg-light');
            btn.remove();
            li.querySelector('.bi-circle-fill')?.classList.replace('bi-circle-fill', 'bi-circle');
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
