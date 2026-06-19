<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ----------------------------------------------------------------------
// Public website
// ----------------------------------------------------------------------
$routes->get('/', 'Web\HomeController::index');
$routes->get('about', 'Web\HomeController::about');
$routes->get('services', 'Web\HomeController::services');
$routes->get('companies', 'Web\HomeController::companies');
$routes->get('contact', 'Web\HomeController::contact');
$routes->post('contact', 'Web\HomeController::submitContact');

// ----------------------------------------------------------------------
// Auth (web)
// ----------------------------------------------------------------------
$routes->get('login', 'Web\AuthController::loginForm');
$routes->post('login', 'Web\AuthController::doLogin');
$routes->get('logout', 'Web\AuthController::logout');

// Password Reset
$routes->get('forgot-password',  'Web\PasswordResetController::forgot');
$routes->post('forgot-password', 'Web\PasswordResetController::sendLink');
$routes->get('reset-password',   'Web\PasswordResetController::reset');
$routes->post('reset-password',  'Web\PasswordResetController::doReset');

$routes->get('setup/(:any)', 'Setup::index/$1');
$routes->get('setup-migrate/(:any)', 'Setup::migrate/$1');

// ----------------------------------------------------------------------
// Admin Panel (web - session-protected)
// ----------------------------------------------------------------------
$routes->group('admin', ['filter' => 'webAuth'], static function ($routes) {
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('dashboard', 'Admin\DashboardController::index');

    // Companies
    $routes->get('companies', 'Admin\CompanyController::index');
    $routes->get('companies/create', 'Admin\CompanyController::create');
    $routes->post('companies', 'Admin\CompanyController::store');
    $routes->get('companies/(:segment)', 'Admin\CompanyController::show/$1');
    $routes->get('companies/(:segment)/edit', 'Admin\CompanyController::edit/$1');
    $routes->post('companies/(:segment)', 'Admin\CompanyController::update/$1');
    $routes->post('companies/(:segment)/delete', 'Admin\CompanyController::delete/$1');

    // Visas
    $routes->get('visas', 'Admin\VisaController::index');
    $routes->get('visas/create', 'Admin\VisaController::create');
    $routes->get('visas/pipeline', 'Admin\VisaPipelineController::index');
    $routes->post('visas', 'Admin\VisaController::store');
    $routes->get('visas/(:segment)', 'Admin\VisaController::show/$1');
    $routes->get('visas/(:segment)/edit', 'Admin\VisaController::edit/$1');
    $routes->post('visas/(:segment)', 'Admin\VisaController::update/$1');
    $routes->post('visas/(:segment)/delete', 'Admin\VisaController::delete/$1');
    $routes->post('visas/(:segment)/payments', 'Admin\VisaController::addPayment/$1');
    $routes->post('visas/(:segment)/extra-costs', 'Admin\VisaController::addExtraCost/$1');
    $routes->post('visas/(:segment)/extra-costs/(:segment)/delete', 'Admin\VisaController::deleteExtraCost/$1/$2');

    // Customers
    $routes->get('customers', 'Admin\CustomerController::index');
    $routes->get('customers/create', 'Admin\CustomerController::create');
    $routes->post('customers', 'Admin\CustomerController::store');
    $routes->get('customers/(:segment)', 'Admin\CustomerController::show/$1');
    $routes->get('customers/(:segment)/edit', 'Admin\CustomerController::edit/$1');
    $routes->post('customers/(:segment)', 'Admin\CustomerController::update/$1');
    $routes->post('customers/(:segment)/delete', 'Admin\CustomerController::delete/$1');

    // Sales
    $routes->get('sales', 'Admin\SaleController::index');
    $routes->get('sales/create', 'Admin\SaleController::create');
    $routes->post('sales', 'Admin\SaleController::store');
    $routes->get('sales/(:segment)', 'Admin\SaleController::show/$1');
    $routes->get('sales/(:segment)/invoice', 'Admin\SaleController::invoice/$1');
    $routes->get('sales/(:segment)/invoice/pdf', 'Admin\SaleController::invoicePdf/$1');
    $routes->post('sales/(:segment)/payments', 'Admin\SaleController::addPayment/$1');
    $routes->post('sales/(:segment)/delete', 'Admin\SaleController::delete/$1');

    // Containers
    $routes->get('containers', 'Admin\ContainerController::index');
    $routes->get('containers/create', 'Admin\ContainerController::create');
    $routes->post('containers', 'Admin\ContainerController::store');
    $routes->get('containers/(:segment)', 'Admin\ContainerController::show/$1');
    $routes->get('containers/(:segment)/edit', 'Admin\ContainerController::edit/$1');
    $routes->post('containers/(:segment)', 'Admin\ContainerController::update/$1');
    $routes->post('containers/(:segment)/delete', 'Admin\ContainerController::delete/$1');
    // Cartons (nested under containers)
    $routes->post('containers/(:segment)/cartons', 'Admin\ContainerController::storeCarton/$1');
    $routes->post('containers/(:segment)/cartons/(:segment)', 'Admin\ContainerController::updateCarton/$1/$2');
    $routes->post('containers/(:segment)/cartons/(:segment)/delete', 'Admin\ContainerController::deleteCarton/$1/$2');

    // Employees
    $routes->get('employees', 'Admin\EmployeeController::index');
    $routes->get('employees/create', 'Admin\EmployeeController::create');
    $routes->post('employees', 'Admin\EmployeeController::store');
    $routes->get('employees/(:segment)', 'Admin\EmployeeController::show/$1');
    $routes->get('employees/(:segment)/edit', 'Admin\EmployeeController::edit/$1');
    $routes->post('employees/(:segment)', 'Admin\EmployeeController::update/$1');
    $routes->post('employees/(:segment)/delete', 'Admin\EmployeeController::delete/$1');

    // Farm Projects
    $routes->get('farm-projects', 'Admin\FarmProjectController::index');
    $routes->get('farm-projects/create', 'Admin\FarmProjectController::create');
    $routes->post('farm-projects', 'Admin\FarmProjectController::store');
    $routes->get('farm-projects/(:segment)', 'Admin\FarmProjectController::show/$1');
    $routes->get('farm-projects/(:segment)/edit', 'Admin\FarmProjectController::edit/$1');
    $routes->post('farm-projects/(:segment)', 'Admin\FarmProjectController::update/$1');
    $routes->post('farm-projects/(:segment)/delete', 'Admin\FarmProjectController::delete/$1');
    $routes->post('farm-projects/(:segment)/activities', 'Admin\FarmProjectController::addActivity/$1');

    // Expenses
    $routes->get('expenses', 'Admin\ExpenseController::index');
    $routes->get('expenses/create', 'Admin\ExpenseController::create');
    $routes->post('expenses', 'Admin\ExpenseController::store');
    $routes->get('expenses/(:segment)', 'Admin\ExpenseController::show/$1');
    $routes->get('expenses/(:segment)/edit', 'Admin\ExpenseController::edit/$1');
    $routes->post('expenses/(:segment)', 'Admin\ExpenseController::update/$1');
    $routes->post('expenses/(:segment)/delete', 'Admin\ExpenseController::delete/$1');

    // Reports
    $routes->get('reports',                       'Admin\ReportController::index');
    $routes->get('reports/sales-daily',           'Admin\ReportController::salesDaily');
    $routes->get('reports/sales-monthly',         'Admin\ReportController::salesMonthly');
    $routes->get('reports/customer-dues',         'Admin\ReportController::customerDues');
    $routes->get('reports/expenses-by-category',  'Admin\ReportController::expensesByCategory');
    $routes->get('reports/profit-loss',           'Admin\ReportController::profitLoss');
    $routes->get('reports/company-wise',          'Admin\ReportController::companyWise');

    // Settings
    $routes->get('settings', 'Admin\SettingController::index');
    $routes->post('settings', 'Admin\SettingController::update');

    // Company Types
    $routes->get('company-types',                              'Admin\CompanyTypeController::index');
    $routes->post('company-types',                             'Admin\CompanyTypeController::store');
    $routes->post('company-types/(:segment)',                  'Admin\CompanyTypeController::update/$1');
    $routes->post('company-types/(:segment)/delete',           'Admin\CompanyTypeController::delete/$1');

    // Countries
    $routes->get('countries',                                  'Admin\CountryController::index');
    $routes->post('countries',                                 'Admin\CountryController::store');
    $routes->get('countries/(:segment)/edit',                  'Admin\CountryController::edit/$1');
    $routes->post('countries/(:segment)',                      'Admin\CountryController::update/$1');
    $routes->post('countries/(:segment)/delete',               'Admin\CountryController::delete/$1');
    $routes->post('countries/(:segment)/toggle',               'Admin\CountryController::toggleActive/$1');

    // Notifications
    $routes->get('notifications',                              'Admin\NotificationController::index');
    $routes->post('notifications/(:segment)/read',            'Admin\NotificationController::markRead/$1');
    $routes->post('notifications/read-all',                   'Admin\NotificationController::markAllRead');
    $routes->post('notifications/(:segment)/dismiss',         'Admin\NotificationController::dismiss/$1');

    // Users (admin)
    $routes->get('users',                                     'Admin\UserController::index');
    $routes->get('users/create',                              'Admin\UserController::create');
    $routes->post('users',                                    'Admin\UserController::store');
    $routes->get('users/(:segment)',                          'Admin\UserController::show/$1');
    $routes->get('users/(:segment)/edit',                     'Admin\UserController::edit/$1');
    $routes->post('users/(:segment)',                         'Admin\UserController::update/$1');
    $routes->post('users/(:segment)/delete',                  'Admin\UserController::delete/$1');

    // Products
    $routes->get('products',                                  'Admin\ProductController::index');
    $routes->get('products/create',                           'Admin\ProductController::create');
    $routes->post('products',                                 'Admin\ProductController::store');
    $routes->get('products/(:segment)/edit',                  'Admin\ProductController::edit/$1');
    $routes->post('products/(:segment)',                      'Admin\ProductController::update/$1');
    $routes->post('products/(:segment)/delete',               'Admin\ProductController::delete/$1');

    // Vendors
    $routes->get('vendors',                                   'Admin\VendorController::index');
    $routes->get('vendors/create',                            'Admin\VendorController::create');
    $routes->post('vendors',                                  'Admin\VendorController::store');
    $routes->get('vendors/(:segment)',                        'Admin\VendorController::show/$1');
    $routes->get('vendors/(:segment)/edit',                   'Admin\VendorController::edit/$1');
    $routes->post('vendors/(:segment)',                       'Admin\VendorController::update/$1');
    $routes->post('vendors/(:segment)/delete',                'Admin\VendorController::delete/$1');
    $routes->post('vendors/(:segment)/payment',               'Admin\VendorController::addPayment/$1');

    // Bank Accounts
    $routes->get('bank-accounts',                             'Admin\BankAccountController::index');
    $routes->get('bank-accounts/create',                      'Admin\BankAccountController::create');
    $routes->post('bank-accounts',                            'Admin\BankAccountController::store');
    $routes->get('bank-accounts/(:segment)',                  'Admin\BankAccountController::show/$1');
    $routes->get('bank-accounts/(:segment)/edit',             'Admin\BankAccountController::edit/$1');
    $routes->post('bank-accounts/(:segment)',                 'Admin\BankAccountController::update/$1');
    $routes->post('bank-accounts/(:segment)/delete',          'Admin\BankAccountController::delete/$1');
    $routes->post('bank-accounts/(:segment)/adjust',          'Admin\BankAccountController::adjust/$1');

    // Payroll
    $routes->get('payroll',                                   'Admin\PayrollController::index');
    $routes->get('payroll/create',                            'Admin\PayrollController::create');
    $routes->post('payroll',                                  'Admin\PayrollController::store');
    $routes->get('payroll/advances',                          'Admin\PayrollController::advances');
    $routes->post('payroll/advances',                         'Admin\PayrollController::addAdvance');
    $routes->get('payroll/(:segment)',                        'Admin\PayrollController::show/$1');
    $routes->post('payroll/(:segment)/paid',                  'Admin\PayrollController::markPaid/$1');
    $routes->post('payroll/(:segment)/delete',                'Admin\PayrollController::delete/$1');

    // Stock
    $routes->get('stock',                                     'Admin\StockController::index');
    $routes->get('stock/create',                              'Admin\StockController::create');
    $routes->post('stock',                                    'Admin\StockController::store');
    $routes->get('stock/(:segment)',                          'Admin\StockController::show/$1');
    $routes->get('stock/(:segment)/edit',                     'Admin\StockController::edit/$1');
    $routes->post('stock/(:segment)',                         'Admin\StockController::update/$1');
    $routes->post('stock/(:segment)/delete',                  'Admin\StockController::delete/$1');
    $routes->post('stock/(:segment)/in',                      'Admin\StockController::stockIn/$1');
    $routes->post('stock/(:segment)/out',                     'Admin\StockController::stockOut/$1');
    $routes->post('stock/(:segment)/adjust',                  'Admin\StockController::adjust/$1');

    // Visa Pipeline (GET already defined above visas/:segment for correct routing)
    $routes->post('visas/(:segment)/stage',                   'Admin\VisaPipelineController::addStage/$1');

    // Customer Ledger
    $routes->get('customers/(:segment)/ledger',               'Admin\CustomerLedgerController::show/$1');
    $routes->get('customers/(:segment)/ledger/print',         'Admin\CustomerLedgerController::print/$1');

    // GRV (Goods Return Vouchers)
    $routes->get('grv',                                       'Admin\GrvController::index');
    $routes->get('grv/create',                                'Admin\GrvController::create');
    $routes->post('grv',                                      'Admin\GrvController::store');
    $routes->get('grv/(:segment)',                            'Admin\GrvController::show/$1');
    $routes->get('grv/(:segment)/edit',                       'Admin\GrvController::edit/$1');
    $routes->post('grv/(:segment)',                           'Admin\GrvController::update/$1');
    $routes->post('grv/(:segment)/approve',                   'Admin\GrvController::approve/$1');
    $routes->post('grv/(:segment)/delete',                    'Admin\GrvController::delete/$1');

    // Purchases (vendor bills)
    $routes->get('purchases',                                 'Admin\PurchaseController::index');
    $routes->get('purchases/create',                          'Admin\PurchaseController::create');
    $routes->post('purchases',                                'Admin\PurchaseController::store');
    $routes->get('purchases/(:segment)',                      'Admin\PurchaseController::show/$1');
    $routes->post('purchases/(:segment)/receive',             'Admin\PurchaseController::receive/$1');
    $routes->post('purchases/(:segment)/delete',              'Admin\PurchaseController::delete/$1');

    // Activity Log
    $routes->get('activity-log',                              'Admin\ActivityLogController::index');

    // Import
    $routes->get('import',                                    'Admin\ImportController::index');
    $routes->post('import/upload',                            'Admin\ImportController::upload');
    $routes->get('import/template/(:segment)',                'Admin\ImportController::template/$1');
});

