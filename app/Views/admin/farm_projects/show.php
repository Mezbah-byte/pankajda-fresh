<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($project['project_name']) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Operations</li>
            <li><a href="<?= site_url('admin/farm-projects') ?>" class="text-muted text-decoration-none">Farm Projects</a></li>
            <li><?= esc($project['project_name']) ?></li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/farm-projects/' . $project['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/farm-projects') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-12">
        <div class="pd-card">
            <h6 class="fw-semibold mb-4" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Project Details</h6>
            <dl class="row mb-0">
                <dt class="col-sm-3">Items</dt><dd class="col-sm-3"><?= esc($project['item_name'] ?? '-') ?></dd>
                <dt class="col-sm-3">Quantity</dt><dd class="col-sm-3"><?= number_format((float) $project['quantity'], 2) ?> <?= esc($project['quantity_unit'] ?? '') ?></dd>
                <dt class="col-sm-3">Start</dt><dd class="col-sm-3"><?= esc($project['start_date'] ?? '-') ?></dd>
                <dt class="col-sm-3">End</dt><dd class="col-sm-3"><?= esc($project['end_date'] ?? '-') ?></dd>
                <dt class="col-sm-3">Production</dt><dd class="col-sm-3"><?= number_format((float) $project['production_amount'], 2) ?> <?= esc($project['production_unit'] ?? '') ?></dd>
                <dt class="col-sm-3">Company</dt><dd class="col-sm-3"><?= esc($company['company_name'] ?? '-') ?></dd>
                <dt class="col-sm-3">Total Rate</dt><dd class="col-sm-3">৳ <?= number_format((float) $project['total_rate'], 2) ?></dd>
                <dt class="col-sm-3">Sale Amount</dt><dd class="col-sm-3" style="color:#02a98f;">৳ <?= number_format((float) $project['sale_amount'], 2) ?></dd>
                <?php if (! empty($project['notes'])): ?>
                    <dt class="col-sm-3 mt-3">Notes</dt><dd class="col-sm-9 mt-3"><?= nl2br(esc($project['notes'])) ?></dd>
                <?php endif; ?>
            </dl>
        </div>

        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold m-0">Activities</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#addAct"><i class="bi bi-plus-circle me-1"></i>Add Activity</button>
            </div>

            <div class="collapse" id="addAct">
                <form method="post" action="<?= site_url('admin/farm-projects/' . $project['un_id'] . '/activities') ?>" class="collapse-panel mb-4">
                    <?= csrf_field() ?>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <select name="activity_type" class="form-select form-select-sm">
                                <option value="general">General</option>
                                <option value="workers">Workers</option>
                                <option value="seeds">Seeds</option>
                                <option value="fertilizer">Fertilizer</option>
                                <option value="harvest">Harvest</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3"><input type="date" name="activity_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>"></div>
                        <div class="col-md-3"><input type="number" step="0.01" name="rate" class="form-control form-control-sm" placeholder="Rate (৳)"></div>
                        <div class="col-md-3"><input type="number" name="worker_count" class="form-control form-control-sm" placeholder="Worker count"></div>
                        <div class="col-md-6"><input type="text" name="seed_name" class="form-control form-control-sm" placeholder="Seed / item name (if applicable)"></div>
                        <div class="col-md-3"><input type="number" step="0.01" name="seed_quantity" class="form-control form-control-sm" placeholder="Qty"></div>
                        <div class="col-md-3"><input type="text" name="seed_unit" class="form-control form-control-sm" placeholder="Unit"></div>
                        <div class="col-12"><input type="text" name="description" class="form-control form-control-sm" placeholder="Description"></div>
                        <div class="col-12 d-flex gap-2 mt-2"><button class="btn btn-primary btn-sm">Record Activity</button></div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Date</th><th>Type</th><th>Description</th><th>Workers / Items</th><th class="text-end">Rate</th></tr></thead>
                    <tbody>
                        <?php foreach (($project['activities'] ?? []) as $a): ?>
                            <tr>
                                <td><?= esc($a['activity_date']) ?></td>
                                <td><span class="badge-secondary-soft"><?= esc(ucfirst($a['activity_type'] ?? 'general')) ?></span></td>
                                <td><?= esc($a['description'] ?? '-') ?></td>
                                <td class="text-muted" style="font-size:.82rem;">
                                    <?php if (! empty($a['worker_count']) && (int) $a['worker_count'] > 0): ?>
                                        <?= (int) $a['worker_count'] ?> workers
                                    <?php endif; ?>
                                    <?php if (! empty($a['seed_name'])): ?>
                                        <?= esc($a['seed_name']) ?> · <?= number_format((float) $a['seed_quantity'], 2) ?> <?= esc($a['seed_unit']) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-semibold">৳ <?= number_format((float) $a['rate'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($project['activities'])): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">No activities yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
