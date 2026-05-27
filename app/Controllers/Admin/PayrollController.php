<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\PayrollService;
use App\Repositories\EmployeeRepository;
use App\Repositories\CompanyRepository;

class PayrollController extends BaseController
{
    private PayrollService     $service;
    private EmployeeRepository $employees;
    private CompanyRepository  $companies;

    public function __construct()
    {
        $this->service   = new PayrollService();
        $this->employees = new EmployeeRepository();
        $this->companies = new CompanyRepository();
    }

    public function index()
    {
        $period  = $this->request->getGet('period') ?: date('Y-m');
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = [
            'pay_period'      => $period,
            'employee_un_id'  => $this->request->getGet('employee_un_id'),
            'status'          => $this->request->getGet('status'),
        ];
        $result  = $this->service->list($filters, $page, 20);
        $summary = $this->service->summary($period);

        return view('admin/payroll/index', [
            'title'      => 'Payroll',
            'records'    => $result['items'],
            'summary'    => $summary,
            'period'     => $period,
            'employees'  => $this->employees->search([], 1, 200)['items'],
            'pagination' => ['page' => $result['page'], 'per_page' => $result['per_page'], 'total' => $result['total'], 'last_page' => max(1, (int) ceil($result['total'] / $result['per_page']))],
            'filters'    => $filters,
        ]);
    }

    public function create()
    {
        return view('admin/payroll/form', [
            'title'     => 'Generate Payroll',
            'record'    => null,
            'employees' => $this->employees->search([], 1, 200)['items'],
            'companies' => $this->companies->search([], 1, 100)['items'],
            'action'    => site_url('admin/payroll'),
        ]);
    }

    public function store()
    {
        if (! $this->validate([
            'employee_un_id' => 'required',
            'pay_period'     => 'required|regex_match[/^\d{4}-\d{2}$/]',
            'basic_salary'   => 'required|numeric',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $record = $this->service->generate($this->request->getPost());
            return redirect()->to('admin/payroll/' . $record['un_id'])->with('success', 'Payroll generated.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('errors', [$e->getMessage()]);
        }
    }

    public function show(string $unId)
    {
        $record = $this->service->get($unId);
        if (! $record) return redirect()->to('admin/payroll')->with('error', 'Record not found.');
        $employee = $this->employees->findByUnId($record['employee_un_id']);
        $company  = $record['company_un_id'] ? $this->companies->findByUnId($record['company_un_id']) : null;
        return view('admin/payroll/show', [
            'title'    => 'Payroll Slip',
            'record'   => $record,
            'employee' => $employee,
            'company'  => $company,
        ]);
    }

    public function markPaid(string $unId)
    {
        try { $this->service->markPaid($unId, $this->request->getPost()); }
        catch (\InvalidArgumentException $e) { return redirect()->back()->with('error', $e->getMessage()); }
        return redirect()->to('admin/payroll/' . $unId)->with('success', 'Marked as paid.');
    }

    public function delete(string $unId)
    {
        try { $this->service->delete($unId); }
        catch (\InvalidArgumentException $e) { return redirect()->to('admin/payroll')->with('error', $e->getMessage()); }
        return redirect()->to('admin/payroll')->with('success', 'Record deleted.');
    }

    public function advances()
    {
        $empUnId = $this->request->getGet('employee_un_id');
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $result  = $this->service->advances($empUnId, $page, 20);
        return view('admin/payroll/advances', [
            'title'     => 'Employee Advances',
            'advances'  => $result['items'],
            'employees' => $this->employees->search([], 1, 200)['items'],
            'employee_un_id' => $empUnId,
            'pagination' => ['page' => $result['page'], 'per_page' => $result['per_page'], 'total' => $result['total'], 'last_page' => max(1, (int) ceil($result['total'] / $result['per_page']))],
        ]);
    }

    public function addAdvance()
    {
        if (! $this->validate(['employee_un_id' => 'required', 'amount' => 'required|numeric|greater_than[0]', 'advance_date' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $this->service->addAdvance($this->request->getPost());
        return redirect()->to('admin/payroll/advances')->with('success', 'Advance recorded.');
    }
}
