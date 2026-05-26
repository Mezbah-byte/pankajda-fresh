<?php

namespace App\Repositories;

use App\Models\SettingModel;

class SettingRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new SettingModel();
    }

    public function all(): array
    {
        return $this->model->orderBy('group', 'ASC')->orderBy('key', 'ASC')->findAll();
    }

    public function get(string $key): ?string
    {
        $row = $this->model->where('key', $key)->first();
        return $row['value'] ?? null;
    }

    public function set(string $key, $value, string $type = 'string', string $group = 'general'): void
    {
        $existing = $this->model->where('key', $key)->first();
        if ($existing) {
            $this->model->update($existing['id'], ['value' => (string) $value, 'type' => $type, 'group' => $group]);
        } else {
            $this->model->insert([
                'key'   => $key,
                'value' => (string) $value,
                'type'  => $type,
                'group' => $group,
            ]);
        }
    }
}
