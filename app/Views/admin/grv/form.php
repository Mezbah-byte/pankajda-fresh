<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $cur = (new \App\Services\SettingService())->get('finance.currency_symbol', '৳'); ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-arrow-return-left me-2"></i><?= esc($title) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/grv') ?>" class="text-muted text-decoration-none">GRV</a></li>
            <li><?= esc($title) ?></li>
        </ul>
    </div>
    <a href="<?= site_url('admin/grv') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger mb-4">
        <ul class="mb-0"><?php foreach ((array) $errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>
<?php if ($error = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-4"><?= esc($error) ?></div>
<?php endif; ?>

<form method="post" action="<?= esc($action) ?>" id="grvForm">
    <?= csrf_field() ?>

    <!-- ── GRV Header ───────────────────────────────────────────── -->
    <div class="pd-card mb-3">
        <h6 class="fw-semibold mb-4" style="color:var(--mz-text-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;">GRV Header</h6>
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                <select name="customer_un_id" class="form-select" required>
                    <option value="">Select customer…</option>
                    <?php foreach (($customers ?? []) as $c): ?>
                        <option value="<?= esc($c['un_id']) ?>"
                            <?= old('customer_un_id', $grv['customer_un_id'] ?? ($preselect_customer ?? '')) === $c['un_id'] ? 'selected' : '' ?>>
                            <?= esc($c['customer_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">GRV Date <span class="text-danger">*</span></label>
                <input type="date" name="grv_date" class="form-control"
                       value="<?= esc(old('grv_date', $grv['grv_date'] ?? date('Y-m-d'))) ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="draft"    <?= old('status', $grv['status'] ?? 'draft') === 'draft'    ? 'selected' : '' ?>>Draft</option>
                    <option value="approved" <?= old('status', $grv['status'] ?? 'draft') === 'approved' ? 'selected' : '' ?>>Approved</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Related Sale (optional)</label>
                <input type="text" name="sale_un_id" class="form-control"
                       value="<?= esc(old('sale_un_id', $grv['sale_un_id'] ?? ($preselect_sale ?? ''))) ?>"
                       placeholder="Sale UN-ID if returning from specific invoice">
                <div class="form-text">Paste the Sale ID to link this GRV to an invoice.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Company</label>
                <select name="company_un_id" class="form-select">
                    <option value="">— None —</option>
                    <?php foreach (($companies ?? []) as $cm): ?>
                        <option value="<?= esc($cm['un_id']) ?>"
                            <?= old('company_un_id', $grv['company_un_id'] ?? '') === $cm['un_id'] ? 'selected' : '' ?>>
                            <?= esc($cm['company_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea class="form-control" name="notes" rows="2"
                          placeholder="Overall notes for this return…"><?= esc(old('notes', $grv['notes'] ?? '')) ?></textarea>
            </div>
        </div>
    </div>

    <!-- ── Return Line Items ────────────────────────────────────── -->
    <div class="pd-card mb-3">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-semibold mb-0" style="color:var(--mz-text-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;">
                Returned Items <span class="badge bg-secondary ms-1" id="itemCount">0</span>
            </h6>
            <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                <i class="bi bi-plus-lg me-1"></i>Add Item
            </button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0" id="itemsTable" style="font-size:.875rem;">
                <thead>
                    <tr>
                        <th style="min-width:200px;">Product</th>
                        <th style="min-width:100px;">Unit</th>
                        <th style="min-width:90px;">Qty <span class="text-danger">*</span></th>
                        <th style="min-width:110px;">Unit Price</th>
                        <th style="min-width:110px;" class="text-end">Line Total</th>
                        <th style="min-width:150px;">Reason</th>
                        <th style="width:44px;"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <!-- Rows injected by JS / pre-populated for edit -->
                </tbody>
                <tfoot>
                    <tr style="background:#f7f8fc;">
                        <td colspan="4" class="text-end fw-semibold">Total Return Amount:</td>
                        <td class="text-end fw-bold" style="color:#FA896B;font-size:1rem;" id="grandTotal"><?= $cur ?> 0.00</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div id="emptyItemsMsg" class="text-center py-4 text-muted" style="font-size:.875rem;">
            <i class="bi bi-box-seam d-block mb-2" style="font-size:1.8rem;color:#E5EAF2;"></i>
            No items added yet. Click "Add Item" above.
        </div>
    </div>

    <!-- ── Submit ────────────────────────────────────────────────── -->
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Save GRV</button>
        <a href="<?= site_url('admin/grv') ?>" class="btn btn-light">Cancel</a>
    </div>
</form>

<!-- Product data for JS autocomplete -->
<script>
const PRODUCTS = <?= json_encode(array_map(fn($p) => [
    'un_id' => $p['un_id'],
    'name'  => $p['product_name'],
    'unit'  => $p['unit'] ?? 'pcs',
    'price' => (float) ($p['default_price'] ?? 0),
], $products ?? []), JSON_UNESCAPED_UNICODE) ?>;

const CUR_SYMBOL = '<?= esc($cur) ?>';
const EXISTING_ITEMS = <?= json_encode(array_map(fn($i) => [
    'product_un_id' => $i['product_un_id'] ?? '',
    'product_name'  => $i['product_name'],
    'unit'          => $i['unit'],
    'quantity'      => (float) $i['quantity'],
    'unit_price'    => (float) $i['unit_price'],
    'reason'        => $i['reason'] ?? '',
], $grv_items ?? []), JSON_UNESCAPED_UNICODE) ?>;

let rowIdx = 0;

function productDatalist() {
    return PRODUCTS.map(p => `<option value="${escHtml(p.name)}" data-un-id="${p.un_id}" data-unit="${escHtml(p.unit)}" data-price="${p.price}">`).join('');
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function addRow(data = {}) {
    const idx = rowIdx++;
    const listId = 'plist_' + idx;
    const tr = document.createElement('tr');
    tr.dataset.idx = idx;
    tr.innerHTML = `
        <td>
            <datalist id="${listId}">${productDatalist()}</datalist>
            <input type="hidden" name="item_product_un_id[]" class="fld-un-id" value="${escHtml(data.product_un_id||'')}">
            <input type="text" class="form-control form-control-sm fld-name" list="${listId}"
                   name="item_product_name[]" placeholder="Product name…"
                   value="${escHtml(data.product_name||'')}" autocomplete="off" required>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm fld-unit"
                   name="item_unit[]" value="${escHtml(data.unit||'pcs')}" style="width:80px;">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm fld-qty"
                   name="item_quantity[]" min="0.001" step="0.001"
                   value="${data.quantity||''}" required style="width:90px;">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm fld-price"
                   name="item_unit_price[]" min="0" step="0.01"
                   value="${data.unit_price||0}" style="width:100px;">
        </td>
        <td class="text-end fld-total fw-semibold" style="white-space:nowrap;">${CUR_SYMBOL} 0.00</td>
        <td>
            <input type="text" class="form-control form-control-sm"
                   name="item_reason[]" placeholder="Reason for return…"
                   value="${escHtml(data.reason||'')}">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-light text-danger btn-remove-row" title="Remove">
                <i class="bi bi-trash" style="font-size:.8rem;"></i>
            </button>
        </td>
    `;

    // Product autocomplete: when product name matches, fill unit + price + un_id
    const nameInput = tr.querySelector('.fld-name');
    nameInput.addEventListener('change', function () {
        const matched = PRODUCTS.find(p => p.name.toLowerCase() === this.value.toLowerCase());
        if (matched) {
            tr.querySelector('.fld-un-id').value  = matched.un_id;
            tr.querySelector('.fld-unit').value   = matched.unit;
            tr.querySelector('.fld-price').value  = matched.price;
        } else {
            tr.querySelector('.fld-un-id').value  = '';
        }
        recalcRow(tr);
        recalcTotal();
    });

    ['fld-qty', 'fld-price'].forEach(cls => {
        tr.querySelector('.' + cls).addEventListener('input', function () {
            recalcRow(tr);
            recalcTotal();
        });
    });

    tr.querySelector('.btn-remove-row').addEventListener('click', function () {
        tr.remove();
        recalcTotal();
        updateCount();
    });

    document.getElementById('itemsBody').appendChild(tr);
    recalcRow(tr);
    recalcTotal();
    updateCount();
}

function recalcRow(tr) {
    const qty   = parseFloat(tr.querySelector('.fld-qty').value)   || 0;
    const price = parseFloat(tr.querySelector('.fld-price').value) || 0;
    const total = qty * price;
    tr.querySelector('.fld-total').textContent = CUR_SYMBOL + ' ' + total.toFixed(2);
}

function recalcTotal() {
    let sum = 0;
    document.querySelectorAll('#itemsBody tr').forEach(tr => {
        const qty   = parseFloat(tr.querySelector('.fld-qty')?.value)   || 0;
        const price = parseFloat(tr.querySelector('.fld-price')?.value) || 0;
        sum += qty * price;
    });
    document.getElementById('grandTotal').textContent = CUR_SYMBOL + ' ' + sum.toFixed(2);
    const rows = document.querySelectorAll('#itemsBody tr').length;
    document.getElementById('emptyItemsMsg').style.display = rows > 0 ? 'none' : 'block';
}

function updateCount() {
    const n = document.querySelectorAll('#itemsBody tr').length;
    document.getElementById('itemCount').textContent = n;
}

document.getElementById('addItemBtn').addEventListener('click', () => addRow());

// Pre-populate existing items (for edit form)
EXISTING_ITEMS.forEach(item => addRow(item));
if (EXISTING_ITEMS.length === 0) {
    document.getElementById('emptyItemsMsg').style.display = 'block';
}

// Prevent submit with no items
document.getElementById('grvForm').addEventListener('submit', function (e) {
    if (document.querySelectorAll('#itemsBody tr').length === 0) {
        e.preventDefault();
        alert('Add at least one returned item before saving.');
    }
});
</script>

<?= $this->endSection() ?>
