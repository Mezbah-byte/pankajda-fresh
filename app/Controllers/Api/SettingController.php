<?php

namespace App\Controllers\Api;

use App\Services\SettingService;

class SettingController extends BaseApiController
{
    private SettingService $service;

    public function __construct()
    {
        $this->service = new SettingService();
    }

    /**
     * GET /api/v1/settings
     * Return all settings grouped by area (general, finance, invoice, etc.).
     */
    public function index()
    {
        $grouped = $this->service->grouped();
        return $this->ok($grouped, 'Settings loaded');
    }

    /**
     * GET /api/v1/settings/{key}
     * Return a single setting value by its key.
     */
    public function show($key = null)
    {
        if (! $key) {
            return $this->failNotFound();
        }
        $value = $this->service->get($key);
        if ($value === null) {
            return $this->failNotFound('Setting not found.');
        }
        return $this->ok(['key' => $key, 'value' => $value]);
    }

    /**
     * PUT /api/v1/settings
     * Bulk update settings. Body: { "site.name": "Pankaj Da", "finance.currency": "BDT", ... }
     */
    public function update()
    {
        $body = $this->request->getJSON(true) ?? $this->request->getRawInput();
        if (empty($body) || ! is_array($body)) {
            return $this->failValidation(['_error' => 'Request body must be a non-empty JSON object of key/value pairs.']);
        }
        $this->service->updateMany($body);
        return $this->ok(null, 'Settings updated');
    }
}
