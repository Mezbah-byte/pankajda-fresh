<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\CompanyRepository;
use App\Services\EmployeeService;

class EmployeeController extends BaseController
{
    private EmployeeService $service;
    private CompanyRepository $companies;

    public function __construct()
    {
        $this->service   = new EmployeeService();
        $this->companies = new CompanyRepository();
    }

    public function index()
    {
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = [
            'q'             => $this->request->getGet('q'),
            'company_un_id' => $this->request->getGet('company_un_id'),
            'department'    => $this->request->getGet('department'),
            'status'        => $this->request->getGet('status'),
        ];
        $result = $this->service->list($filters, $page, 15);
        return view('admin/employees/index', [
            'title'      => 'Employees',
            'employees'  => $result['items'],
            'companies'  => $this->companies->search([], 1, 100)['items'],
            'pagination' => $this->paginationMeta($result),
            'filters'    => $filters,
            'totals'     => $this->service->totals(),
        ]);
    }

    public function create()
    {
        return view('admin/employees/form', [
            'title'     => 'Add Employee',
            'employee'  => null,
            'companies' => $this->companies->search([], 1, 100)['items'],
            'action'    => site_url('admin/employees'),
        ]);
    }

    public function store()
    {
        if (! $this->validate(['name' => 'required|min_length[2]|max_length[150]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $row = $this->service->create($this->request->getPost());
        return redirect()->to('admin/employees/' . $row['un_id'])->with('success', 'Employee added.');
    }

    public function show(string $unId)
    {
        $employee = $this->service->get($unId);
        if (! $employee) return redirect()->to('admin/employees')->with('error', 'Employee not found.');
        $company = $employee['company_un_id'] ? $this->companies->findByUnId($employee['company_un_id']) : null;
        return view('admin/employees/show', [
            'title'    => $employee['name'],
            'employee' => $employee,
            'company'  => $company,
        ]);
    }

    public function edit(string $unId)
    {
        $employee = $this->service->get($unId);
        if (! $employee) return redirect()->to('admin/employees')->with('error', 'Employee not found.');
        return view('admin/employees/form', [
            'title'     => 'Edit ' . $employee['name'],
            'employee'  => $employee,
            'companies' => $this->companies->search([], 1, 100)['items'],
            'action'    => site_url('admin/employees/' . $unId),
        ]);
    }

    public function update(string $unId)
    {
        if (! $this->validate(['name' => 'required|min_length[2]|max_length[150]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->update($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/employees')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/employees/' . $unId)->with('success', 'Employee updated.');
    }

    public function delete(string $unId)
    {
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/employees')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/employees')->with('success', 'Employee deleted.');
    }

    private function paginationMeta(array $result): array
    {
        return [
            'page'      => $result['page'],
            'per_page'  => $result['per_page'],
            'total'     => $result['total'],
            'last_page' => max(1, (int) ceil($result['total'] / max(1, $result['per_page']))),
        ];
    }
}
