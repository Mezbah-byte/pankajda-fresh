<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\CountryService;

class CountryController extends BaseController
{
    private CountryService $service;

    public function __construct()
    {
        $this->service = new CountryService();
    }

    public function index()
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = [
            'q'         => $this->request->getGet('q'),
            'is_active' => $this->request->getGet('is_active'),
        ];
        $result = $this->service->list($filters, $page, 30);
        return view('admin/countries/index', [
            'title'      => 'Countries',
            'countries'  => $result['items'],
            'filters'    => $filters,
            'pagination' => [
                'page'      => $result['page'],
                'per_page'  => $result['per_page'],
                'total'     => $result['total'],
                'last_page' => max(1, (int) ceil($result['total'] / max(1, $result['per_page']))),
            ],
        ]);
    }

    public function store()
    {
        if (! $this->validate(['name' => 'required|min_length[2]|max_length[100]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $this->service->create($this->request->getPost());
        return redirect()->to('admin/countries')->with('success', 'Country added.');
    }

    public function edit(string $unId)
    {
        $country = $this->service->get($unId);
        if (! $country) {
            return redirect()->to('admin/countries')->with('error', 'Country not found.');
        }
        return view('admin/countries/edit', [
            'title'   => 'Edit ' . $country['name'],
            'country' => $country,
        ]);
    }

    public function update(string $unId)
    {
        if (! $this->validate(['name' => 'required|min_length[2]|max_length[100]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->service->update($unId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/countries')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/countries')->with('success', 'Country updated.');
    }

    public function delete(string $unId)
    {
        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/countries')->with('error', $e->getMessage());
        }
        return redirect()->to('admin/countries')->with('success', 'Country deleted.');
    }

    public function toggleActive(string $unId)
    {
        $country = $this->service->get($unId);
        if ($country) {
            $this->service->update($unId, array_merge($country, [
                'is_active' => $country['is_active'] ? 0 : 1,
            ]));
        }
        return redirect()->to('admin/countries')->with('success', 'Status toggled.');
    }
}
