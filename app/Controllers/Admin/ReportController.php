<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\ReportService;

class ReportController extends BaseController
{
    private ReportService $service;

    public function __construct()
    {
        $this->service = new ReportService();
    }

    /**
     * Reports landing page - links to each report.
     */
    public function index()
    {
        return view('admin/reports/index', ['title' => 'Reports']);
    }

    public function salesDaily()
    {
        $from = $this->request->getGet('from');
        $to   = $this->request->getGet('to');
        $data = $this->service->salesDaily($from, $to);

        if ($this->request->getGet('export') === 'csv') {
            return $this->csv(
                $data['rows'],
                ['sale_date', 'invoices', 'total', 'paid', 'due'],
                'sales-daily-' . date('Y-m-d') . '.csv'
            );
        }

        return view('admin/reports/sales_daily', [
            'title' => 'Daily Sales Report',
            'data'  => $data,
        ]);
    }

    public function salesMonthly()
    {
        $data = $this->service->salesMonthly(
            $this->request->getGet('from'),
            $this->request->getGet('to')
        );
        if ($this->request->getGet('export') === 'csv') {
            return $this->csv($data['rows'],
                ['month', 'invoices', 'total', 'paid', 'due'],
                'sales-monthly-' . date('Y-m-d') . '.csv');
        }
        return view('admin/reports/sales_monthly', [
            'title' => 'Monthly Sales Report',
            'data'  => $data,
        ]);
    }

    public function customerDues()
    {
        $data = $this->service->customerDues();
        if ($this->request->getGet('export') === 'csv') {
            return $this->csv($data['rows'],
                ['customer_name', 'phone', 'city', 'current_due', 'credit_limit'],
                'customer-dues-' . date('Y-m-d') . '.csv');
        }
        return view('admin/reports/customer_dues', [
            'title' => 'Customer Dues Report',
            'data'  => $data,
        ]);
    }

    public function expensesByCategory()
    {
        $data = $this->service->expenseByCategory(
            $this->request->getGet('from'),
            $this->request->getGet('to')
        );
        if ($this->request->getGet('export') === 'csv') {
            return $this->csv($data['rows'],
                ['category', 'count', 'total'],
                'expenses-by-category-' . date('Y-m-d') . '.csv');
        }
        return view('admin/reports/expenses_by_category', [
            'title' => 'Expenses by Category',
            'data'  => $data,
        ]);
    }

    public function profitLoss()
    {
        $data = $this->service->profitLoss(
            $this->request->getGet('from'),
            $this->request->getGet('to')
        );
        return view('admin/reports/profit_loss', [
            'title' => 'Profit / Loss Report',
            'data'  => $data,
        ]);
    }

    public function companyWise()
    {
        $data = $this->service->companyWise();
        if ($this->request->getGet('export') === 'csv') {
            return $this->csv($data['rows'],
                ['company_name', 'sales', 'sales_due', 'expenses', 'visa_cost', 'visa_due', 'net'],
                'company-wise-' . date('Y-m-d') . '.csv');
        }
        return view('admin/reports/company_wise', [
            'title' => 'Company-wise Report',
            'data'  => $data,
        ]);
    }

    private function csv(array $rows, array $cols, string $filename)
    {
        $csv = $this->service->rowsToCsv($rows, $cols, $filename);
        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }
}
