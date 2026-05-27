<?php
// Partial: add stage form
// Used in: visa show page
// Vars: $visa (array), $stages_list (VisaPipelineService::STAGES)
?>
<div class="pd-card">
    <h6 class="fw-semibold mb-3"><i class="bi bi-plus-circle me-2"></i>Add Stage Update</h6>
    <?php if (session('errors')): ?>
        <div class="alert alert-danger py-2"><ul class="mb-0 small"><?php foreach (session('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>
    <form method="post" action="<?= site_url('admin/visas/' . $visa['un_id'] . '/stage') ?>">
        <?= csrf_field() ?>
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label">Stage <span class="text-danger">*</span></label>
                <select class="form-select" name="stage" required>
                    <option value="">Select Stage</option>
                    <?php foreach ($stages_list as $key => $info): ?>
                        <option value="<?= esc($key) ?>" <?= ($visa['status']??'')===$key?'selected':'' ?>><?= esc($info['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="stage_date" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">Notes</label>
                <input type="text" class="form-control" name="notes" placeholder="Optional notes">
            </div>
        </div>
        <button class="btn btn-primary mt-3"><i class="bi bi-check-circle me-1"></i>Add Stage</button>
    </form>
</div>
