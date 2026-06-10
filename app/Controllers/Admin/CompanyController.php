<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\CompanyService;
use App\Services\CompanyTypeService;

class CompanyController extends BaseController
{
    private CompanyService $service;
    private CompanyTypeService $typeService;

    public function __construct()
    {
        $this->service     = new CompanyService();
        $this->typeService = new CompanyTypeService();
    }

    private function companyTypes(): array
    {
        $names = $this->typeService->names();
        return $names ?: ['Trading', 'Import / Export', 'Farm', 'Service', 'Manufacturing', 'Retail', 'Wholesale', 'Construction', 'Technology', 'Logistics', 'Other'];
    }

    public function index()
    {
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = [
            'q'            => $this->request->getGet('q'),
            'status'       => $this->request->getGet('status'),
            'company_type' => $this->request->getGet('company_type'),
        ];
        $result = $this->service->list($filters, $page, 15);

        return view('admin/companies/index', [
            'title'        => 'Companies',
            'companies'    => $result['items'],
            'pagination'   => [
                'page'      => $result['page'],
                'per_page'  => $result['per_page'],
                'total'     => $result['total'],
                'last_page' => max(1, (int) ceil($result['total'] / max(1, $result['per_page']))),
            ],
            'filters'      => $filters,
            'totals'       => $this->service->totals(),
            'company_types' => $this->companyTypes(),
        ]);
    }

    public function create()
    {
        return view('admin/companies/form', [
            'title'         => 'Add Company',
            'company'       => null,
            'action'        => site_url('admin/companies'),
            'company_types' => $this->companyTypes(),
        ]);
    }

    public function store()
    {
        $rules = [
            'company_name' => 'required|min_length[2]|max_length[200]',
            'email'        => 'permit_empty|valid_email',
            'logo'         => 'permit_empty|uploaded[logo]|is_image[logo]|max_size[logo,2048]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $data['logo_path'] = $this->handleLogoUpload($data['logo_path'] ?? null);

        $row = $this->service->create($data);
        return redirect()->to('admin/companies/' . $row['un_id'])->with('success', 'Company created.');
    }

    public function show(string $unId)
    {
        $company = $this->service->get($unId);
        if (! $company) {
            return redirect()->to('admin/companies')->with('error', 'Company not found.');
        }

        return view('admin/companies/show', [
            'title'            => $company['company_name'] ?? 'Company',
            'company'          => $company,
            'stats'            => $this->service->getStats($unId),
            'recent_customers' => $this->service->getRecentCustomers($unId, 5),
            'recent_employees' => $this->service->getRecentEmployees($unId, 5),
            'recent_sales'     => $this->service->getRecentSales($unId, 5),
        ]);
    }

    public function edit(string $unId)
    {
        $company = $this->service->get($unId);
        if (! $company) {
            return redirect()->to('admin/companies')->with('error', 'Company not found.');
        }
        return view('admin/companies/form', [
            'title'         => 'Edit ' . ($company['company_name'] ?? ''),
            'company'       => $company,
            'action'        => site_url('admin/companies/' . $unId),
            'company_types' => $this->companyTypes(),
        ]);
    }

    public function update(string $unId)
    {
        $rules = [
            'company_name' => 'required|min_length[2]|max_length[200]',
            'email'        => 'permit_empty|valid_email',
            'logo'         => 'permit_empty|uploaded[logo]|is_image[logo]|max_size[logo,2048]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        $existing = $this->service->get($unId);
        $newLogoPath = $this->handleLogoUpload($existing['logo_path'] ?? null);
        if ($newLogoPath !== null) {
            $data['logo_path'] = $newLogoPath;
        }

        try {
            $this->service->update($unId, $data);
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

    private function handleLogoUpload(?string $existingPath): ?string
    {
        $file = $this->request->getFile('logo');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $dir = FCPATH . 'uploads/companies/';
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($existingPath && file_exists(FCPATH . $existingPath)) {
            @unlink(FCPATH . $existingPath);
        }

        $newName = $file->getRandomName();
        $file->move($dir, $newName);

        return 'uploads/companies/' . $newName;
    }
}
