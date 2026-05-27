<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Ledger — <?= esc($customer['name'] ?? '') ?></title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Arial,sans-serif; font-size:11pt; color:#333; padding:20px; }
h1 { font-size:16pt; font-weight:bold; }
h2 { font-size:13pt; color:#555; font-weight:normal; }
.header { border-bottom:2px solid #333; padding-bottom:12px; margin-bottom:16px; }
table { width:100%; border-collapse:collapse; margin-top:12px; }
th { background:#f2f6fa; border:1px solid #ddd; padding:6px 10px; text-align:left; font-size:10pt; }
td { border:1px solid #ddd; padding:6px 10px; font-size:10pt; }
.text-right { text-align:right; }
.credit { color:#1a8754; }
.debit { color:#dc3545; }
.balance-due { color:#dc3545; font-weight:bold; }
.balance-clear { color:#1a8754; font-weight:bold; }
.summary { background:#f9f9f9; border:1px solid #ddd; padding:12px 16px; margin-top:16px; border-radius:4px; }
.summary table { margin:0; }
.summary td { border:none; padding:4px 0; }
@media print { body { padding:0; } }
</style>
</head>
<body>
<div class="header">
    <h1>Customer Ledger Statement</h1>
    <h2><?= esc($customer['name'] ?? '') ?></h2>
    <div style="font-size:10pt;color:#666;margin-top:4px;">
        <?php if (!empty($customer['email'])): ?><?= esc($customer['email']) ?> | <?php endif; ?>
        <?php if (!empty($customer['phone'])): ?><?= esc($customer['phone']) ?> | <?php endif; ?>
        Generated: <?= date('d F Y') ?>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Description</th>
            <th class="text-right">Debit (৳)</th>
            <th class="text-right">Credit (৳)</th>
            <th class="text-right">Balance (৳)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (($ledger ?? []) as $row): ?>
            <tr>
                <td><?= esc($row['date'] ?? '') ?></td>
                <td><?= esc($row['description'] ?? '') ?></td>
                <td class="text-right <?= !empty($row['debit'])&&(float)$row['debit']>0?'debit':'' ?>">
                    <?= !empty($row['debit'])&&(float)$row['debit']>0 ? number_format((float)$row['debit'],2) : '-' ?>
                </td>
                <td class="text-right <?= !empty($row['credit'])&&(float)$row['credit']>0?'credit':'' ?>">
                    <?= !empty($row['credit'])&&(float)$row['credit']>0 ? number_format((float)$row['credit'],2) : '-' ?>
                </td>
                <td class="text-right <?= ((float)($row['balance']??0))>0?'balance-due':'balance-clear' ?>">
                    <?= number_format((float)($row['balance']??0),2) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="summary">
    <table>
        <tr><td style="width:60%"><strong>Total Sales</strong></td><td class="text-right debit">৳ <?= number_format((float)($summary['total_sales']??0),2) ?></td></tr>
        <tr><td><strong>Total Paid</strong></td><td class="text-right credit">৳ <?= number_format((float)($summary['total_paid']??0),2) ?></td></tr>
        <tr><td><strong>Balance Due</strong></td><td class="text-right <?= ((float)($summary['balance']??0))>0?'balance-due':'balance-clear' ?>">৳ <?= number_format((float)($summary['balance']??0),2) ?></td></tr>
    </table>
</div>

<script>window.onload = function() { window.print(); }</script>
</body>
</html>
