<?php
// Partial: visa stage timeline
// Used in: visa show page or standalone
// Vars: $visa, $stages (array of stage rows), $labels (VisaPipelineService::STAGES)
?>
<div class="visa-timeline">
    <?php if (empty($stages)): ?>
        <p class="text-muted">No stage history yet.</p>
    <?php else: ?>
        <div class="timeline">
            <?php foreach ($stages as $stage): ?>
                <?php
                $info   = $labels[$stage['stage']] ?? ['label' => ucfirst($stage['stage']), 'color' => 'secondary'];
                $color  = $info['color'];
                $label  = $info['label'];
                ?>
                <div class="timeline-item d-flex gap-3 mb-3">
                    <div class="timeline-dot" style="flex-shrink:0;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--mz-bg);">
                        <span class="badge bg-<?= esc($color) ?>-subtle text-<?= esc($color) ?>" style="font-size:.7rem;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                            <i class="bi bi-circle-fill" style="font-size:.45rem;"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fw-semibold" style="font-size:.88rem;"><?= esc($label) ?></span>
                            <span class="text-muted" style="font-size:.75rem;"><?= esc($stage['stage_date']) ?></span>
                        </div>
                        <?php if (!empty($stage['notes'])): ?>
                            <div class="text-muted" style="font-size:.8rem;margin-top:2px;"><?= esc($stage['notes']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
