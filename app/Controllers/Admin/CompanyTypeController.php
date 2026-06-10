<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\CompanyTypeService;

class CompanyTypeController extends BaseController
{
    private CompanyTypeService $service;

    public function __construct()
    {
        $this->service = new CompanyTypeService();
    }

    public function index()
    {
        return view('admin/company_types/index', [
            'title' => 'Company Types',
            'types' => $this->service->all(),
        ]);
    }

    public function store()
    {
        if (! $this->validate(['name' => 'required|min_length[2]|max_length[100]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $this->service->create($this->request->getPost());
        return redirect()->to('admin/company-types')->with('success', 'Company type added.');
    }

    public function update(string $unId)
    {
        if (! $this->validate(['name' => 'required|min_length[2]|max_length[100]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->update($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/company-types')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/company-types')->with('success', 'Company type updated.');
    }

    public function delete(string $unId)
    {
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/company-types')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/company-types')->with('success', 'Company type deleted.');
    }
}
