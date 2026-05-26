<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice <?= esc($sale['invoice_no']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #1f1d3a;
            padding: 30px;
        }
        .invoice-head {
            display: table;
            width: 100%;
            padding-bottom: 18px;
            border-bottom: 2px solid #5e60ce;
            margin-bottom: 18px;
        }
        .invoice-head .left  { display: table-cell; width: 60%; vertical-align: top; }
        .invoice-head .right { display: table-cell; width: 40%; vertical-align: top; text-align: right; }
        .brand { font-size: 18px; font-weight: 700; color: #5e60ce; margin-bottom: 4px; }
        .label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #777; margin-top: 10px; margin-bottom: 2px; }
        .bill-row { display: table; width: 100%; margin: 16px 0; }
        .bill-left  { display: table-cell; width: 55%; vertical-align: top; }
        .bill-right { display: table-cell; width: 45%; vertical-align: top; text-align: right; }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }
        table.items thead tr { background: #f7f8fc; }
        table.items th, table.items td { padding: 8px 10px; border-bottom: 1px solid #e9ecef; }
        table.items th { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #555; font-weight: 600; }
        .text-right { text-align: right; }
        .totals { width: 45%; float: right; margin-top: 8px; }
        .totals table { width: 100%; }
        .totals td { padding: 5px 10px; }
        .totals .grand td { border-top: 2px solid #1f1d3a; font-weight: 700; font-size: 13px; padding-top: 8px; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            color: #fff;
        }
        .badge-paid     { background: #2ec4b6; }
        .badge-partial  { background: #f6a623; }
        .badge-due      { background: #e74c3c; }
        .badge-cash     { background: #5e60ce; }
        .badge-credit   { background: #6930c3; }
        .footer { margin-top: 40px; font-size: 10px; color: #999; text-align: center; border-top: 1px solid #e9ecef; padding-top: 12px; }
        .clearfix::after { content: ''; display: table; clear: both; }
    </style>
</head>
<body>

<div class="invoice-head">
    <div class="left">
        <div class="brand"><?= esc($company['company_name'] ?? 'Pankaj Da Business') ?></div>
        <div style="color:#555;"><?= esc($company['address'] ?? 'Dhaka, Bangladesh') ?></div>
        <?php if (! empty($company['phone'])): ?>
            <div style="color:#555;">Phone: <?= esc($company['phone']) ?></div>
        <?php endif; ?>
    </div>
    <div class="right">
        <div class="label">Invoice No.</div>
        <div style="font-size:16px; font-weight:700;"><?= esc($sale['invoice_no']) ?></div>
        <div class="label">Date</div>
        <div><?= esc($sale['sale_date']) ?></div>
    </div>
</div>

<div class="bill-row">
    <div class="bill-left">
        <div class="label">Bill To</div>
        <div style="font-weight:700;"><?= esc($customer['customer_name'] ?? '-') ?></div>
        <?php if (! empty($customer['phone'])): ?>
            <div style="color:#555;"><?= esc($customer['phone']) ?></div>
        <?php endif; ?>
        <?php if (! empty($customer['address'])): ?>
            <div style="color:#555;"><?= esc($customer['address']) ?><?php if (!empty($customer['city'])): ?>, <?= esc($customer['city']) ?><?php endif; ?></div>
        <?php endif; ?>
    </div>
    <div class="bill-right">
        <div class="label">Sale Type</div>
        <div>
            <span class="badge badge-<?= esc($sale['sale_type']) ?>"><?= esc(ucfirst($sale['sale_type'])) ?></span>
        </div>
        <div class="label">Payment Status</div>
        <?php $st = $sale['payment_status'] ?? 'due'; ?>
        <div>
            <span class="badge badge-<?= esc($st) ?>"><?= esc(ucfirst($st)) ?></span>
        </div>
    </div>
</div>

<table class="items">
    <thead>
        <tr>
            <th>#</th>
            <th>Product / Service</th>
            <th class="text-right">Qty</th>
            <th>Unit</th>
            <th class="text-right">Unit Price</th>
            <th class="text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (($sale['items'] ?? []) as $i => $it): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($it['product_name']) ?></td>
            <td class="text-right"><?= number_format((float) $it['quantity'], 2) ?></td>
            <td><?= esc($it['unit']) ?></td>
            <td class="text-right">&#x9F3; <?= number_format((float) $it['unit_price'], 2) ?></td>
            <td class="text-right" style="font-weight:600;">&#x9F3; <?= number_format((float) $it['total'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="clearfix">
    <?php if (! empty($sale['notes'])): ?>
        <div style="width:50%;">
            <div class="label">Notes</div>
            <div style="color:#555;"><?= nl2br(esc($sale['notes'])) ?></div>
        </div>
    <?php endif; ?>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal</td>
                <td class="text-right">&#x9F3; <?= number_format((float) ($sale['subtotal'] ?? $sale['total_amount']), 2) ?></td>
            </tr>
            <?php if (! empty($sale['discount_amount']) && (float) $sale['discount_amount'] > 0): ?>
            <tr>
                <td>Discount</td>
                <td class="text-right" style="color:#e74c3c;">- &#x9F3; <?= number_format((float) $sale['discount_amount'], 2) ?></td>
            </tr>
            <?php endif; ?>
            <?php if (! empty($sale['tax_amount']) && (float) $sale['tax_amount'] > 0): ?>
            <tr>
                <td>Tax</td>
                <td class="text-right">+ &#x9F3; <?= number_format((float) $sale['tax_amount'], 2) ?></td>
            </tr>
            <?php endif; ?>
            <tr class="grand">
                <td>Total</td>
                <td class="text-right">&#x9F3; <?= number_format((float) $sale['total_amount'], 2) ?></td>
            </tr>
            <tr>
                <td style="color:#2ec4b6;">Amount Paid</td>
                <td class="text-right" style="color:#2ec4b6;">&#x9F3; <?= number_format((float) ($sale['paid_amount'] ?? 0), 2) ?></td>
            </tr>
            <?php $due = (float)($sale['total_amount'] ?? 0) - (float)($sale['paid_amount'] ?? 0); ?>
            <?php if ($due > 0): ?>
            <tr>
                <td style="color:#e74c3c;">Balance Due</td>
                <td class="text-right" style="color:#e74c3c; font-weight:700;">&#x9F3; <?= number_format($due, 2) ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<div class="footer">
    Generated by Pankaj Da ERP &middot; <?= date('d M Y H:i') ?> &middot; Thank you for your business!
</div>

</body>
</html>
