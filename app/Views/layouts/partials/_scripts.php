<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Sidebar open / close ──────────────────────────────────────────
function openSidebar() {
    document.getElementById('mzSidebar').classList.add('open');
    document.getElementById('mzOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('mzSidebar').classList.remove('open');
    document.getElementById('mzOverlay').classList.remove('show');
    document.body.style.overflow = '';
}

// Close sidebar on ESC
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

// ── Mobile search toggle ──────────────────────────────────────────
function toggleMobileSearch() {
    const bar = document.getElementById('mzMobileSearch');
    if (!bar) return;
    const visible = bar.style.display !== 'none' && bar.style.display !== '';
    if (visible) {
        bar.style.display = 'none';
    } else {
        bar.style.removeProperty('display');
        bar.querySelector('input')?.focus();
    }
}

// ── Notification bell ─────────────────────────────────────────────
(function () {
    const bell    = document.getElementById('notifBell');
    const badge   = document.getElementById('notifBadge');
    const list    = document.getElementById('notifList');
    const loading = document.getElementById('notifLoading');
    const markAll = document.getElementById('notifMarkAll');

    if (!bell) return;

    const BASE = '<?= site_url('admin/notifications') ?>';
    const CSRF = '<?= csrf_hash() ?>';

    function escHtml(s) {
        return String(s).replace(/[&<>"']/g,
            c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function renderItem(n) {
        const li = document.createElement('li');
        li.id = 'nb-' + n.un_id;
        li.className = 'border-bottom px-3 py-2 d-flex gap-2 align-items-start' + (!n.read_at ? ' bg-light' : '');
        li.innerHTML =
            '<div class="flex-shrink-0 mt-1">' +
                (n.read_at
                    ? '<i class="bi bi-circle text-muted" style="font-size:.5rem;"></i>'
                    : '<i class="bi bi-circle-fill" style="font-size:.5rem;color:var(--mz-primary);"></i>') +
            '</div>' +
            '<div class="flex-grow-1" style="font-size:.82rem;">' +
                '<div class="fw-semibold">' + escHtml(n.title) + '</div>' +
                (n.body ? '<div class="text-muted">' + escHtml(n.body) + '</div>' : '') +
                '<div class="text-muted" style="font-size:.72rem;">' + escHtml(n.type) + '</div>' +
            '</div>' +
            (n.link
                ? '<a href="' + escHtml(n.link) + '" class="btn btn-sm btn-light py-0 px-1 align-self-center" style="flex-shrink:0;"><i class="bi bi-arrow-right-circle"></i></a>'
                : '');
        return li;
    }

    function updateBadge(count) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    }

    function loadUnread() {
        fetch(BASE + '/../../api/v1/notifications/unread', {
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const items = res.data || [];
            updateBadge(items.length);
            if (loading) loading.remove();
            list.innerHTML = '';
            if (items.length === 0) {
                list.innerHTML = '<li class="text-center py-4 text-muted small"><i class="bi bi-bell-slash me-1"></i>No new notifications</li>';
                return;
            }
            items.forEach(n => list.appendChild(renderItem(n)));
        })
        .catch(() => {});
    }

    bell.addEventListener('show.bs.dropdown', loadUnread);

    if (markAll) {
        markAll.addEventListener('click', e => {
            e.stopPropagation();
            fetch(BASE + '/read-all', {
                method: 'POST',
                headers: {'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF}
            }).then(() => {
                updateBadge(0);
                list.querySelectorAll('.bg-light').forEach(el => el.classList.remove('bg-light'));
                list.querySelectorAll('.bi-circle-fill').forEach(el => {
                    el.classList.replace('bi-circle-fill', 'bi-circle');
                    el.style.color = '';
                    el.classList.add('text-muted');
                });
            });
        });
    }

    // Initial badge count (silent)
    fetch(BASE + '/../../api/v1/notifications/count', {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(res => {
        if (res.success && res.data && res.data.unread > 0) {
            updateBadge(res.data.unread);
        }
    })
    .catch(() => {});
})();
</script>
