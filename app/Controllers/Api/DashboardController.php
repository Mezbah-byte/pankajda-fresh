<?php

namespace App\Controllers\Api;

use App\Services\DashboardService;

class DashboardController extends BaseApiController
{
    public function stats()
    {
        $service = new DashboardService();
        return $this->ok($service->summary());
    }
}
