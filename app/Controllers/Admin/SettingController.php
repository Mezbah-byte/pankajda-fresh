<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\SettingService;

class SettingController extends BaseController
{
    private SettingService $service;

    public function __construct()
    {
        $this->service = new SettingService();
    }

    public function index()
    {
        return view('admin/settings/index', [
            'title'    => 'Settings',
            'grouped'  => $this->service->grouped(),
        ]);
    }

    public function update()
    {
        $this->service->updateMany($this->request->getPost());
        return redirect()->to('admin/settings')->with('success', 'Settings saved.');
    }
}
