<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\CustomerService;
use App\Services\EmployeeService;
use App\Services\ExpenseService;
use App\Services\ProductService;
use App\Services\StockService;
use App\Services\VendorService;

class ImportController extends BaseController
{
    public function index()
    {
        return view('admin/import/index', [
            'title'   => 'Import Data',
            'modules' => [
                'customers' => 'Customers',
                'employees' => 'Employees',
                'products'  => 'Products',
                'stock'     => 'Stock Items',
                'vendors'   => 'Vendors',
                'expenses'  => 'Expenses',
            ],
        ]);
    }

    public function template(string $module)
    {
        $templates = [
            'customers' => ['name', 'email', 'phone', 'address', 'opening_balance'],
            'employees' => ['full_name', 'email', 'phone', 'department', 'designation', 'join_date', 'basic_salary'],
            'products'  => ['product_name', 'sku', 'category', 'unit', 'sale_price', 'cost_price', 'description'],
            'stock'     => ['item_name', 'category', 'unit', 'current_qty', 'min_qty', 'unit_cost'],
            'vendors'   => ['vendor_name', 'contact_person', 'email', 'phone', 'address'],
            'expenses'  => ['title', 'amount', 'category', 'expense_date', 'notes'],
        ];
        if (! isset($templates[$module])) {
            return redirect()->to('admin/import')->with('error', 'Unknown module.');
        }
        $headers = implode(',', $templates[$module]) . "\n";
        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $module . '_template.csv"')
            ->setBody($headers);
    }

    public function upload()
    {
        if (! $this->validate([
            'module' => 'required',
            'file'   => 'uploaded[file]|ext_in[file,csv]|max_size[file,2048]',
        ])) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $module = $this->request->getPost('module');
        $file   = $this->request->getFile('file');
        if (! $file->isValid()) {
            return redirect()->back()->with('error', 'Invalid file upload.');
        }

        $path = $file->getTempName();
        $rows = $this->parseCsv($path);
        if (empty($rows)) {
            return redirect()->back()->with('error', 'CSV is empty or malformed.');
        }

        [$imported, $skipped, $errors] = $this->processModule($module, $rows);

        return redirect()->to('admin/import')->with('success', "Import done. Imported: {$imported}, Skipped: {$skipped}.")
                         ->with('import_errors', $errors);
    }

    private function parseCsv(string $path): array
    {
        $rows    = [];
        $handle  = fopen($path, 'r');
        $headers = null;
        while (($row = fgetcsv($handle)) !== false) {
            if ($headers === null) {
                $headers = array_map('trim', $row);
                continue;
            }
            if (count($row) !== count($headers)) continue;
            $rows[] = array_combine($headers, array_map('trim', $row));
        }
        fclose($handle);
        return $rows;
    }

    private function processModule(string $module, array $rows): array
    {
        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        $serviceMap = [
            'customers' => CustomerService::class,
            'employees' => EmployeeService::class,
            'products'  => ProductService::class,
            'stock'     => StockService::class,
            'vendors'   => VendorService::class,
            'expenses'  => ExpenseService::class,
        ];

        if (! isset($serviceMap[$module])) {
            return [0, count($rows), ['Unknown module: ' . $module]];
        }

        $service = new ($serviceMap[$module])();

        foreach ($rows as $i => $row) {
            try {
                $service->create($row);
                $imported++;
            } catch (\Throwable $e) {
                $skipped++;
                $errors[] = 'Row ' . ($i + 2) . ': ' . $e->getMessage();
            }
        }
        return [$imported, $skipped, $errors];
    }
}
