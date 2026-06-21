<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mz-page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><?= esc($company['company_name']) ?></h4>
        <ul class="mz-breadcrumb">
            <li>Business</li>
            <li><a href="<?= site_url('admin/companies') ?>" class="text-muted text-decoration-none">Companies</a></li>
            <li><?= esc($company['company_name']) ?></li>
        </ul>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/companies/' . $company['un_id'] . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Edit</a>
        <a href="<?= site_url('admin/companies') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<?php if ($flash = session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-3"><?= esc($flash) ?></div>
<?php endif; ?>

<!-- ── Stats Cards ───────────────────────────────────────────────── -->
<?php $s = $stats ?? []; ?>
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:1.6rem;font-weight:700;color:var(--mz-primary);"><?= $s['customers'] ?? 0 ?></div>
            <div class="text-muted" style="font-size:.8rem;">Customers</div>
            <a href="<?= site_url('admin/customers?company_un_id=' . $company['un_id']) ?>" class="stretched-link"></a>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:1.6rem;font-weight:700;color:#49BEFF;"><?= $s['employees'] ?? 0 ?></div>
            <div class="text-muted" style="font-size:.8rem;">Employees</div>
            <a href="<?= site_url('admin/employees?company_un_id=' . $company['un_id']) ?>" class="stretched-link"></a>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:1.6rem;font-weight:700;color:#13DEB9;"><?= $s['visas'] ?? 0 ?></div>
            <div class="text-muted" style="font-size:.8rem;">Visas</div>
            <a href="<?= site_url('admin/visas?company_un_id=' . $company['un_id']) ?>" class="stretched-link"></a>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:1.6rem;font-weight:700;color:#FA896B;"><?= $s['containers'] ?? 0 ?></div>
            <div class="text-muted" style="font-size:.8rem;">Containers</div>
            <a href="<?= site_url('admin/containers?company_un_id=' . $company['un_id']) ?>" class="stretched-link"></a>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:1.4rem;font-weight:700;color:#5D87FF;">৳ <?= number_format($s['sales_total'] ?? 0, 0) ?></div>
            <div class="text-muted" style="font-size:.8rem;">Total Sales</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:1.4rem;font-weight:700;color:#FA896B;">৳ <?= number_format($s['sales_due'] ?? 0, 0) ?></div>
            <div class="text-muted" style="font-size:.8rem;">Outstanding Due</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:1.4rem;font-weight:700;color:#FFAE1F;">৳ <?= number_format($s['expenses_total'] ?? 0, 0) ?></div>
            <div class="text-muted" style="font-size:.8rem;">Total Expenses</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pd-card text-center py-3">
            <div style="font-size:1.6rem;font-weight:700;color:#13DEB9;"><?= $s['vendors'] ?? 0 ?></div>
            <div class="text-muted" style="font-size:.8rem;">Vendors</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- ── Left column ───────────────────────────────────────────── -->
    <div class="col-md-8">

        <!-- Company Profile -->
        <div class="pd-card">
            <div class="d-flex align-items-center gap-3 mb-4">
                <?php if (! empty($company['logo_path'])): ?>
                    <img src="<?= base_url($company['logo_path']) ?>" alt="Logo"
                         style="width:56px;height:56px;object-fit:contain;border-radius:8px;border:1px solid #eee;padding:4px;flex-shrink:0;">
                <?php else: ?>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-2 fw-bold"
                         style="width:56px;height:56px;background:linear-gradient(135deg,#5D87FF,#49BEFF);color:#fff;font-size:1.3rem;flex-shrink:0;">
                        <?= esc(strtoupper(substr($company['company_name'], 0, 1))) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h6 class="fw-bold m-0"><?= esc($company['company_name']) ?></h6>
                    <span class="text-muted" style="font-size:.8rem;"><?= esc($company['company_type'] ?? 'Business') ?> &middot; <?= esc(short_un_id($company['un_id'])) ?></span>
                </div>
            </div>

            <div class="row g-0">
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.73rem;text-transform:uppercase;letter-spacing:.5px;">Registration</h6>
                    <dl class="row mb-4" style="font-size:.875rem;">
                        <dt class="col-sm-5">Trade License</dt><dd class="col-sm-7"><?= esc($company['trade_license'] ?? '-') ?></dd>
                        <dt class="col-sm-5">Tax ID / VAT</dt><dd class="col-sm-7"><?= esc($company['tax_id'] ?? '-') ?></dd>
                        <dt class="col-sm-5">Established</dt><dd class="col-sm-7"><?= esc($company['established_date'] ?? '-') ?></dd>
                    </dl>
                    <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.73rem;text-transform:uppercase;letter-spacing:.5px;">Contact</h6>
                    <dl class="row mb-0" style="font-size:.875rem;">
                        <dt class="col-sm-5">Contact Person</dt><dd class="col-sm-7"><?= esc($company['contact_person'] ?? '-') ?></dd>
                        <dt class="col-sm-5">Phone</dt><dd class="col-sm-7"><?= esc($company['phone'] ?? '-') ?></dd>
                        <dt class="col-sm-5">Fax</dt><dd class="col-sm-7"><?= esc($company['fax'] ?? '-') ?></dd>
                        <dt class="col-sm-5">Email</dt><dd class="col-sm-7"><?= esc($company['email'] ?? '-') ?></dd>
                        <dt class="col-sm-5">Website</dt>
                        <dd class="col-sm-7">
                            <?php if (! empty($company['website'])): ?>
                                <a href="<?= esc($company['website']) ?>" target="_blank" rel="noopener"><?= esc($company['website']) ?></a>
                            <?php else: ?>-<?php endif; ?>
                        </dd>
                        <dt class="col-sm-5">Address</dt><dd class="col-sm-7"><?= esc(trim(($company['address'] ?? '') . ($company['city'] ? ', ' . $company['city'] : '') . ($company['country'] ? ', ' . $company['country'] : ''))) ?></dd>
                    </dl>
                </div>
                <div class="col-md-6 ps-md-4">
                    <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.73rem;text-transform:uppercase;letter-spacing:.5px;">Finance</h6>
                    <dl class="row mb-4" style="font-size:.875rem;">
                        <dt class="col-sm-5">Currency</dt><dd class="col-sm-7"><?= esc($company['currency'] ?? 'BDT') ?></dd>
                        <dt class="col-sm-5">Opening Bal.</dt><dd class="col-sm-7"><?= esc($company['currency'] ?? 'BDT') ?> <?= number_format((float) ($company['opening_balance'] ?? 0), 2) ?></dd>
                    </dl>
                    <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.73rem;text-transform:uppercase;letter-spacing:.5px;">Banking</h6>
                    <dl class="row mb-0" style="font-size:.875rem;">
                        <dt class="col-sm-5">Bank</dt><dd class="col-sm-7"><?= esc($company['bank_name'] ?? '-') ?></dd>
                        <dt class="col-sm-5">Account</dt><dd class="col-sm-7"><?= esc($company['bank_account'] ?? '-') ?></dd>
                        <dt class="col-sm-5">Routing</dt><dd class="col-sm-7"><?= esc($company['bank_routing'] ?? '-') ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <?php if (! empty($company['notes'])): ?>
        <div class="pd-card">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.73rem;text-transform:uppercase;letter-spacing:.5px;">Notes</h6>
            <p class="mb-0" style="font-size:.875rem;line-height:1.7;"><?= nl2br(esc($company['notes'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- Recent Customers -->
        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-people-fill me-2" style="color:var(--mz-primary);"></i>Recent Customers</h6>
                <a href="<?= site_url('admin/customers/create') ?>?company_un_id=<?= esc($company['un_id']) ?>" class="btn btn-sm btn-light"><i class="bi bi-plus-circle me-1"></i>Add</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle" style="font-size:.875rem;">
                    <thead><tr><th>Name</th><th>Phone</th><th>Email</th><th class="text-end">Due</th></tr></thead>
                    <tbody>
                        <?php if (empty($recent_customers)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">No customers yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_customers as $c): ?>
                                <tr>
                                    <td><a href="<?= site_url('admin/customers/' . $c['un_id']) ?>" class="text-decoration-none fw-semibold" style="color:var(--mz-primary);"><?= esc($c['customer_name']) ?></a></td>
                                    <td><?= esc($c['phone'] ?? '-') ?></td>
                                    <td><?= esc($c['email'] ?? '-') ?></td>
                                    <td class="text-end <?= (($c['current_due'] ?? 0) > 0) ? 'text-danger fw-semibold' : '' ?>">৳ <?= number_format((float) ($c['current_due'] ?? 0), 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-cart-check me-2" style="color:#13DEB9;"></i>Recent Sales</h6>
                <a href="<?= site_url('admin/sales/create') ?>?company_un_id=<?= esc($company['un_id']) ?>" class="btn btn-sm btn-light"><i class="bi bi-plus-circle me-1"></i>New Invoice</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle" style="font-size:.875rem;">
                    <thead><tr><th>Invoice No</th><th>Date</th><th class="text-end">Amount</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if (empty($recent_sales)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">No sales yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_sales as $s): ?>
                                <tr>
                                    <td><a href="<?= site_url('admin/sales/' . $s['un_id']) ?>" class="text-decoration-none fw-semibold" style="color:var(--mz-primary);"><?= esc($s['invoice_no']) ?></a></td>
                                    <td><?= esc($s['sale_date']) ?></td>
                                    <td class="text-end">৳ <?= number_format((float) $s['total_amount'], 2) ?></td>
                                    <td>
                                        <?php $ps = $s['payment_status'] ?? 'due'; ?>
                                        <span class="badge <?= $ps === 'paid' ? 'bg-success' : ($ps === 'partial' ? 'bg-warning text-dark' : 'bg-danger') ?>" style="font-size:.7rem;"><?= ucfirst($ps) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Employees -->
        <div class="pd-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-person-badge me-2" style="color:#49BEFF;"></i>Recent Employees</h6>
                <a href="<?= site_url('admin/employees/create') ?>?company_un_id=<?= esc($company['un_id']) ?>" class="btn btn-sm btn-light"><i class="bi bi-plus-circle me-1"></i>Add</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle" style="font-size:.875rem;">
                    <thead><tr><th>Name</th><th>Department</th><th>Designation</th><th>Phone</th></tr></thead>
                    <tbody>
                        <?php if (empty($recent_employees)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">No employees yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_employees as $e): ?>
                                <tr>
                                    <td><a href="<?= site_url('admin/employees/' . $e['un_id']) ?>" class="text-decoration-none fw-semibold" style="color:var(--mz-primary);"><?= esc($e['name'] ?? '-') ?></a></td>
                                    <td><?= esc($e['department'] ?? '-') ?></td>
                                    <td><?= esc($e['designation'] ?? '-') ?></td>
                                    <td><?= esc($e['phone'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ── Right column ───────────────────────────────────────────── -->
    <div class="col-md-4">
        <!-- Status card -->
        <div class="pd-card">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.73rem;text-transform:uppercase;letter-spacing:.5px;">Status</h6>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Status</span>
                <?php $st = $company['status'] ?? 'active'; ?>
                <?php if ($st === 'active'): ?>
                    <span class="badge-success-soft">Active</span>
                <?php elseif ($st === 'pending'): ?>
                    <span class="badge-warning-soft">Pending</span>
                <?php else: ?>
                    <span class="badge-secondary-soft"><?= esc(ucfirst($st)) ?></span>
                <?php endif; ?>
            </div>
            <div class="d-flex justify-content-between mb-2" style="font-size:.875rem;">
                <span class="text-muted">Created</span>
                <span><?= esc(substr($company['created_at'] ?? '-', 0, 10)) ?></span>
            </div>
            <div class="d-flex justify-content-between" style="font-size:.875rem;">
                <span class="text-muted">Updated</span>
                <span><?= esc(substr($company['updated_at'] ?? '-', 0, 10)) ?></span>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="pd-card">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.73rem;text-transform:uppercase;letter-spacing:.5px;">Quick Actions</h6>
            <div class="d-grid gap-2">
                <a href="<?= site_url('admin/customers/create') ?>?company_un_id=<?= esc($company['un_id']) ?>" class="btn btn-light text-start">
                    <i class="bi bi-person-plus me-2"></i>Add Customer
                </a>
                <a href="<?= site_url('admin/employees/create') ?>?company_un_id=<?= esc($company['un_id']) ?>" class="btn btn-light text-start">
                    <i class="bi bi-person-badge me-2"></i>Add Employee
                </a>
                <a href="<?= site_url('admin/sales/create') ?>?company_un_id=<?= esc($company['un_id']) ?>" class="btn btn-light text-start">
                    <i class="bi bi-receipt me-2"></i>New Invoice
                </a>
                <a href="<?= site_url('admin/visas/create') ?>?company_un_id=<?= esc($company['un_id']) ?>" class="btn btn-light text-start">
                    <i class="bi bi-passport me-2"></i>New Visa
                </a>
                <a href="<?= site_url('admin/containers/create') ?>?company_un_id=<?= esc($company['un_id']) ?>" class="btn btn-light text-start">
                    <i class="bi bi-box-seam me-2"></i>New Container
                </a>
                <a href="<?= site_url('admin/expenses/create') ?>?company_un_id=<?= esc($company['un_id']) ?>" class="btn btn-light text-start">
                    <i class="bi bi-cash-coin me-2"></i>Add Expense
                </a>
                <a href="<?= site_url('admin/reports/company-wise') ?>?company_un_id=<?= esc($company['un_id']) ?>" class="btn btn-light text-start">
                    <i class="bi bi-graph-up me-2"></i>Company Report
                </a>
            </div>
        </div>

        <!-- More Counts -->
        <div class="pd-card">
            <h6 class="fw-semibold mb-3" style="color:var(--mz-text-muted);font-size:.73rem;text-transform:uppercase;letter-spacing:.5px;">More</h6>
            <?php
            $moreStats = [
                ['label' => 'Farm Projects', 'val' => $s['farm_projects'] ?? 0, 'icon' => 'bi-tree', 'url' => site_url('admin/farm-projects?company_un_id=' . $company['un_id'])],
                ['label' => 'Vendors',       'val' => $s['vendors'] ?? 0,       'icon' => 'bi-truck', 'url' => site_url('admin/vendors?company_un_id=' . $company['un_id'])],
            ];
            ?>
            <?php foreach ($moreStats as $ms): ?>
                <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:.875rem;">
                    <span class="text-muted"><i class="bi <?= $ms['icon'] ?> me-1"></i><?= $ms['label'] ?></span>
                    <a href="<?= $ms['url'] ?>" class="fw-semibold text-decoration-none" style="color:var(--mz-primary);"><?= $ms['val'] ?></a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Delete -->
        <div class="pd-card border-danger" style="border:1px solid #FA896B;">
            <h6 class="fw-semibold mb-3" style="color:#FA896B;font-size:.73rem;text-transform:uppercase;letter-spacing:.5px;">Danger Zone</h6>
            <form method="post" action="<?= site_url('admin/companies/' . $company['un_id'] . '/delete') ?>"
                  onsubmit="return confirm('Delete this company and all linked records? This cannot be undone.');">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-outline-danger w-100"><i class="bi bi-trash me-2"></i>Delete Company</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
