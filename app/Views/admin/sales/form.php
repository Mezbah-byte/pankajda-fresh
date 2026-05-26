<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">New Sale</h4>
        <p class="text-muted small m-0">Invoice <?= esc($next_invoice) ?></p>
    </div>
    <a href="<?= site_url('admin/sales') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($err) ?></div>
<?php endif; ?>

<form method="post" action="<?= esc($action) ?>" id="saleForm">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-8">
            <div class="pd-card">
                <h6 class="fw-bold mb-3">Header</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                        <select name="customer_un_id" class="form-select" required>
                            <option value="">Select customer...</option>
                            <?php foreach (($customers ?? []) as $c): ?>
                                <option value="<?= esc($c['un_id']) ?>"><?= esc($c['customer_name']) ?> <?= $c['phone'] ? '— ' . esc($c['phone']) : '' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Sale Type <span class="text-danger">*</span></label>
                        <select name="sale_type" class="form-select" id="saleType" required>
                            <option value="cash">Cash</option>
                            <option value="credit">Credit</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Sale Date</label>
                        <input type="date" name="sale_date" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Company</label>
                        <select name="company_un_id" class="form-select">
                            <option value="">— None —</option>
                            <?php foreach (($companies ?? []) as $cm): ?>
                                <option value="<?= esc($cm['un_id']) ?>"><?= esc($cm['company_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Notes</label>
                        <input type="text" name="notes" class="form-control">
                    </div>
                </div>
            </div>

            <div class="pd-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold m-0">Line Items</h6>
                    <button type="button" class="btn btn-sm btn-light" onclick="addItem()"><i class="bi bi-plus-circle me-1"></i>Add Row</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle" id="itemsTable">
                        <thead><tr><th>Product</th><th style="width:100px;">Qty</th><th style="width:90px;">Unit</th><th style="width:120px;">Price (৳)</th><th style="width:130px;" class="text-end">Total</th><th style="width:60px;"></th></tr></thead>
                        <tbody id="itemsBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="pd-card sticky-top" style="top:90px;">
                <h6 class="fw-bold mb-3">Summary</h6>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Subtotal</span><span id="subtotal">৳ 0.00</span></div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-0">Discount</label>
                        <input type="number" step="0.01" name="discount" id="discount" class="form-control form-control-sm" value="0" oninput="recalc()">
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-0">Tax</label>
                        <input type="number" step="0.01" name="tax" id="tax" class="form-control form-control-sm" value="0" oninput="recalc()">
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2 fs-5"><span class="fw-bold">Total</span><span class="fw-bold" id="total">৳ 0.00</span></div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold mb-1">Paid Now</label>
                    <input type="number" step="0.01" name="paid_amount" id="paidAmount" class="form-control" value="0" oninput="recalc()">
                </div>
                <div class="d-flex justify-content-between mb-3"><span class="text-muted">Due</span><span class="fw-semibold text-danger" id="dueAmount">৳ 0.00</span></div>
                <button class="btn btn-primary w-100" type="submit"><i class="bi bi-check-circle me-2"></i>Save Sale</button>
            </div>
        </div>
    </div>
</form>

<script>
function addItem() {
    const i = document.querySelectorAll('#itemsBody tr').length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" class="form-control" name="items[${i}][product_name]" required></td>
        <td><input type="number" step="0.001" min="0" class="form-control qty" name="items[${i}][quantity]" value="1" oninput="recalc()"></td>
        <td><input type="text" class="form-control" name="items[${i}][unit]" value="kg"></td>
        <td><input type="number" step="0.01" min="0" class="form-control price" name="items[${i}][unit_price]" value="0" oninput="recalc()"></td>
        <td class="text-end fw-semibold line-total">৳ 0.00</td>
        <td><button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('tr').remove();recalc()"><i class="bi bi-x"></i></button></td>`;
    document.getElementById('itemsBody').appendChild(tr);
    recalc();
}
function recalc() {
    let subtotal = 0;
    document.querySelectorAll('#itemsBody tr').forEach(tr => {
        const q = parseFloat(tr.querySelector('.qty').value) || 0;
        const p = parseFloat(tr.querySelector('.price').value) || 0;
        const t = q * p;
        tr.querySelector('.line-total').textContent = '৳ ' + t.toFixed(2);
        subtotal += t;
    });
    const disc = parseFloat(document.getElementById('discount').value) || 0;
    const tax  = parseFloat(document.getElementById('tax').value) || 0;
    const total = Math.max(0, subtotal - disc + tax);
    document.getElementById('subtotal').textContent = '৳ ' + subtotal.toFixed(2);
    document.getElementById('total').textContent    = '৳ ' + total.toFixed(2);

    // For cash sales auto-fill paid = total
    if (document.getElementById('saleType').value === 'cash') {
        document.getElementById('paidAmount').value = total.toFixed(2);
    }
    const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    const due = Math.max(0, total - paid);
    document.getElementById('dueAmount').textContent = '৳ ' + due.toFixed(2);
}
document.getElementById('saleType').addEventListener('change', recalc);
addItem(); // start with one row
</script>

<?= $this->endSection() ?>
