<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\DashboardService;

class DashboardController extends BaseController
{
    private DashboardService $service;

    public function __construct()
    {
        $this->service = new DashboardService();
    }

    public function index()
    {
        $data = $this->service->summary();
        return view('admin/dashboard', array_merge(['title' => 'Dashboard'], $data));
    }
}