// ----------------------------------------------------------------------
// REST API v1
// ----------------------------------------------------------------------
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api'], static function ($routes) {

    // Auth (public)
    $routes->group('auth', static function ($routes) {
        $routes->post('login',    'AuthController::login',     ['filter' => 'rateLimit']);
        $routes->post('register', 'AuthController::register',  ['filter' => 'rateLimit']);
        $routes->post('refresh',  'AuthController::refresh');
        $routes->post('logout',   'AuthController::logout');
        $routes->get('me',        'AuthController::me');
    });

    // Companies
    $routes->resource('companies', [
        'controller' => 'CompanyController',
        'placeholder' => '(:segment)',
        'except' => 'new,edit',
    ]);

    // Visas
    $routes->resource('visas', [
        'controller' => 'VisaController',
        'placeholder' => '(:segment)',
        'except' => 'new,edit',
    ]);
    $routes->post('visas/(:segment)/payments', 'VisaController::addPayment/$1');

    // Containers
    $routes->resource('containers', [
        'controller' => 'ContainerController',
        'placeholder' => '(:segment)',
        'except' => 'new,edit',
    ]);

    // Customers
    $routes->resource('customers', [
        'controller' => 'CustomerController',
        'placeholder' => '(:segment)',
        'except' => 'new,edit',
    ]);

    // Sales / Invoices
    $routes->resource('sales', [
        'controller' => 'SaleController',
        'placeholder' => '(:segment)',
        'except' => 'new,edit',
    ]);
    $routes->post('sales/(:segment)/payments', 'SaleController::addPayment/$1');

    // Employees
    $routes->resource('employees', [
        'controller' => 'EmployeeController',
        'placeholder' => '(:segment)',
        'except' => 'new,edit',
    ]);

    // Farm Projects
    $routes->resource('farm-projects', [
        'controller' => 'FarmProjectController',
        'placeholder' => '(:segment)',
        'except' => 'new,edit',
    ]);
    $routes->post('farm-projects/(:segment)/activities', 'FarmProjectController::addActivity/$1');

    // Expenses
    $routes->get('expenses/categories', 'ExpenseController::categories');
    $routes->resource('expenses', [
        'controller' => 'ExpenseController',
        'placeholder' => '(:segment)',
        'except' => 'new,edit',
    ]);

    // Settings
    $routes->get('settings', 'SettingController::index');
    $routes->get('settings/(:segment)', 'SettingController::show/$1');
    $routes->put('settings', 'SettingController::update');

    // Reports
    $routes->group('reports', static function ($routes) {
        $routes->get('sales-daily',          'ReportController::salesDaily');
        $routes->get('sales-monthly',        'ReportController::salesMonthly');
        $routes->get('customer-dues',        'ReportController::customerDues');
        $routes->get('expenses-by-category', 'ReportController::expensesByCategory');
        $routes->get('profit-loss',          'ReportController::profitLoss');
        $routes->get('company-wise',         'ReportController::companyWise');
    });

    // Dashboard
    $routes->get('dashboard/stats', 'DashboardController::stats');

    // Notifications
    $routes->get('notifications',                              'NotificationController::index');
    $routes->get('notifications/unread',                      'NotificationController::unread');
    $routes->get('notifications/count',                       'NotificationController::count');
    $routes->put('notifications/(:segment)/read',             'NotificationController::markRead/$1');
    $routes->put('notifications/read-all',                    'NotificationController::markAllRead');
    $routes->delete('notifications/(:segment)',               'NotificationController::delete/$1');

    // Vendors
    $routes->get('vendors/totals',                            'VendorController::totals');
    $routes->resource('vendors', [
        'controller'  => 'VendorController',
        'placeholder' => '(:segment)',
        'except'      => 'new,edit',
    ]);
    $routes->post('vendors/(:segment)/payments',              'VendorController::addPayment/$1');
    $routes->get('vendors/(:segment)/payments',               'VendorController::payments/$1');

    // Bank Accounts
    $routes->get('bank-accounts/active',                      'BankAccountController::active');
    $routes->resource('bank-accounts', [
        'controller'  => 'BankAccountController',
        'placeholder' => '(:segment)',
        'except'      => 'new,edit',
    ]);
    $routes->post('bank-accounts/(:segment)/adjust',          'BankAccountController::adjust/$1');

    // Payroll
    $routes->get('payroll/summary',                           'PayrollController::summary');
    $routes->get('payroll/advances',                          'PayrollController::advances');
    $routes->post('payroll/advances',                         'PayrollController::addAdvance');
    $routes->get('payroll',                                   'PayrollController::index');
    $routes->post('payroll',                                  'PayrollController::create');
    $routes->get('payroll/(:segment)',                        'PayrollController::show/$1');
    $routes->post('payroll/(:segment)/paid',                  'PayrollController::markPaid/$1');
    $routes->delete('payroll/(:segment)',                     'PayrollController::delete/$1');

    // Stock
    $routes->get('stock/low-stock',                           'StockController::lowStock');
    $routes->get('stock/categories',                          'StockController::categories');
    $routes->get('stock/summary',                             'StockController::summary');
    $routes->resource('stock', [
        'controller'  => 'StockController',
        'placeholder' => '(:segment)',
        'except'      => 'new,edit',
    ]);
    $routes->post('stock/(:segment)/in',                      'StockController::stockIn/$1');
    $routes->post('stock/(:segment)/out',                     'StockController::stockOut/$1');
    $routes->post('stock/(:segment)/adjust',                  'StockController::adjust/$1');
    $routes->get('stock/(:segment)/transactions',             'StockController::transactions/$1');

    // Customer Ledger
    $routes->get('customers/(:segment)/ledger',               'CustomerLedgerController::show/$1');

    // Activity Log (read-only)
    $routes->get('activity-log',                              'ActivityLogController::index');
    $routes->get('activity-log/entity-types',                 'ActivityLogController::entityTypes');
});

// 404 fallback
$routes->set404Override(static function () {
    return view('errors/html/error_404');
});
