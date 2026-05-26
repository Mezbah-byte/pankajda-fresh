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

$routes->get('setup/(:any)', 'Setup::index/$1');

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
    $routes->post('visas', 'Admin\VisaController::store');
    $routes->get('visas/(:segment)', 'Admin\VisaController::show/$1');
    $routes->get('visas/(:segment)/edit', 'Admin\VisaController::edit/$1');
    $routes->post('visas/(:segment)', 'Admin\VisaController::update/$1');
    $routes->post('visas/(:segment)/delete', 'Admin\VisaController::delete/$1');
    $routes->post('visas/(:segment)/payments', 'Admin\VisaController::addPayment/$1');

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

    // Notifications
    $routes->get('notifications',                              'Admin\NotificationController::index');
    $routes->post('notifications/(:segment)/read',            'Admin\NotificationController::markRead/$1');
    $routes->post('notifications/read-all',                   'Admin\NotificationController::markAllRead');
    $routes->post('notifications/(:segment)/dismiss',         'Admin\NotificationController::dismiss/$1');
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
});

// 404 fallback
$routes->set404Override(static function () {
    return view('errors/html/error_404');
});
