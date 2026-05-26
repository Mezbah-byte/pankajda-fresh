<?php

namespace App\Controllers\Api;

use App\Services\ReportService;

class ReportController extends BaseApiController
{
    private ReportService $service;

    public function __construct()
    {
        $this->service = new ReportService();
    }

    /**
     * GET /api/v1/reports/sales-daily?date_from=&date_to=
     */
    public function salesDaily()
    {
        $from = $this->request->getGet('date_from');
        $to   = $this->request->getGet('date_to');
        $data = $this->service->salesDaily($from, $to);
        return $this->ok($data, 'Daily sales report');
    }

    /**
     * GET /api/v1/reports/sales-monthly?date_from=&date_to=
     */
    public function salesMonthly()
    {
        $from = $this->request->getGet('date_from');
        $to   = $this->request->getGet('date_to');
        $data = $this->service->salesMonthly($from, $to);
        return $this->ok($data, 'Monthly sales report');
    }

    /**
     * GET /api/v1/reports/customer-dues
     */
    public function customerDues()
    {
        $data = $this->service->customerDues();
        return $this->ok($data, 'Customer dues report');
    }

    /**
     * GET /api/v1/reports/expenses-by-category?date_from=&date_to=
     */
    public function expensesByCategory()
    {
        $from = $this->request->getGet('date_from');
        $to   = $this->request->getGet('date_to');
        $data = $this->service->expenseByCategory($from, $to);
        return $this->ok($data, 'Expenses by category report');
    }

    /**
     * GET /api/v1/reports/profit-loss?date_from=&date_to=
     */
    public function profitLoss()
    {
        $from = $this->request->getGet('date_from');
        $to   = $this->request->getGet('date_to');
        $data = $this->service->profitLoss($from, $to);
        return $this->ok($data, 'Profit & loss report');
    }

    /**
     * GET /api/v1/reports/company-wise
     */
    public function companyWise()
    {
        $data = $this->service->companyWise();
        return $this->ok($data, 'Company-wise report');
    }
}
