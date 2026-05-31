<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($container['container_number']) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/containers') ?>" class="text-muted text-decoration-none">Containers</a></li>
            <li><?= esc($container['container_number']) ?></li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/containers/' . $container['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/containers') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="pd-card">
            <h6 class="fw-semibold mb-4" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Container Details</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4">BL Number</dt><dd class="col-sm-8"><?= esc($container['bl_number'] ?? '-') ?></dd>
                <dt class="col-sm-4">Product</dt><dd class="col-sm-8"><?= esc($container['product_name'] ?? '-') ?></dd>
                <dt class="col-sm-4">Origin</dt><dd class="col-sm-8"><?= esc($container['origin_country'] ?? '-') ?></dd>
                <dt class="col-sm-4">Arrival Date</dt><dd class="col-sm-8"><?= esc($container['arrival_date'] ?? '-') ?></dd>
                <dt class="col-sm-4">Customs Status</dt>
                <dd class="col-sm-8">
                    <?php $cs = $container['customs_status'] ?? 'pending'; ?>
                    <?php if ($cs === 'cleared'): ?>
                        <span class="badge-success-soft">Cleared</span>
                    <?php elseif ($cs === 'held'): ?>
                        <span class="badge-danger-soft">Held</span>
                    <?php else: ?>
                        <span class="badge-warning-soft">Pending</span>
                    <?php endif; ?>
                    <?php if (! empty($container['customs_clear_date'])): ?>
                        <span class="text-muted ms-1" style="font-size:.8rem;">on <?= esc($container['customs_clear_date']) ?></span>
                    <?php endif; ?>
                </dd>
                <dt class="col-sm-4">Total Products</dt><dd class="col-sm-8"><?= number_format((float) $container['total_products'], 2) ?> <?= esc($container['unit']) ?></dd>
                <dt class="col-sm-4">Damaged</dt><dd class="col-sm-8" style="color:#FA896B;"><?= number_format((float) $container['damaged_products'], 2) ?> <?= esc($container['unit']) ?></dd>
                <dt class="col-sm-4">Company</dt><dd class="col-sm-8"><?= esc($company['company_name'] ?? '-') ?></dd>
                <dt class="col-sm-4">Notes</dt><dd class="col-sm-8"><?= nl2br(esc($container['notes'] ?? '-')) ?></dd>
            </dl>
        </div>
    </div>

    <div class="col-md-4">
        <div class="pd-card">
            <h6 class="fw-semibold mb-4" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">Cost Breakdown</h6>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Product Cost</span>
                <span>৳ <?= number_format(max(0, (float) $container['cost_total'] - (float) $container['customs_cost'] - (float) $container['transport_cost'] - (float) $container['other_cost']), 0) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Customs</span>
                <span>৳ <?= number_format((float) $container['customs_cost'], 0) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Transport</span>
                <span>৳ <?= number_format((float) $container['transport_cost'], 0) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Other</span>
                <span>৳ <?= number_format((float) $container['other_cost'], 0) ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-4">
                <span class="fw-bold">Total Cost</span>
                <span class="fw-bold">৳ <?= number_format((float) $container['cost_total'], 0) ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Sales (so far)</span>
                <span style="color:#02a98f;">৳ <?= number_format((float) ($container['total_sold'] ?? 0), 0) ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="fw-bold">Profit / Loss</span>
                <span class="fw-bold"
                      style="color:<?= ((float) ($container['profit'] ?? 0)) >= 0 ? '#02a98f' : '#FA896B' ?>;">
                    ৳ <?= number_format((float) ($container['profit'] ?? 0), 0) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- ── Cartons ──────────────────────────────────────────────────────────── -->
