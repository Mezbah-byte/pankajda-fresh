<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $cur = (new \App\Services\SettingService())->get('finance.currency_symbol', '৳'); ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-cart-plus me-2"></i><?= esc($title) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/purchases') ?>" class="text-muted text-decoration-none">Purchases</a></li>
            <li><?= esc($title) ?></li>
        </ul>
    </div>
    <a href="<?= site_url('admin/purchases') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger mb-4">
        <ul class="mb-0"><?php foreach ((array) $errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>
<?php if ($error = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-4"><?= esc($error) ?></div>
<?php endif; ?>

<form method="post" action="<?= esc($action) ?>" id="purchaseForm">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-8">

            <!-- Header -->
            <div class="pd-card mb-3">
                <h6 class="fw-semibold mb-4" style="color:var(--mz-text-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;">Purchase Header</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Vendor <span class="text-danger">*</span></label>
                        <select name="vendor_un_id" class="form-select" required>
                            <option value="">Select vendor…</option>
                            <?php foreach (($vendors ?? []) as $v): ?>
                                <option value="<?= esc($v['un_id']) ?>"
                                    <?= old('vendor_un_id', $preselect_vendor ?? '') === $v['un_id'] ? 'selected' : '' ?>>
                                    <?= esc($v['vendor_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" class="form-control"
                               value="<?= esc(old('purchase_date', date('Y-m-d'))) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="received" <?= old('status', 'received') === 'received' ? 'selected' : '' ?>>Received</option>
                            <option value="draft"    <?= old('status') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                        <div class="form-text">Received = stock-in + payable now.</div>
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
                        <input type="text" name="notes" class="form-control" value="<?= esc(old('notes', '')) ?>">
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="pd-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-semibold mb-0" style="color:var(--mz-text-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;">
                        Purchased Items <span class="badge bg-secondary ms-1" id="itemCount">0</span>
                    </h6>
                    <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                        <i class="bi bi-plus-lg me-1"></i>Add Item
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size:.875rem;">
                        <thead>
                            <tr>
                                <th style="min-width:200px;">Product</th>
                                <th style="min-width:90px;">Unit</th>
                                <th style="min-width:90px;">Qty <span class="text-danger">*</span></th>
                                <th style="min-width:110px;">Unit Cost</th>
                                <th style="min-width:110px;" class="text-end">Line Total</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody"></tbody>
                    </table>
                </div>
                <div id="emptyItemsMsg" class="text-center py-4 text-muted" style="font-size:.875rem;">
                    <i class="bi bi-box-seam d-block mb-2" style="font-size:1.8rem;color:#E5EAF2;"></i>
                    No items yet. Click "Add Item".
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="col-md-4">
            <div class="pd-card" style="position:sticky;top:90px;">
                <h6 class="fw-semibold mb-4" style="color:var(--mz-text-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;">Bill Summary</h6>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Subtotal</span>
                    <span id="subtotal" class="fw-semibold"><?= $cur ?> 0.00</span>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.78rem;color:var(--mz-text-muted);">Discount (<?= esc($cur) ?>)</label>
                    <input type="number" step="0.01" min="0" name="discount" id="discount" class="form-control form-control-sm" value="0" oninput="recalcTotal()">
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-4">
                    <span class="fw-bold" style="font-size:1.05rem;">Total Bill</span>
                    <span class="fw-bold" id="grandTotal" style="font-size:1.05rem;"><?= $cur ?> 0.00</span>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.82rem;">Paid Now (<?= esc($cur) ?>)</label>
                    <input type="number" step="0.01" min="0" name="paid_amount" id="paidAmount" class="form-control" value="0" oninput="recalcTotal()">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.82rem;">Pay From Bank Account</label>
                    <select name="bank_account_un_id" class="form-select form-select-sm">
                        <option value="">— Cash / none —</option>
                        <?php foreach (($banks ?? []) as $b): ?>
                            <option value="<?= esc($b['un_id']) ?>"><?= esc($b['account_name'] ?? $b['bank_name'] ?? $b['un_id']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span class="text-muted">Payable (Due)</span>
                    <span class="fw-semibold" id="dueAmount" style="color:#FA896B;"><?= $cur ?> 0.00</span>
                </div>
                <button class="btn btn-primary w-100" type="submit"><i class="bi bi-check-circle me-2"></i>Save Purchase</button>
            </div>
        </div>
    </div>
</form>

<script>
const PRODUCTS = <?= json_encode(array_map(fn($p) => [
    'un_id' => $p['un_id'],
    'name'  => $p['product_name'],
    'unit'  => $p['unit'] ?? 'pcs',
], $products ?? []), JSON_UNESCAPED_UNICODE) ?>;
const CUR_SYMBOL = '<?= esc($cur) ?>';
let rowIdx = 0;

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function productDatalist() {
    return PRODUCTS.map(p => `<option value="${escHtml(p.name)}">`).join('');
}

function addRow() {
    const idx = rowIdx++;
    const listId = 'plist_' + idx;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <datalist id="${listId}">${productDatalist()}</datalist>
            <input type="hidden" name="item_product_un_id[]" class="fld-un-id" value="">
            <input type="text" class="form-control form-control-sm fld-name" list="${listId}"
                   name="item_product_name[]" placeholder="Product name…" autocomplete="off" required>
        </td>
        <td><input type="text" class="form-control form-control-sm fld-unit" name="item_unit[]" value="pcs" style="width:80px;"></td>
        <td><input type="number" class="form-control form-control-sm fld-qty" name="item_quantity[]" min="0.001" step="0.001" required style="width:90px;"></td>
        <td><input type="number" class="form-control form-control-sm fld-cost" name="item_unit_cost[]" min="0" step="0.01" value="0" style="width:100px;"></td>
        <td class="text-end fld-total fw-semibold" style="white-space:nowrap;">${CUR_SYMBOL} 0.00</td>
        <td><button type="button" class="btn btn-sm btn-light text-danger btn-remove-row"><i class="bi bi-trash" style="font-size:.8rem;"></i></button></td>
    `;

    tr.querySelector('.fld-name').addEventListener('change', function () {
        const m = PRODUCTS.find(p => p.name.toLowerCase() === this.value.toLowerCase());
        if (m) {
            tr.querySelector('.fld-un-id').value = m.un_id;
            tr.querySelector('.fld-unit').value  = m.unit;
        } else {
            tr.querySelector('.fld-un-id').value = '';
        }
    });
    ['fld-qty', 'fld-cost'].forEach(cls => {
        tr.querySelector('.' + cls).addEventListener('input', () => { recalcRow(tr); recalcTotal(); });
    });
    tr.querySelector('.btn-remove-row').addEventListener('click', () => { tr.remove(); recalcTotal(); updateCount(); });

    document.getElementById('itemsBody').appendChild(tr);
    recalcTotal();
    updateCount();
}

function recalcRow(tr) {
    const qty  = parseFloat(tr.querySelector('.fld-qty').value)  || 0;
    const cost = parseFloat(tr.querySelector('.fld-cost').value) || 0;
    tr.querySelector('.fld-total').textContent = CUR_SYMBOL + ' ' + (qty * cost).toFixed(2);
}

function recalcTotal() {
    let sub = 0;
    document.querySelectorAll('#itemsBody tr').forEach(tr => {
        const qty  = parseFloat(tr.querySelector('.fld-qty')?.value)  || 0;
        const cost = parseFloat(tr.querySelector('.fld-cost')?.value) || 0;
        sub += qty * cost;
    });
    const disc  = parseFloat(document.getElementById('discount').value) || 0;
    const total = Math.max(0, sub - disc);
    const paid  = parseFloat(document.getElementById('paidAmount').value) || 0;
    document.getElementById('subtotal').textContent   = CUR_SYMBOL + ' ' + sub.toFixed(2);
    document.getElementById('grandTotal').textContent = CUR_SYMBOL + ' ' + total.toFixed(2);
    document.getElementById('dueAmount').textContent  = CUR_SYMBOL + ' ' + Math.max(0, total - paid).toFixed(2);
    const rows = document.querySelectorAll('#itemsBody tr').length;
    document.getElementById('emptyItemsMsg').style.display = rows > 0 ? 'none' : 'block';
}

function updateCount() {
    document.getElementById('itemCount').textContent = document.querySelectorAll('#itemsBody tr').length;
}

document.getElementById('addItemBtn').addEventListener('click', addRow);
document.getElementById('purchaseForm').addEventListener('submit', function (e) {
    if (document.querySelectorAll('#itemsBody tr').length === 0) {
        e.preventDefault();
        alert('Add at least one purchased item.');
    }
});
addRow();
</script>

<?= $this->endSection() ?>
