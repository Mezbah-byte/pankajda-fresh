<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\CompanyRepository;
use App\Services\ContainerService;

class ContainerController extends BaseController
{
    private ContainerService $service;
    private CompanyRepository $companies;

    public function __construct()
    {
        $this->service   = new ContainerService();
        $this->companies = new CompanyRepository();
    }

    public function index()
    {
        $filters = [
            'q'              => $this->request->getGet('q'),
            'status'         => $this->request->getGet('status'),
            'customs_status' => $this->request->getGet('customs_status'),
        ];
        $grouped = $this->service->listGroupedByCompany($filters);
        return view('admin/containers/index', [
            'title'   => 'Containers',
            'grouped' => $grouped,
            'filters' => $filters,
            'totals'  => $this->service->totals(),
        ]);
    }

    public function create()
    {
        return view('admin/containers/form', [
            'title'     => 'Add Container',
            'container' => null,
            'companies' => $this->companies->search([], 1, 100)['items'],
            'action'    => site_url('admin/containers'),
        ]);
    }

    public function store()
    {
        if (! $this->validate(['container_number' => 'required|max_length[80]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $row = $this->service->create($this->request->getPost());
        return redirect()->to('admin/containers/' . $row['un_id'])->with('success', 'Container added.');
    }

    public function show(string $unId)
    {
        $container = $this->service->get($unId);
        if (! $container) return redirect()->to('admin/containers')->with('error', 'Container not found.');
        $company = $container['company_un_id'] ? $this->companies->findByUnId($container['company_un_id']) : null;
        return view('admin/containers/show', [
            'title'     => $container['container_number'],
            'container' => $container,
            'company'   => $company,
            'cartons'   => $this->service->cartonsFor($unId),
        ]);
    }

    public function storeCarton(string $unId)
    {
        if (! $this->validate([
            'carton_number' => 'max_length[80]',
            'quantity'      => 'permit_empty|decimal',
            'weight_gross'  => 'permit_empty|decimal',
            'weight_net'    => 'permit_empty|decimal',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->addCarton($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/containers/' . $unId)->with('success', 'Carton added.');
    }

    public function updateCarton(string $unId, string $cartonUnId)
    {
        if (! $this->validate([
            'carton_number' => 'max_length[80]',
            'quantity'      => 'permit_empty|decimal',
            'weight_gross'  => 'permit_empty|decimal',
            'weight_net'    => 'permit_empty|decimal',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->updateCarton($unId, $cartonUnId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/containers/' . $unId)->with('success', 'Carton updated.');
    }

    public function deleteCarton(string $unId, string $cartonUnId)
    {
        try {
            $this->service->deleteCarton($unId, $cartonUnId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/containers/' . $unId)->with('error', $e->getMessage());
        }
        return redirect()->to('admin/containers/' . $unId)->with('success', 'Carton deleted.');
    }

    public function edit(string $unId)
    {
        $container = $this->service->get($unId);
        if (! $container) return redirect()->to('admin/containers')->with('error', 'Container not found.');
        return view('admin/containers/form', [
            'title'     => 'Edit ' . $container['container_number'],
            'container' => $container,
            'companies' => $this->companies->search([], 1, 100)['items'],
            'action'    => site_url('admin/containers/' . $unId),
        ]);
    }

    public function update(string $unId)
    {
        if (! $this->validate(['container_number' => 'required|max_length[80]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->update($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/containers')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/containers/' . $unId)->with('success', 'Container updated.');
    }

    public function delete(string $unId)
    {
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/containers')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/containers')->with('success', 'Container deleted.');
    }
}
