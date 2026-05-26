<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\CompanyRepository;
use App\Services\FarmProjectService;

class FarmProjectController extends BaseController
{
    private FarmProjectService $service;
    private CompanyRepository $companies;

    public function __construct()
    {
        $this->service   = new FarmProjectService();
        $this->companies = new CompanyRepository();
    }

    public function index()
    {
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = [
            'q'             => $this->request->getGet('q'),
            'company_un_id' => $this->request->getGet('company_un_id'),
            'status'        => $this->request->getGet('status'),
        ];
        $result = $this->service->list($filters, $page, 15);
        return view('admin/farm_projects/index', [
            'title'      => 'Farm Projects',
            'projects'   => $result['items'],
            'companies'  => $this->companies->search([], 1, 100)['items'],
            'pagination' => [
                'page'      => $result['page'],
                'per_page'  => $result['per_page'],
                'total'     => $result['total'],
                'last_page' => max(1, (int) ceil($result['total'] / max(1, $result['per_page']))),
            ],
            'filters'    => $filters,
            'totals'     => $this->service->totals(),
        ]);
    }

    public function create()
    {
        return view('admin/farm_projects/form', [
            'title'     => 'New Farm Project',
            'project'   => null,
            'companies' => $this->companies->search([], 1, 100)['items'],
            'action'    => site_url('admin/farm-projects'),
        ]);
    }

    public function store()
    {
        if (! $this->validate(['project_name' => 'required|min_length[2]|max_length[200]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $row = $this->service->create($this->request->getPost());
        return redirect()->to('admin/farm-projects/' . $row['un_id'])->with('success', 'Farm project created.');
    }

    public function show(string $unId)
    {
        $project = $this->service->get($unId);
        if (! $project) return redirect()->to('admin/farm-projects')->with('error', 'Farm project not found.');
        $company = $project['company_un_id'] ? $this->companies->findByUnId($project['company_un_id']) : null;
        return view('admin/farm_projects/show', [
            'title'   => $project['project_name'],
            'project' => $project,
            'company' => $company,
        ]);
    }

    public function edit(string $unId)
    {
        $project = $this->service->get($unId);
        if (! $project) return redirect()->to('admin/farm-projects')->with('error', 'Farm project not found.');
        return view('admin/farm_projects/form', [
            'title'     => 'Edit ' . $project['project_name'],
            'project'   => $project,
            'companies' => $this->companies->search([], 1, 100)['items'],
            'action'    => site_url('admin/farm-projects/' . $unId),
        ]);
    }

    public function update(string $unId)
    {
        if (! $this->validate(['project_name' => 'required|min_length[2]|max_length[200]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->update($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/farm-projects')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/farm-projects/' . $unId)->with('success', 'Farm project updated.');
    }

    public function delete(string $unId)
    {
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/farm-projects')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/farm-projects')->with('success', 'Farm project deleted.');
    }

    public function addActivity(string $unId)
    {
        try {
            $this->service->addActivity($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/farm-projects/' . $unId)->with('success', 'Activity recorded.');
    }
}
