<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\CompanyService;

/**
 * Admin (web) Company controller. Renders Bootstrap views and submits
 * form posts back to the same controller. Reuses CompanyService.
 */
class CompanyController extends BaseController
{
    private CompanyService $service;

    public function __construct()
    {
        $this->service = new CompanyService();
    }

    public function index()
    {
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = [
            'q'      => $this->request->getGet('q'),
            'status' => $this->request->getGet('status'),
        ];
        $result = $this->service->list($filters, $page, 15);

        return view('admin/companies/index', [
            'title'      => 'Companies',
            'companies'  => $result['items'],
            'pagination' => [
                'page'      => $result['page'],
                'per_page'  => $result['per_page'],
                'total'     => $result['total'],
                'last_page' => max(1, (int) ceil($result['total'] / max(1, $result['per_page']))),
            ],
            'filters'    => $filters,
        ]);
    }

    public function create()
    {
        return view('admin/companies/form', [
            'title'   => 'Add Company',
            'company' => null,
            'action'  => site_url('admin/companies'),
        ]);
    }

    public function store()
    {
        $rules = [
            'company_name' => 'required|min_length[2]|max_length[200]',
            'email'        => 'permit_empty|valid_email',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        $this->service->create($this->request->getPost());
        return redirect()->to('admin/companies')->with('success', 'Company created.');
    }

    public function show(string $unId)
    {
        $company = $this->service->get($unId);
        if (! $company) {
            return redirect()->to('admin/companies')->with('error', 'Company not found.');
        }
        return view('admin/companies/show', [
            'title'   => $company['company_name'] ?? 'Company',
            'company' => $company,
        ]);
    }

    public function edit(string $unId)
    {
        $company = $this->service->get($unId);
        if (! $company) {
            return redirect()->to('admin/companies')->with('error', 'Company not found.');
        }
        return view('admin/companies/form', [
            'title'   => 'Edit ' . ($company['company_name'] ?? ''),
            'company' => $company,
            'action'  => site_url('admin/companies/' . $unId),
        ]);
    }

    public function update(string $unId)
    {
        $rules = [
            'company_name' => 'required|min_length[2]|max_length[200]',
            'email'        => 'permit_empty|valid_email',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->update($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/companies')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/companies/' . $unId)->with('success', 'Company updated.');
    }

    public function delete(string $unId)
    {
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/companies')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/companies')->with('success', 'Company deleted.');
    }
}