<div class="pd-card mt-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-semibold mb-0" style="color:var(--mz-text-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;">
            Cartons <span class="badge bg-secondary ms-1"><?= count($cartons) ?></span>
        </h6>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCartonModal">
            <i class="bi bi-plus-lg me-1"></i>Add Carton
        </button>
    </div>

    <?php if (empty($cartons)): ?>
        <p class="text-muted mb-0" style="font-size:.875rem;">No cartons recorded yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Carton No.</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Gross kg</th>
                        <th>Net kg</th>
                        <th>Condition</th>
                        <th>Notes</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartons as $i => $c): ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td><?= esc($c['carton_number'] ?? '-') ?></td>
                        <td><?= esc($c['product_name'] ?? '-') ?></td>
                        <td><?= number_format((float) $c['quantity'], 2) ?> <?= esc($c['unit']) ?></td>
                        <td><?= $c['weight_gross'] !== null ? number_format((float) $c['weight_gross'], 2) : '-' ?></td>
                        <td><?= $c['weight_net']   !== null ? number_format((float) $c['weight_net'],   2) : '-' ?></td>
                        <td>
                            <?php if ($c['condition'] === 'damaged'): ?>
                                <span class="badge-danger-soft">Damaged</span>
                            <?php elseif ($c['condition'] === 'partial'): ?>
                                <span class="badge-warning-soft">Partial</span>
                            <?php else: ?>
                                <span class="badge-success-soft">Good</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted" style="font-size:.8rem;"><?= esc($c['notes'] ?? '') ?></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light btn-edit-carton"
                                    data-un-id="<?= esc($c['un_id']) ?>"
                                    data-carton-number="<?= esc($c['carton_number'] ?? '') ?>"
                                    data-product-name="<?= esc($c['product_name'] ?? '') ?>"
                                    data-quantity="<?= esc($c['quantity']) ?>"
                                    data-unit="<?= esc($c['unit']) ?>"
                                    data-weight-gross="<?= esc($c['weight_gross'] ?? '') ?>"
                                    data-weight-net="<?= esc($c['weight_net'] ?? '') ?>"
                                    data-condition="<?= esc($c['condition']) ?>"
                                    data-notes="<?= esc($c['notes'] ?? '') ?>"
                                    data-bs-toggle="modal" data-bs-target="#editCartonModal">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="post" action="<?= site_url('admin/containers/' . $container['un_id'] . '/cartons/' . $c['un_id'] . '/delete') ?>" class="d-inline"
                                  onsubmit="return confirm('Delete this carton?');">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="fw-semibold">
                        <td colspan="3" class="text-muted">Total</td>
                        <td><?= number_format(array_sum(array_column($cartons, 'quantity')), 2) ?></td>
                        <td><?= number_format(array_sum(array_column($cartons, 'weight_gross')), 2) ?></td>
                        <td><?= number_format(array_sum(array_column($cartons, 'weight_net')), 2) ?></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Add Carton Modal -->
<div class="modal fade" id="addCartonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= site_url('admin/containers/' . $container['un_id'] . '/cartons') ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Add Carton</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Carton Number</label>
                            <input type="text" name="carton_number" class="form-control" placeholder="e.g. C-001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control" value="<?= esc($container['product_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" step="0.001" min="0" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit" class="form-control" value="pcs" placeholder="pcs / kg …">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Condition</label>
                            <select name="condition" class="form-select">
                                <option value="good">Good</option>
                                <option value="partial">Partial</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gross Weight (kg)</label>
                            <input type="number" name="weight_gross" class="form-control" step="0.001" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Net Weight (kg)</label>
                            <input type="number" name="weight_net" class="form-control" step="0.001" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Carton</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Carton Modal -->
<div class="modal fade" id="editCartonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="editCartonForm" action="">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit Carton</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Carton Number</label>
                            <input type="text" name="carton_number" id="edit_carton_number" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" id="edit_product_name" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" id="edit_quantity" class="form-control" step="0.001" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit" id="edit_unit" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Condition</label>
                            <select name="condition" id="edit_condition" class="form-select">
                                <option value="good">Good</option>
                                <option value="partial">Partial</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gross Weight (kg)</label>
                            <input type="number" name="weight_gross" id="edit_weight_gross" class="form-control" step="0.001" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Net Weight (kg)</label>
                            <input type="number" name="weight_net" id="edit_weight_net" class="form-control" step="0.001" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-edit-carton').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var d = this.dataset;
        var baseUrl = '<?= site_url('admin/containers/' . $container['un_id'] . '/cartons/') ?>';
        document.getElementById('editCartonForm').action = baseUrl + d.unId;
        document.getElementById('edit_carton_number').value = d.cartonNumber;
        document.getElementById('edit_product_name').value  = d.productName;
        document.getElementById('edit_quantity').value      = d.quantity;
        document.getElementById('edit_unit').value          = d.unit;
        document.getElementById('edit_weight_gross').value  = d.weightGross;
        document.getElementById('edit_weight_net').value    = d.weightNet;
        document.getElementById('edit_condition').value     = d.condition;
        document.getElementById('edit_notes').value         = d.notes;
    });
});
</script>

<?= $this->endSection() ?>
