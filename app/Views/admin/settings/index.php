<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Settings</h4>
        <p class="text-muted small m-0">Global preferences for the ERP. Changes apply system-wide.</p>
    </div>
</div>

<form method="post" action="<?= site_url('admin/settings') ?>">
    <?= csrf_field() ?>

    <?php $groupTitles = ['general' => 'General', 'finance' => 'Finance', 'invoice' => 'Invoice', 'site' => 'Site']; ?>
    <?php $groupIcons  = ['general' => 'bi-globe', 'finance' => 'bi-cash-stack', 'invoice' => 'bi-receipt', 'site' => 'bi-window']; ?>

    <?php if (empty($grouped)): ?>
        <div class="pd-card text-center py-5">
            <i class="bi bi-gear" style="font-size:2.5rem;color:#cbcae3;"></i>
            <p class="text-muted mt-2 mb-0">No settings yet. Run <code>php spark db:seed SettingsSeeder</code> to populate defaults.</p>
        </div>
    <?php else: ?>
        <?php foreach ($grouped as $group => $rows): ?>
            <div class="pd-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded d-inline-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <i class="bi <?= esc($groupIcons[$group] ?? 'bi-gear') ?>"></i>
                    </div>
                    <h6 class="fw-bold m-0"><?= esc($groupTitles[$group] ?? ucfirst($group)) ?></h6>
                </div>
                <div class="row g-3">
                    <?php foreach ($rows as $row): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted"><?= esc($row['key']) ?></label>
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

        <div class="d-flex gap-2 mt-3">
            <button class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save All Settings</button>
        </div>
    <?php endif; ?>
</form>

<?= $this->endSection() ?>
