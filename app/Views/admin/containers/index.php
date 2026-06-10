<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$cur = (new \App\Services\SettingService())->get('finance.currency_symbol', '৳');
?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-box-seam me-2"></i>Containers / Imports</h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li>Containers</li>
        </ul>
    </div>
    <a href="<?= site_url('admin/containers/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Container
    </a>
</div>

<!-- ── Summary Cards ─────────────────────────────────────────────── -->
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2"
                 style="width:42px;height:42px;background:#ECF2FF;color:#5D87FF;">
                <i class="bi bi-box-seam fs-5"></i>
            </div>
            <div style="font-size:1.9rem;font-weight:700;color:var(--mz-primary);"><?= number_format($totals['count']) ?></div>
            <div class="text-muted" style="font-size:.8rem;">Total Containers</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2"
                 style="width:42px;height:42px;background:#E6FFFA;color:#02a98f;">
                <i class="bi bi-patch-check fs-5"></i>
            </div>
            <div style="font-size:1.9rem;font-weight:700;color:#02a98f;"><?= number_format($totals['cleared']) ?></div>
            <div class="text-muted" style="font-size:.8rem;">Customs Cleared</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2"
                 style="width:42px;height:42px;background:#FFF5E0;color:#c98400;">
                <i class="bi bi-hourglass-split fs-5"></i>
            </div>
            <div style="font-size:1.9rem;font-weight:700;color:#c98400;"><?= number_format(max(0, $totals['count'] - $totals['cleared'])) ?></div>
            <div class="text-muted" style="font-size:.8rem;">Pending Clearance</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2"
                 style="width:42px;height:42px;background:#ECF2FF;color:#5D87FF;">
                <i class="bi bi-currency-exchange fs-5"></i>
            </div>
            <div style="font-size:1.5rem;font-weight:700;color:var(--mz-text-primary);"><?= $cur ?> <?= number_format($totals['cost'], 0) ?></div>
            <div class="text-muted" style="font-size:.8rem;">Total Cost</div>
        </div>
    </div>
</div>

