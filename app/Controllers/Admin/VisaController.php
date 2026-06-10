<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\CompanyRepository;
use App\Services\VisaService;
use App\Services\VisaPipelineService;
use App\Services\CountryService;
use App\Repositories\VisaRepository;

class VisaController extends BaseController
{
    private VisaService $service;
    private CompanyRepository $companies;
    private CountryService $countryService;

    public function __construct()
    {
        $this->service        = new VisaService();
        $this->companies      = new CompanyRepository();
        $this->countryService = new CountryService();
    }

    private function countries(): array
    {
        $names = $this->countryService->names();
        return $names ?: ['Bangladesh', 'Saudi Arabia', 'UAE', 'Qatar', 'Kuwait', 'Bahrain', 'Oman', 'Malaysia', 'Singapore'];
    }

    public function index()
    {
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = [
            'q'              => $this->request->getGet('q'),
            'company_un_id'  => $this->request->getGet('company_un_id'),
            'payment_status' => $this->request->getGet('payment_status'),
            'status'         => $this->request->getGet('status'),
        ];
        $result    = $this->service->list($filters, $page, 15);
        $companies = $this->companies->search([], 1, 100)['items'];
        $pipeline  = new VisaPipelineService();
        return view('admin/visas/index', [
            'title'       => 'Visas',
            'visas'       => $result['items'],
            'companies'   => $companies,
            'pagination'  => [
                'page'      => $result['page'],
                'per_page'  => $result['per_page'],
                'total'     => $result['total'],
                'last_page' => max(1, (int) ceil($result['total'] / max(1, $result['per_page']))),
            ],
            'filters'     => $filters,
            'totals'      => $this->service->totals(),
            'stage_counts' => $pipeline->pipeline(),
            'stages_list'  => VisaPipelineService::STAGES,
        ]);
    }

    public function create()
    {
        return view('admin/visas/form', [
            'title'     => 'Add Visa',
            'visa'      => null,
            'companies' => $this->companies->search([], 1, 100)['items'],
            'countries' => $this->countries(),
            'action'    => site_url('admin/visas'),
        ]);
    }

    public function store()
    {
        if (! $this->validate([
            'visa_name'     => 'required|min_length[2]|max_length[200]',
            'company_un_id' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $row = $this->service->create($this->request->getPost());
        return redirect()->to('admin/visas/' . $row['un_id'])->with('success', 'Visa added.');
    }

    public function show(string $unId)
    {
        $visa = $this->service->get($unId);
        if (! $visa) return redirect()->to('admin/visas')->with('error', 'Visa not found.');
        $payments   = $this->service->paymentsFor($unId);
        $extraCosts = $this->service->extraCostsFor($unId);
        $company    = $this->companies->findByUnId($visa['company_un_id'] ?? '');
        $pipeline   = new VisaPipelineService();
        return view('admin/visas/show', [
            'title'        => $visa['visa_name'],
            'visa'         => $visa,
            'payments'     => $payments,
            'extra_costs'  => $extraCosts,
            'company'      => $company,
            'stages'       => $pipeline->stagesFor($unId),
            'stages_list'  => VisaPipelineService::STAGES,
        ]);
    }

    public function addExtraCost(string $unId)
    {
        if (! $this->validate([
            'description' => 'required|max_length[200]',
            'amount'      => 'required|numeric|greater_than[0]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->addExtraCost($unId, $this->request->getPost());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/visas/' . $unId)->with('success', 'Extra cost added.');
    }

    public function deleteExtraCost(string $unId, string $costUnId)
    {
        try {
            $this->service->deleteExtraCost($unId, $costUnId);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/visas/' . $unId)->with('success', 'Extra cost removed.');
    }

    public function edit(string $unId)
    {
        $visa = $this->service->get($unId);
        if (! $visa) return redirect()->to('admin/visas')->with('error', 'Visa not found.');
        return view('admin/visas/form', [
            'title'     => 'Edit ' . $visa['visa_name'],
            'visa'      => $visa,
            'companies' => $this->companies->search([], 1, 100)['items'],
            'countries' => $this->countries(),
            'action'    => site_url('admin/visas/' . $unId),
        ]);
    }

    public function update(string $unId)
    {
        if (! $this->validate(['visa_name' => 'required|min_length[2]|max_length[200]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->update($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/visas')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/visas/' . $unId)->with('success', 'Visa updated.');
    }

    public function delete(string $unId)
    {
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/visas')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/visas')->with('success', 'Visa deleted.');
    }

    public function addPayment(string $unId)
    {
        if (! $this->validate(['amount' => 'required|numeric|greater_than[0]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->addPayment($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/visas/' . $unId)->with('success', 'Payment recorded.');
    }
}
