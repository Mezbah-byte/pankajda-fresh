<?php

namespace App\Services;

use App\Repositories\SettingRepository;

/**
 * SettingService - read & write application settings grouped by area.
 *
 * Settings table is a flat key/value store. We group settings in the
 * UI (general/finance/invoice) for editing convenience.
 */
class SettingService extends BaseService
{
    private SettingRepository $repo;

    public function __construct(?SettingRepository $repo = null)
    {
        $this->repo = $repo ?? new SettingRepository();
    }

    public function grouped(): array
    {
        $all = $this->repo->all();
        $by  = [];
        foreach ($all as $row) {
            $by[$row['group']][] = $row;
        }
        return $by;
    }

    public function get(string $key, $default = null)
    {
        $v = $this->repo->get($key);
        return $v ?? $default;
    }

    public function updateMany(array $input): void
    {
        $this->transaction(function () use ($input) {
            foreach ($input as $key => $value) {
                // Only persist non-array values; ignore framework fields
                if (is_array($value)) continue;
                if (str_starts_with($key, '_'))   continue;  // _method, _token etc.
                if (in_array($key, ['csrf_token_name'], true)) continue;

                // Infer group from the key prefix (site., finance., invoice.)
                $group = strpos($key, '.') !== false ? explode('.', $key, 2)[0] : 'general';
                $this->repo->set($key, $value, 'string', $group);
            }
            $this->audit('setting.updated', 'setting', null, ['count' => count($input)]);
        });
    }
}