<?php if ($flash = session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-3"><i class="bi bi-check-circle-fill me-2"></i><?= esc($flash) ?></div>
<?php endif; ?>
<?php if ($flash = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-3"><i class="bi bi-exclamation-circle-fill me-2"></i><?= esc($flash) ?></div>
<?php endif; ?>

<div class="pd-card">
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" class="form-control" name="q"
                   placeholder="Search container, supplier, BL, product…"
                   value="<?= esc($filters['q'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <select name="customs_status" class="form-select">
                <option value="">All customs status</option>
                <option value="pending" <?= ($filters['customs_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="cleared" <?= ($filters['customs_status'] ?? '') === 'cleared' ? 'selected' : '' ?>>Cleared</option>
                <option value="held"    <?= ($filters['customs_status'] ?? '') === 'held'    ? 'selected' : '' ?>>Held</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All status</option>
                <option value="in_transit" <?= ($filters['status'] ?? '') === 'in_transit' ? 'selected' : '' ?>>In Transit</option>
                <option value="received"   <?= ($filters['status'] ?? '') === 'received'   ? 'selected' : '' ?>>Received</option>
                <option value="sold"       <?= ($filters['status'] ?? '') === 'sold'       ? 'selected' : '' ?>>Sold</option>
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-light"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
        <div class="col-auto">
            <a href="<?= site_url('admin/containers') ?>" class="btn btn-light" title="Reset filters"><i class="bi bi-x-circle"></i></a>
        </div>
    </form>

    <?php if (empty($grouped)): ?>
        <div class="text-center py-5">
            <i class="bi bi-box-seam" style="font-size:2.5rem;color:#E5EAF2;display:block;margin-bottom:10px;"></i>
            <p class="text-muted mb-2">No containers found.</p>
            <a href="<?= site_url('admin/containers/create') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>Add Container
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($grouped as $gi => $group):
            $grpId = 'grp-' . md5($group['company_un_id'] ?? 'none');
        ?>
        <div class="mb-3 ctn-group">

            <!-- ── Company header (collapsed by default) ── -->
            <button type="button"
                    class="btn w-100 text-start d-flex align-items-center gap-2 px-3 py-2 mb-0 ctn-group-toggle collapsed"
                    data-bs-toggle="collapse"
                    data-bs-target="#<?= $grpId ?>"
                    aria-expanded="false"
                    style="background:var(--mz-bg);border:1px solid var(--mz-border);border-radius:10px;">
                <div class="d-inline-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                     style="width:30px;height:30px;background:#ECF2FF;color:#5D87FF;">
                    <i class="bi bi-building" style="font-size:.8rem;"></i>
                </div>
                <span class="fw-semibold" style="font-size:.92rem;color:var(--mz-text-primary);"><?= esc($group['company_name']) ?></span>
                <span class="badge-secondary-soft ms-1"><?= count($group['containers']) ?> container<?= count($group['containers']) !== 1 ? 's' : '' ?></span>
                <i class="bi bi-chevron-right ms-auto ctn-chevron" style="font-size:.75rem;color:var(--mz-text-muted);transition:transform .2s;"></i>
            </button>

            <!-- ── Company body ── -->
            <div class="collapse" id="<?= $grpId ?>">
                <div class="pt-2 pb-1" style="border-left:3px solid #E5EAF2;margin-left:14px;padding-left:12px;">
                    <?php foreach ($group['containers'] as $ci => $c):
                        $ctnId = 'ctn-' . esc($c['un_id']);
                        $hasCartons = ! empty($c['cartons']);
                    ?>

                    <!-- ── Container row ── -->
                    <div class="ctn-item mb-2" style="border:1px solid var(--mz-border);border-radius:10px;overflow:hidden;">

                        <!-- Container main row -->
                        <div class="d-flex align-items-center gap-2 px-3 py-2"
                             style="background:var(--mz-card-bg);">

                            <!-- Carton toggle (only if cartons exist) -->
                            <?php if ($hasCartons): ?>
                            <button type="button"
                                    class="btn btn-sm p-0 d-flex align-items-center justify-content-center flex-shrink-0 ctn-carton-toggle"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#<?= $ctnId ?>"
                                    aria-expanded="false"
                                    style="width:22px;height:22px;border:1px solid var(--mz-border);border-radius:5px;background:var(--mz-bg);">
                                <i class="bi bi-chevron-right" style="font-size:.6rem;color:var(--mz-text-muted);transition:transform .2s;"></i>
                            </button>
                            <?php else: ?>
                            <span class="flex-shrink-0" style="width:22px;height:22px;display:inline-block;"></span>
                            <?php endif; ?>

                            <!-- Serial -->
                            <span class="text-muted fw-semibold flex-shrink-0" style="font-size:.75rem;width:20px;"><?= $c['serial'] ?></span>

                            <!-- Container number + BL -->
                            <div class="flex-grow-1 min-w-0">
                                <a href="<?= site_url('admin/containers/' . $c['un_id']) ?>"
                                   class="fw-semibold text-decoration-none"
                                   style="color:var(--mz-primary);font-size:.875rem;"><?= esc($c['container_number']) ?></a>
                                <?php if (! empty($c['bl_number'])): ?>
                                    <span class="text-muted ms-2" style="font-size:.73rem;">BL: <?= esc($c['bl_number']) ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Carton count badge -->
                            <?php if ($hasCartons): ?>
                            <span class="badge-secondary-soft flex-shrink-0" style="font-size:.72rem;">
                                <i class="bi bi-layers me-1"></i><?= count($c['cartons']) ?> carton<?= count($c['cartons']) !== 1 ? 's' : '' ?>
                            </span>
                            <?php endif; ?>

                            <!-- Product -->
                            <span class="text-muted d-none d-md-inline flex-shrink-0" style="font-size:.8rem;min-width:80px;"><?= esc($c['product_name'] ?? '-') ?></span>

                            <!-- Arrival -->
                            <span class="text-muted d-none d-lg-inline flex-shrink-0" style="font-size:.8rem;white-space:nowrap;min-width:85px;"><?= esc($c['arrival_date'] ?? '-') ?></span>

                            <!-- Customs badge -->
                            <?php $cs = $c['customs_status'] ?? 'pending'; ?>
                            <span class="flex-shrink-0">
                                <?php if ($cs === 'cleared'): ?>
                                    <span class="badge-success-soft" style="font-size:.72rem;">Cleared</span>
                                <?php elseif ($cs === 'held'): ?>
                                    <span class="badge-danger-soft" style="font-size:.72rem;">Held</span>
                                <?php else: ?>
                                    <span class="badge-warning-soft" style="font-size:.72rem;">Pending</span>
                                <?php endif; ?>
                            </span>

                            <!-- Qty -->
                            <span class="text-end d-none d-lg-inline flex-shrink-0" style="font-size:.8rem;min-width:70px;">
                                <?= number_format((float) $c['total_products'], 0) ?> <?= esc($c['unit'] ?? '') ?>
                                <?php if ((float) $c['damaged_products'] > 0): ?>
                                    <span style="color:#FA896B;">/ <?= number_format((float) $c['damaged_products'], 0) ?></span>
                                <?php endif; ?>
                            </span>

                            <!-- Cost -->
                            <span class="fw-semibold flex-shrink-0 d-none d-md-inline" style="font-size:.8rem;white-space:nowrap;min-width:80px;text-align:right;">
                                <?= $cur ?> <?= number_format((float) $c['cost_total'], 0) ?>
                            </span>

                            <!-- Status -->
                            <?php $st = $c['status'] ?? ''; ?>
                            <span class="flex-shrink-0">
                                <?php if ($st === 'received'): ?>
                                    <span class="badge-success-soft" style="font-size:.72rem;">Received</span>
                                <?php elseif ($st === 'in_transit'): ?>
                                    <span class="badge-warning-soft" style="font-size:.72rem;">In Transit</span>
                                <?php elseif ($st === 'sold'): ?>
                                    <span class="badge-primary-soft" style="font-size:.72rem;">Sold</span>
                                <?php else: ?>
                                    <span class="badge-secondary-soft" style="font-size:.72rem;"><?= esc(str_replace('_', ' ', ucfirst($st))) ?></span>
                                <?php endif; ?>
                            </span>

                            <!-- Actions -->
                            <div class="d-flex gap-1 flex-shrink-0 ms-1">
                                <a href="<?= site_url('admin/containers/' . $c['un_id']) ?>"
                                   class="btn btn-sm btn-light" title="View" style="padding:3px 7px;">
                                    <i class="bi bi-eye" style="font-size:.8rem;"></i>
                                </a>
                                <a href="<?= site_url('admin/containers/' . $c['un_id'] . '/edit') ?>"
                                   class="btn btn-sm btn-light" title="Edit" style="padding:3px 7px;">
                                    <i class="bi bi-pencil" style="font-size:.8rem;"></i>
                                </a>
                            </div>
                        </div><!-- /container main row -->

                        <!-- ── Carton sub-rows (tree-connected) ── -->
                        <?php if ($hasCartons): ?>
                        <div class="collapse" id="<?= $ctnId ?>">
                            <div style="background:#FAFBFE;border-top:1px solid var(--mz-border);padding:8px 12px 8px 48px;position:relative;">
                                <!-- Tree vertical line -->
                                <div class="ctn-tree-line" style="position:absolute;left:32px;top:0;bottom:8px;width:2px;background:#E5EAF2;border-radius:2px;"></div>

                                <?php foreach ($c['cartons'] as $ci2 => $carton):
                                    $isLast = ($ci2 === array_key_last($c['cartons']));
                                ?>
                                <div class="d-flex align-items-center gap-3 py-2 ctn-carton-row"
                                     style="position:relative;border-bottom:<?= $isLast ? 'none' : '1px solid #EFF2F7' ?>;">
                                    <!-- Tree horizontal connector -->
                                    <div style="position:absolute;left:-18px;top:50%;width:14px;height:2px;background:#E5EAF2;"></div>
                                    <!-- Tree dot -->
                                    <div style="position:absolute;left:-5px;top:50%;transform:translateY(-50%);width:8px;height:8px;border-radius:50%;background:<?= $carton['condition'] === 'damaged' ? '#FA896B' : ($carton['condition'] === 'partial' ? '#FFAE1F' : '#13DEB9') ?>;border:2px solid white;box-shadow:0 0 0 1px #E5EAF2;"></div>

                                    <!-- Carton icon -->
                                    <div class="d-inline-flex align-items-center justify-content-center rounded flex-shrink-0"
                                         style="width:26px;height:26px;background:#ECF2FF;color:#5D87FF;">
                                        <i class="bi bi-archive" style="font-size:.7rem;"></i>
                                    </div>

                                    <!-- Carton No -->
                                    <span class="fw-semibold flex-shrink-0" style="font-size:.8rem;color:var(--mz-text-primary);min-width:70px;"><?= esc($carton['carton_number'] ?: 'Carton ' . ($ci2 + 1)) ?></span>

                                    <!-- Product -->
                                    <span class="text-muted flex-shrink-0 d-none d-md-inline" style="font-size:.78rem;min-width:80px;"><?= esc($carton['product_name'] ?? '-') ?></span>

                                    <!-- Qty -->
                                    <span class="flex-shrink-0" style="font-size:.78rem;color:var(--mz-text-primary);">
                                        <?= number_format((float) $carton['quantity'], 2) ?> <?= esc($carton['unit'] ?? '') ?>
                                    </span>

                                    <!-- Weight -->
                                    <?php if ($carton['weight_gross'] !== null): ?>
                                    <span class="text-muted flex-shrink-0 d-none d-lg-inline" style="font-size:.75rem;"><?= number_format((float) $carton['weight_gross'], 2) ?> kg</span>
                                    <?php endif; ?>

                                    <!-- Condition badge -->
                                    <span class="flex-shrink-0">
                                        <?php if ($carton['condition'] === 'damaged'): ?>
                                            <span class="badge-danger-soft" style="font-size:.7rem;">Damaged</span>
                                        <?php elseif ($carton['condition'] === 'partial'): ?>
                                            <span class="badge-warning-soft" style="font-size:.7rem;">Partial</span>
                                        <?php else: ?>
                                            <span class="badge-success-soft" style="font-size:.7rem;">Good</span>
                                        <?php endif; ?>
                                    </span>

                                    <!-- Notes -->
                                    <?php if (! empty($carton['notes'])): ?>
                                    <span class="text-muted d-none d-lg-inline" style="font-size:.73rem;font-style:italic;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= esc($carton['notes']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div><!-- /ctn-item -->
                    <?php endforeach; ?>
                </div>
            </div>
        </div><!-- /ctn-group -->
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
/* Chevron rotation on expand */
.ctn-group-toggle:not(.collapsed) .ctn-chevron,
.ctn-carton-toggle[aria-expanded="true"] i {
    transform: rotate(90deg);
}
.ctn-group-toggle { color: var(--mz-text-primary); }
.ctn-group-toggle:hover { background: var(--mz-bg) !important; }
.ctn-group-toggle:focus { box-shadow: none; }
.ctn-carton-row:hover { background: rgba(93,135,255,.03); border-radius: 6px; }
</style>

<script>
// Sync chevron rotation via Bootstrap collapse events
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.ctn-carton-toggle').forEach(function (btn) {
        var target = document.querySelector(btn.dataset.bsTarget);
        if (!target) return;
        target.addEventListener('show.bs.collapse', function () {
            btn.querySelector('i').style.transform = 'rotate(90deg)';
        });
        target.addEventListener('hide.bs.collapse', function () {
            btn.querySelector('i').style.transform = '';
        });
    });
});
</script>

<?= $this->endSection() ?>
