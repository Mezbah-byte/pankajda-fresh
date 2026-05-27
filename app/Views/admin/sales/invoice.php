<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice <?= esc($sale['invoice_no']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; color: #1f1d3a; padding: 30px; max-width: 920px; margin: 0 auto; }
        .invoice-head { display: flex; justify-content: space-between; align-items: start; padding-bottom: 22px; border-bottom: 2px solid #5e60ce; }
        .brand { font-weight: 800; font-size: 1.4rem; color: #5e60ce; }
        .label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #777; }
        .totals-table td { padding: 6px 12px; }
        .totals-table .total-row td { border-top: 2px solid #1f1d3a; font-weight: 700; font-size: 1.15rem; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="invoice-head">
    <div>
        <div class="brand"><?= esc($company['company_name'] ?? 'Pankaj Da Business') ?></div>
        <div class="small text-muted"><?= esc($company['address'] ?? 'Dhaka, Bangladesh') ?></div>
        <?php if (! empty($company['phone'])): ?><div class="small text-muted">Phone: <?= esc($company['phone']) ?></div><?php endif; ?>
    </div>
    <div class="text-end">
        <div class="label">Invoice</div>
        <div style="font-size: 1.3rem; font-weight: 700;"><?= esc($sale['invoice_no']) ?></div>
        <div class="label mt-2">Date</div>
        <div><?= esc($sale['sale_date']) ?></div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-6">
        <div class="label">Bill To</div>
        <div class="fw-bold"><?= esc($customer['customer_name'] ?? '-') ?></div>
        <div class="small text-muted">
            <?= esc($customer['phone'] ?? '') ?><br>
            <?= esc($customer['address'] ?? '') ?> <?= esc($customer['city'] ?? '') ?>
        </div>
    </div>
    <div class="col-6 text-end">
        <div class="label">Sale Type</div>
        <div><span class="badge bg-secondary"><?= esc(ucfirst($sale['sale_type'])) ?></span></div>
        <div class="label mt-2">Status</div>
        <?php $st = $sale['payment_status']; ?>
        <div><span class="badge <?= $st === 'paid' ? 'bg-success' : ($st === 'partial' ? 'bg-warning' : 'bg-danger') ?>"><?= esc(ucfirst($st)) ?></span></div>
    </div>
</div>

<table class="table mt-4">
    <thead style="background: #f7f8fc;">
        <tr>
            <th>#</th><th>Product / Service</th><th class="text-end">Qty</th><th>Unit</th><th class="text-end">Rate</th><th class="text-end">Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (($sale['items'] ?? []) as $i => $it): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= esc($it['product_name']) ?></td>
                <td class="text-end"><?= number_format((float) $it['quantity'], 2) ?></td>
                <td><?= esc($it['unit']) ?></td>
                <td class="text-end">৳ <?= number_format((float) $it['unit_price'], 2) ?></td>
                <td class="text-end fw-semibold">৳ <?= number_format((float) $it['total'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="row">
    <div class="col-7">
        <?php if (! empty($sale['notes'])): ?>
            <div class="label mt-3">Notes</div>
            <div class="text-muted"><?= nl2br(esc($sale['notes'])) ?></div>
        <?php endif; ?>
    </div>
    <div class="col-5">
        <table class="table table-sm totals-table mb-0">
            <tr><td class="text-muted">Subtotal</td><td class="text-end">৳ <?= number_format((float) $sale['subtotal'], 2) ?></td></tr>
            <tr><td class="text-muted">Discount</td><td class="text-end">− ৳ <?= number_format((float) $sale['discount'], 2) ?></td></tr>
            <tr><td class="text-muted">Tax</td><td class="text-end">+ ৳ <?= number_format((float) $sale['tax'], 2) ?></td></tr>
            <tr class="total-row"><td>Total</td><td class="text-end">৳ <?= number_format((float) $sale['total_amount'], 2) ?></td></tr>
            <tr><td class="text-muted">Paid</td><td class="text-end text-success">৳ <?= number_format((float) $sale['paid_amount'], 2) ?></td></tr>
            <tr><td class="text-muted">Due</td><td class="text-end text-danger fw-bold">৳ <?= number_format((float) $sale['due_amount'], 2) ?></td></tr>
        </table>
    </div>
</div>

<div class="text-center mt-5 text-muted small">Thank you for your business.</div>

<div class="text-center mt-4 no-print d-flex gap-2 justify-content-center flex-wrap">
    <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
    <a class="btn btn-success" id="waShareBtn" href="#"
       onclick="shareWhatsApp(); return false;" target="_blank">
        <i class="bi bi-whatsapp me-1"></i>Share on WhatsApp
    </a>
    <a class="btn btn-light" href="javascript:window.close()">Close</a>
</div>
<script>
function shareWhatsApp() {
    var invoiceNo = '<?= esc($sale['invoice_no']) ?>';
    var customer  = '<?= esc(addslashes($customer['name'] ?? '')) ?>';
    var amount    = '৳ <?= number_format((float)($sale['total_amount'] ?? 0), 2) ?>';
    var url       = window.location.href;
    var msg = 'Invoice ' + invoiceNo + ' for ' + customer + '\nAmount: ' + amount + '\nView: ' + url;
    window.open('https://wa.me/?text=' + encodeURIComponent(msg), '_blank');
}
</script>

</body>
</html>
