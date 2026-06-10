<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\SettingService;
use App\Repositories\CompanyTypeRepository;
use App\Repositories\CountryRepository;

class SettingController extends BaseController
{
    private SettingService $service;

    public function __construct()
    {
        $this->service = new SettingService();
    }

    public function index()
    {
        $typeRepo    = new CompanyTypeRepository();
        $countryRepo = new CountryRepository();

        return view('admin/settings/index', [
            'title'               => 'Settings',
            'grouped'             => $this->service->grouped(),
            'settings_flat'       => $this->service->flat(),
            'company_types_count' => count($typeRepo->all()),
            'countries_count'     => count($countryRepo->all()),
        ]);
    }

    public function update()
    {
        // PHP converts dots in $_POST keys to underscores (site.name → site_name).
        // Parse the raw body manually to preserve the original dotted keys.
        $data = [];
        foreach (explode('&', (string) $this->request->getBody()) as $pair) {
            if (strpos($pair, '=') === false) continue;
            $eqPos = strpos($pair, '=');
            $k = urldecode(substr($pair, 0, $eqPos));
            $v = urldecode(substr($pair, $eqPos + 1));
            if ($k !== '') {
                $data[$k] = $v;
            }
        }
        $this->service->updateMany($data);
        return redirect()->to('admin/settings')->with('success', 'Settings saved.');
    }
}
