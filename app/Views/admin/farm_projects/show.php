<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold"><?= esc($project['project_name']) ?></h4>
        <p class="text-muted small m-0"><?= esc($project['crop_name'] ?? '') ?> &middot; <?= number_format((float) $project['land_size'], 2) ?> <?= esc($project['land_unit'] ?? '') ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/farm-projects/' . $project['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/farm-projects') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Project Details</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4 text-muted">Crop</dt><dd class="col-sm-8"><?= esc($project['crop_name'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Land</dt><dd class="col-sm-8"><?= number_format((float) $project['land_size'], 2) ?> <?= esc($project['land_unit'] ?? '') ?></dd>
                <dt class="col-sm-4 text-muted">Start</dt><dd class="col-sm-8"><?= esc($project['start_date'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">End</dt><dd class="col-sm-8"><?= esc($project['end_date'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Production</dt><dd class="col-sm-8"><?= number_format((float) $project['production_amount'], 2) ?> <?= esc($project['production_unit'] ?? '') ?></dd>
                <dt class="col-sm-4 text-muted">Company</dt><dd class="col-sm-8"><?= esc($company['company_name'] ?? '-') ?></dd>
                <dt class="col-sm-4 text-muted">Notes</dt><dd class="col-sm-8"><?= nl2br(esc($project['notes'] ?? '-')) ?></dd>
            </dl>
        </div>

        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0">Activities</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#addAct"><i class="bi bi-plus-circle me-1"></i>Add Activity</button>
            </div>

            <div class="collapse" id="addAct">
                <form method="post" action="<?= site_url('admin/farm-projects/' . $project['un_id'] . '/activities') ?>" class="border rounded p-3 mb-3" style="background:#f7f8fc;">
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
                        <div class="col-md-3"><input type="number" step="0.01" name="cost" class="form-control form-control-sm" placeholder="Cost (৳)"></div>
                        <div class="col-md-3"><input type="number" name="worker_count" class="form-control form-control-sm" placeholder="Worker count"></div>
                        <div class="col-md-6"><input type="text" name="seed_name" class="form-control form-control-sm" placeholder="Seed name (if applicable)"></div>
                        <div class="col-md-3"><input type="number" step="0.01" name="seed_quantity" class="form-control form-control-sm" placeholder="Qty"></div>
                        <div class="col-md-3"><input type="text" name="seed_unit" class="form-control form-control-sm" placeholder="Unit"></div>
                        <div class="col-12"><input type="text" name="description" class="form-control form-control-sm" placeholder="Description"></div>
                        <div class="col-12 d-flex gap-2 mt-2"><button class="btn btn-primary btn-sm">Record Activity</button></div>
                    </div>
                </form>
            </div>

            <table class="table align-middle">
                <thead><tr><th>Date</th><th>Type</th><th>Description</th><th>Workers / Seeds</th><th class="text-end">Cost</th></tr></thead>
                <tbody>
                    <?php foreach (($project['activities'] ?? []) as $a): ?>
                        <tr>
                            <td><?= esc($a['activity_date']) ?></td>
                            <td><span class="badge bg-light text-dark"><?= esc(ucfirst($a['activity_type'] ?? 'general')) ?></span></td>
                            <td><?= esc($a['description'] ?? '-') ?></td>
                            <td class="small text-muted">
                                <?php if (! empty($a['worker_count']) && (int) $a['worker_count'] > 0): ?>
                                    <?= (int) $a['worker_count'] ?> workers
                                <?php endif; ?>
                                <?php if (! empty($a['seed_name'])): ?>
                                    <?= esc($a['seed_name']) ?> · <?= number_format((float) $a['seed_quantity'], 2) ?> <?= esc($a['seed_unit']) ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-semibold">৳ <?= number_format((float) $a['cost'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($project['activities'])): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">No activities yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-bold mb-3">Profit / Loss</h6>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Total Cost</span><span>৳ <?= number_format((float) $project['total_cost'], 2) ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Sale Amount</span><span class="text-success">৳ <?= number_format((float) $project['sale_amount'], 2) ?></span></div>
            <hr>
            <div class="d-flex justify-content-between fs-5"><span class="fw-bold">Profit/Loss</span>
                <span class="fw-bold <?= ((float) $project['profit']) >= 0 ? 'text-success' : 'text-danger' ?>">৳ <?= number_format((float) $project['profit'], 2) ?></span>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
