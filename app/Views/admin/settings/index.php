<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header">
    <h4>Settings</h4>
    <ul class="mz-breadcrumb">
        <li>Insights</li>
        <li>Settings</li>
    </ul>
</div>

<form method="post" action="<?= site_url('admin/settings') ?>">
    <?= csrf_field() ?>

    <?php $groupTitles = ['general' => 'General', 'finance' => 'Finance', 'invoice' => 'Invoice', 'site' => 'Site']; ?>
    <?php $groupIcons  = ['general' => 'bi-globe', 'finance' => 'bi-cash-stack', 'invoice' => 'bi-receipt', 'site' => 'bi-window']; ?>
    <?php $groupColors = ['general' => '#5D87FF', 'finance' => '#13DEB9', 'invoice' => '#FFAE1F', 'site' => '#FA896B']; ?>

    <?php if (empty($grouped)): ?>
        <div class="pd-card text-center py-5">
            <i class="bi bi-gear" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:12px;"></i>
            <p class="text-muted mb-0">No settings yet. Run <code>php spark db:seed SettingsSeeder</code> to populate defaults.</p>
        </div>
    <?php else: ?>
        <?php foreach ($grouped as $group => $rows): ?>
            <div class="pd-card">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-2"
                         style="width:42px;height:42px;background:<?= esc($groupColors[$group] ?? '#5D87FF') ?>18;color:<?= esc($groupColors[$group] ?? '#5D87FF') ?>;flex-shrink:0;">
                        <i class="bi <?= esc($groupIcons[$group] ?? 'bi-gear') ?>"></i>
                    </div>
                    <h6 class="fw-bold m-0"><?= esc($groupTitles[$group] ?? ucfirst($group)) ?></h6>
                </div>
                <div class="row g-3">
                    <?php foreach ($rows as $row): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.78rem;color:var(--mz-text-muted);text-transform:uppercase;letter-spacing:.3px;"><?= esc($row['key']) ?></label>
                            <?php if (in_array($row['type'] ?? 'string', ['text', 'textarea'], true)): ?>
                                <textarea class="form-control" name="<?= esc($row['key']) ?>" rows="3"><?= esc($row['value']) ?></textarea>
                            <?php else: ?>
                                <input type="<?= ($row['type'] ?? 'string') === 'number' ? 'number' : 'text' ?>" class="form-control"
                                       name="<?= esc($row['key']) ?>"
                                       value="<?= esc($row['value']) ?>"
                                       <?= ($row['type'] ?? 'string') === 'number' ? 'step="0.01"' : '' ?>>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="d-flex gap-2 mt-1">
            <button class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save All Settings</button>
        </div>
    <?php endif; ?>
</form>

<?= $this->endSection() ?>
