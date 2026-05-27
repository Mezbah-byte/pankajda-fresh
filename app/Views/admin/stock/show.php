<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($item['item_name']) ?></h4>
        <ul class="mz-breadcrumb"><li>Operations</li><li><a href="<?= site_url('admin/stock') ?>">Stock</a></li><li><?= esc($item['item_name']) ?></li></ul>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#stockInModal"><i class="bi bi-arrow-down-circle me-1"></i>Stock In</button>
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#stockOutModal"><i class="bi bi-arrow-up-circle me-1"></i>Stock Out</button>
        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#adjustModal"><i class="bi bi-sliders me-1"></i>Adjust</button>
        <a href="<?= site_url('admin/stock/' . $item['un_id'] . '/edit') ?>" class="btn btn-light"><i class="bi bi-pencil me-1"></i>Edit</a>
        <form method="post" action="<?= site_url('admin/stock/' . $item['un_id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this item?');">
            <?= csrf_field() ?>
            <button class="btn btn-light text-danger"><i class="bi bi-trash"></i></button>
        </form>
    </div>
</div>

<?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="pd-stat <?= (float)($item['current_qty']??0)<=(float)($item['min_qty']??0)?'gradient-4':'gradient-1' ?>">
            <div class="stat-label">Current Stock</div>
            <div class="stat-value"><?= number_format((float)($item['current_qty']??0),2) ?> <?= esc($item['unit']??'pcs') ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="pd-stat gradient-3">
            <div class="stat-label">Unit Cost</div>
            <div class="stat-value">৳ <?= number_format((float)($item['unit_cost']??0),2) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="pd-stat gradient-2">
            <div class="stat-label">Stock Value</div>
            <div class="stat-value">৳ <?= number_format(((float)($item['current_qty']??0))*((float)($item['unit_cost']??0)),2) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="pd-stat gradient-1">
            <div class="stat-label">Min Qty Alert</div>
            <div class="stat-value"><?= number_format((float)($item['min_qty']??0),2) ?> <?= esc($item['unit']??'pcs') ?></div>
        </div>
    </div>
</div>

<div class="pd-card">
    <h6 class="fw-semibold mb-3">Transaction History</h6>
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr><th>Date</th><th>Type</th><th class="text-end">Quantity</th><th class="text-end">Unit Cost</th><th>Reference</th><th>Notes</th></tr>
            </thead>
            <tbody>
                <?php foreach (($transactions ?? []) as $txn): ?>
                    <?php
                    $typeColors = ['in'=>'success','out'=>'danger','adjustment'=>'warning'];
                    $tc = $typeColors[$txn['type']??'adjustment'] ?? 'secondary';
                    ?>
                    <tr>
                        <td><?= esc($txn['txn_date']) ?></td>
                        <td><span class="badge bg-<?= $tc ?>-subtle text-<?= $tc ?>"><?= esc(ucfirst($txn['type']??'')) ?></span></td>
                        <td class="text-end fw-semibold <?= ($txn['type']??'')==='out'?'text-danger':'text-success' ?>">
                            <?= ($txn['type']??'')==='out'?'-':'+' ?><?= number_format((float)($txn['quantity']??0),2) ?>
                        </td>
                        <td class="text-end"><?= $txn['unit_cost'] ? '৳ ' . number_format((float)$txn['unit_cost'],2) : '-' ?></td>
                        <td><?= esc($txn['reference']??'-') ?></td>
                        <td class="text-muted"><?= esc($txn['notes']??'-') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($transactions)): ?><tr><td colspan="6" class="text-center text-muted py-4">No transactions yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Stock In Modal -->
<div class="modal fade" id="stockInModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title text-success"><i class="bi bi-arrow-down-circle me-2"></i>Stock In</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="post" action="<?= site_url('admin/stock/' . $item['un_id'] . '/in') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Quantity <span class="text-danger">*</span></label><input type="number" step="0.01" min="0.01" class="form-control" name="quantity" required></div>
                    <div class="mb-3"><label class="form-label">Unit Cost (৳)</label><input type="number" step="0.01" min="0" class="form-control" name="unit_cost" value="<?= esc($item['unit_cost']??'') ?>"></div>
                    <div class="mb-3"><label class="form-label">Date</label><input type="date" class="form-control" name="txn_date" value="<?= date('Y-m-d') ?>"></div>
                    <div class="mb-3"><label class="form-label">Reference</label><input type="text" class="form-control" name="reference" placeholder="Invoice / PO number"></div>
                    <div class="mb-3"><label class="form-label">Notes</label><textarea class="form-control" name="notes" rows="2"></textarea></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-success">Receive Stock</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Stock Out Modal -->
<div class="modal fade" id="stockOutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title text-warning"><i class="bi bi-arrow-up-circle me-2"></i>Stock Out</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="post" action="<?= site_url('admin/stock/' . $item['un_id'] . '/out') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-info py-2 mb-3"><small>Available: <strong><?= number_format((float)($item['current_qty']??0),2) ?> <?= esc($item['unit']??'pcs') ?></strong></small></div>
                    <div class="mb-3"><label class="form-label">Quantity <span class="text-danger">*</span></label><input type="number" step="0.01" min="0.01" max="<?= (float)($item['current_qty']??0) ?>" class="form-control" name="quantity" required></div>
                    <div class="mb-3"><label class="form-label">Date</label><input type="date" class="form-control" name="txn_date" value="<?= date('Y-m-d') ?>"></div>
                    <div class="mb-3"><label class="form-label">Reference</label><input type="text" class="form-control" name="reference" placeholder="Purpose / project"></div>
                    <div class="mb-3"><label class="form-label">Notes</label><textarea class="form-control" name="notes" rows="2"></textarea></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-warning">Issue Stock</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Adjust Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-sliders me-2"></i>Adjust Stock</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="post" action="<?= site_url('admin/stock/' . $item['un_id'] . '/adjust') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">New Quantity <span class="text-danger">*</span></label><input type="number" step="0.01" min="0" class="form-control" name="new_qty" value="<?= esc(number_format((float)($item['current_qty']??0),2,'.','')) ?>" required></div>
                    <div class="mb-3"><label class="form-label">Reason</label><input type="text" class="form-control" name="notes" placeholder="Reason for adjustment"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Apply Adjustment</button></div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
